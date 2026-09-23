<?php

namespace App\Models;

use App\Stevebauman\Inventory\Models\InventoryStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceItem extends Model
{
    use SoftDeletes;

    public const UNPAID = 1;

    public const PARTIAL = 2;

    public const PAID = 3;

    protected $table = 'invoice_items';

    protected $fillable = [
        'invoice_id',
        'user_id',
        'user_updated',
        'qty',
        'discount',
        'amount',
        'inventory_id',
        'price',
        'discount_type',
        'status',
        'paid_amount',
        'inventory_stock_id',
        'metric_id',
        'quantity',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'inventory_id');
    }

    public function metric()
    {
        return $this->belongsTo(Metric::class, 'metric_id');
    }

    public function stock()
    {
        return $this->belongsTo(InventoryStock::class, 'inventory_stock_id');
    }

    /**
     * Put the sold quantity back into the stock the item was taken from.
     */
    public function returnToStock(string $reason): void
    {
        if ($this->stock === null || $this->metric === null) {
            return;
        }

        $this->stock->put($this->qty * $this->metric->qty, $reason);
    }

    /**
     * Item amount for a quantity and unit price after applying its own discount.
     */
    public static function calculateAmount(float|int $qty, float|int $price, float|int $discount, ?string $discountType): float
    {
        $gross = $qty * $price;
        $discountAmount = $discountType === 'R' ? $gross * ($discount / 100) : $discount;

        return $gross - $discountAmount;
    }
}
