<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ExchangeMoney extends Model
{
    use Hashidable;
    use LogsActivity;
    use SoftDeletes;

    public const USD_TO_KHR = 1;

    public const KHR_TO_USD = 2;

    protected $table = 'exchange_money';

    protected $fillable = [
        'amount_exchange',
        'rate_exchange',
        'exchange_date',
        'exchange_type',
        'branch_id',
        'exchange_id',
        'user_id',
        'user_update',
        'total_amount',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exchange_date' => 'date',
            'amount_exchange' => 'float',
            'rate_exchange' => 'float',
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

    public function exchange()
    {
        return $this->belongsTo(ExchangeRate::class, 'exchange_id');
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
