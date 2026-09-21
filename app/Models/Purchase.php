<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Purchase extends Model
{
    use Hashidable;
    use LogsActivity;
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $table = 'purchase';

    protected $fillable = [
        'po_code',
        'supplier_id',
        'location_id',
        'po_date',
        'delivery_date',
        'cost',
        'discount',
        'status',
        'remark',
        'user_id',
        'user_updated',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'po_code',
                'supplier_id',
                'location_id',
                'po_date',
                'delivery_date',
                'cost',
                'discount',
                'status',
                'remark',
                'user_id',
                'user_updated',
                'branch_id',
            ]);
    }

    public function items()
    {
        return $this->belongsToMany('App\Models\Product', 'purchase_detail', 'purchase_id', 'inventory_id')->withPivot('metric_id', 'quantity', 'price', 'id', 'purchase_id')->using('App\Models\PurchaseDetail');
    }

    public function purchaseDetails()
    {
        return $this->hasMany('App\Models\PurchaseDetail', 'purchase_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id');
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

            return $query->whereIn('purchase.branch_id', $branch);
        }
    }
}
