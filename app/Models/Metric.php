<?php

namespace App\Models;

use App\Http\Traits\Hashidable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Metric extends Model
{
    use Hashidable;
    use SoftDeletes;

    protected $table = 'metrics';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'name_kh',
        'qty',
        'branch_id',
        'user_id',
        'user_updated',
        'name_show',
    ];

    public function user_create()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function user_update()
    {
        return $this->belongsTo('App\Models\User', 'user_updated');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function inventory()
    {
        return $this->morphedByMany("App\Models\Product", 'metricsables');
    }

    public function scopeBranch($query)
    {
        if (auth()->user()->is_admin != 1) {
            $branch = auth()->user()->branch->pluck('id');

            return $query->whereIn('branch_id', $branch);
        }
    }
}
