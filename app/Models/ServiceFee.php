<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class ServiceFee extends Model
{
    use Hashidable;
    use LogsActivity;
    use SoftDeletes;

    public const TYPE_USD = 1;

    public const TYPE_RIEL = 2;

    protected $table = 'service_fees';

    protected $fillable = [
        'amount',
        'service_type',
        'service_fees',
        'payment_type',
        'user_id',
        'user_update',
        'branch_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'float',
            'service_type' => 'integer',
            'service_fees' => 'float',
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

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_type');
    }

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('branch_id', $branch);
        }
    }
}
