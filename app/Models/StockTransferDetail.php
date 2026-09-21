<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Support\LogOptions;

class StockTransferDetail extends Model
{
    protected $table = 'stock_transfer_detail';

    protected $fillable = [
        'stock_transfer_id',
        'inventory_id',
        'metric_id',
        'uom_id',
        'qty',
        'remark',
        'transfer_metric_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'stock_transfer_id',
                'inventory_id',
                'metric_id',
                'uom_id',
                'qty',
                'remark',
                'transfer_metric_id',
            ]);
    }

    public function metric()
    {
        return $this->belongsTo('App\Models\Metric', 'metric_id');
    }

    public function transferMetric()
    {
        return $this->belongsTo('App\Models\Metric', 'transfer_metric_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'inventory_id');
    }

    public function stockTransfer()
    {
        return $this->belongsTo('App\Models\StockTransfer', 'stock_transfer_id');
    }
}
