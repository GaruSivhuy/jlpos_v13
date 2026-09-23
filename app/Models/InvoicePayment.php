<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoicePayment extends Model
{
    use SoftDeletes;

    protected $table = 'invoice_payments';

    protected $fillable = [
        'user_id',
        'invoice_id',
        'full_amount',
        'paid_amount',
        'paid_at',
        'note',
        'payment_gateway',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_gateway');
    }
}
