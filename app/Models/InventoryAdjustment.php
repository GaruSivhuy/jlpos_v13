<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class InventoryAdjustment extends Model
{
    use LogsActivity;

    protected $table = 'inventory_adjustment';

    protected $fillable = [
        'adjustment_code',
        'location_id',
        'note',
        'user_id',
        'user_updated',
        'status',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'adjustment_code',
                'location_id',
                'note',
                'user_id',
                'user_updated',
                'status',
                'branch_id',
            ]);
    }

    public function inventoryAdjustmentDetails()
    {
        return $this->hasMany('App\Models\InventoryAdjustmentDetail', 'adjustment_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function updatedBy()
    {
        return $this->belongsTo('App\Models\User', 'user_updated');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('inventory_adjustment.branch_id', $branch);
        }
    }
}
