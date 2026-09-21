<?php

namespace App\Models;

use App\Stevebauman\Inventory\Traits\SupplierTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Supplier extends Model
{
    use LogsActivity, SupplierTrait;

    protected $table = 'suppliers';

    protected $fillable = [
        'name_kh',
        'name',
        'contact_phone',
        'address',
        'branch_id',
        'user_id',
        'user_updated',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'name_kh',
                'name',
                'contact_phone',
                'address',
                'branch_id',
                'user_id',
                'user_updated',
            ]);
    }

    protected static $logOnlyDirty = true;

    public function items()
    {
        return $this->belongsToMany('Inventory', 'inventory_suppliers', 'supplier_id')->withTimestamps();
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

            return $query->whereIn('branch_id', $branch);
        }
    }
}
