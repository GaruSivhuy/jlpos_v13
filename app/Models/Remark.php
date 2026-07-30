<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Remark extends Model
{
    use SoftDeletes;

    protected $table = 'remarks';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name_kh',
        'branch_id',
        'user_id',
        'user_updated',
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
        return $this->morphedByMany("App\Models\Product", 'remarksables');
    }

    public function scopeBranch($query){
        if(auth()->user()->is_admin != 1){
            $branch = auth()->user()->branch->pluck('id');
            return $query->whereIn('branch_id', $branch);
        }
    }


    public function getDescriptionForEvent(string $eventName): string
    {
        return "{$eventName} Remarks";
    }
}
