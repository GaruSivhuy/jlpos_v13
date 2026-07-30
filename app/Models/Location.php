<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    protected $fillable = [
        'name',
        'name_kh',
        'branch_id',
        'user_id',
        'user_updated',
        'is_retail',
    ];

    protected $scoped = ['belongs_to'];

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function user_create()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function user_update()
    {
        return $this->belongsTo('App\Models\User', 'user_updated');
    }

    public function scopeBranch($query){
        if(auth()->user()->is_admin != 1){
            $branch = auth()->user()->branch->pluck('id');
            return $query->whereIn('branch_id', $branch);
        }
    }

}
