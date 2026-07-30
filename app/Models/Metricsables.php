<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Metricsables extends Model
{
    use LogsActivity;
    
    protected $table = 'metricsables';

    protected $fillable = [
        'metric_id',
        'metricsables_type',
        'metricsables_id',
        'price',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([  
                'metric_id',
                'metricsables_type',
                'metricsables_id',
                'price',
                'user_id',
                'created_at',
                'updated_at',
            ]);
    }

    public function inventory()
    {
        return $this->belongsTo("App\Models\Product", 'metricsables_id');
    }

    public function metric()
    {
        return $this->belongsTo("App\Models\Metric", 'metric_id');
    }

    public function user_updated()
    {
        return $this->belongsTo("App\Models\User", 'user_id');
    }
}
