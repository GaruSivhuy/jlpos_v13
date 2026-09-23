<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class OverMoney extends Model
{
    use Hashidable;
    use LogsActivity;
    use SoftDeletes;

    public const USD = 1;

    public const KHR = 2;

    protected $table = 'over_money';

    protected $fillable = [
        'over_amount',
        'over_money_type',
        'over_money_date',
        'branch_id',
        'user_id',
        'user_update',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'over_money_date' => 'date',
            'over_amount' => 'float',
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

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('branch_id', $branch);
        }
    }
}
