<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;

class InventoryAdjustmentDetail extends Model
{
    protected $table = 'inventory_adjustment_detail';

    protected $fillable = [
        'adjustment_id',
        'inventory_id',
        'metric_id',
        'uom_id',
        'adjustment_type',
        'qty',
        'remark',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'adjustment_id',
                'inventory_id',
                'metric_id',
                'uom_id',
                'adjustment_type',
                'qty',
                'remark',
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

    public function inventoryAdjustment()
    {
        return $this->belongsTo('App\Models\InventoryAdjustment', 'adjustment_id');
    }
}
