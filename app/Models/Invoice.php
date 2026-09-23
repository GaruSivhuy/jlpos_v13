<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Invoice extends Model
{
    use Hashidable;
    use LogsActivity;
    use SoftDeletes;

    public const DRAFT = 0;

    public const UNPAID = 1;

    public const PARTIAL = 2;

    public const PAID = 3;

    public const CANCEL = 4;

    protected $table = 'invoices';

    protected $fillable = [
        'user_id',
        'user_updated',
        'customer_id',
        'invoiced_at',
        'currency',
        'exchange_rate_id',
        'discount',
        'total',
        'status',
        'note',
        'discount_type',
        'paid_amount',
        'branch_id',
        'total_return',
        'total_return_riel',
        'paid_amount_usd',
        'paid_amount_riel',
        'payment_date',
        'payment_gateway',
        'order_number',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'invoiced_at' => 'datetime',
            'payment_date' => 'date',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'user_id',
                'user_updated',
                'customer_id',
                'invoiced_at',
                'currency',
                'exchange_rate_id',
                'discount',
                'total',
                'status',
                'note',
                'discount_type',
                'paid_amount',
                'branch_id',
                'total_return',
                'total_return_riel',
                'paid_amount_usd',
                'paid_amount_riel',
                'payment_date',
                'payment_gateway',
            ])
            ->logOnlyDirty();
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id');
    }

    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'user_updated');
    }

    public function exchange()
    {
        return $this->belongsTo(ExchangeRate::class, 'exchange_rate_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway');
    }

    public function getInvoiceCodeAttribute(): string
    {
        return 'INV-'.str_pad($this->id, env('ID_PAD_LENGTH', 8), '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate the invoice total from its items minus the invoice level discount.
     */
    public function updateTotal(): void
    {
        $this->total = (float) $this->invoiceItems()->sum('amount') - (float) $this->discount;
        $this->save();
    }

    /**
     * Cancel the invoice, returning the stock of every item and removing its items and payments.
     */
    public function cancel(): void
    {
        DB::transaction(function () {
            $this->invoiceItems()->with(['stock', 'metric'])->get()
                ->each(fn (InvoiceItem $item) => $item->returnToStock('Return stock by cancel invoice '.$this->invoice_code));

            $this->invoiceItems()->delete();
            $this->invoicePayments()->delete();

            $this->update([
                'status' => self::CANCEL,
                'user_updated' => auth()->id(),
            ]);
        });
    }

    /**
     * Record a payment received in USD and/or Riel and allocate it over the invoice items.
     */
    public function recordPayment(float $receivedUsd, float $receivedRiel, int $paymentGatewayId): void
    {
        DB::transaction(function () use ($receivedUsd, $receivedRiel, $paymentGatewayId) {
            $this->updateTotal();

            $exchangeRate = (float) $this->exchange?->exchange_rate;
            $received = round($receivedUsd + ($exchangeRate > 0 ? $receivedRiel / $exchangeRate : 0), 2);
            $total = round((float) $this->total, 2);

            $remaining = $received;

            foreach ($this->invoiceItems as $item) {
                $itemAmount = round((float) $item->amount, 2);
                $paidAmount = min($itemAmount, $remaining);

                $item->update([
                    'paid_amount' => $paidAmount,
                    'status' => $remaining >= $itemAmount ? InvoiceItem::PAID : InvoiceItem::PARTIAL,
                ]);

                $remaining -= $paidAmount;
            }

            $isFullyPaid = $received >= $total;
            $paidAmount = $isFullyPaid ? $total : $received;
            $change = $isFullyPaid ? round($received - $total, 2) : 0;

            $this->invoicePayments()->create([
                'user_id' => auth()->id(),
                'full_amount' => $received,
                'paid_amount' => $paidAmount,
                'paid_at' => now(),
                'payment_gateway' => $paymentGatewayId,
                'note' => 'Make payment from sale#'.$this->id,
            ]);

            $this->fill([
                'paid_amount' => $paidAmount,
                'status' => $isFullyPaid ? self::PAID : self::PARTIAL,
                'total_return' => $change,
                'total_return_riel' => $change * $exchangeRate,
                'paid_amount_usd' => round($receivedUsd, 2),
                'paid_amount_riel' => round($receivedRiel),
                'user_updated' => auth()->id(),
            ]);

            if ($isFullyPaid) {
                $this->payment_date = now();
                $this->payment_gateway = $paymentGatewayId;
            }

            $this->save();
        });
    }

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('invoices.branch_id', $branch);
        }
    }
}
