<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ChangeProduct extends Model
{
    use Hashidable;
    use LogsActivity;

    public const TYPE_ITEM = 1;

    public const TYPE_CASH = 2;

    public const TYPE_EARRING = 3;

    protected $table = 'change_product';

    protected $fillable = [
        'change_description',
        'change_product_type',
        'change_product_date',
        'location_id',
        'inventory_id',
        'metric_id',
        'qty',
        'branch_id',
        'amount',
        'total_amount',
        'submit_status',
        'user_id',
        'user_update',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'change_product_date' => 'date',
            'amount' => 'float',
            'total_amount' => 'float',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable();
    }

    public function user_create()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'user_update');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function inventories()
    {
        return $this->belongsTo(Product::class, 'inventory_id');
    }

    public function metrics()
    {
        return $this->belongsTo(Metric::class, 'metric_id');
    }

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('branch_id', $branch);
        }
    }
}
