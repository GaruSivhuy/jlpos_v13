<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class StockTransfer extends Model
{
    use Hashidable;
    use LogsActivity;

    protected $table = 'stock_transfer';

    protected $fillable = [
        'st_code',
        'transfer_date',
        'from_location',
        'to_location',
        'remark',
        'user_id',
        'user_updated',
        'status',
        'branch_id',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'st_code',
                'transfer_date',
                'from_location',
                'to_location',
                'remark',
                'user_id',
                'user_updated',
                'status',
                'branch_id',
            ]);
    }

    public function stockTransferDetails()
    {
        return $this->hasMany('App\Models\StockTransferDetail', 'stock_transfer_id');
    }

    public function fromLocation()
    {
        return $this->belongsTo('App\Models\Location', 'from_location');
    }

    public function toLocation()
    {
        return $this->belongsTo('App\Models\Location', 'to_location');
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

            return $query->whereIn('stock_transfer.branch_id', $branch);
        }
    }
}
