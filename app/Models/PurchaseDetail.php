<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;

class PurchaseDetail extends Model
{
    protected $table = 'purchase_detail';

    protected $fillable = [
        'purchase_id',
        'inventory_id',
        'metric_id',
        'uom_id',
        'quantity',
        'price',
        'produce_date',
        'expired_date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'purchase_id',
                'inventory_id',
                'metric_id',
                'uom_id',
                'quantity',
                'price',
                'produce_date',
                'expired_date',
            ]);
    }

    public function metric()
    {
        return $this->belongsTo('App\Models\Metric', 'metric_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'inventory_id');
    }

    public function purchase()
    {
        return $this->belongsTo('App\Models\Purchase', 'purchase_id');
    }
}
