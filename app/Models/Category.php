<?php

namespace App\Models;

use App\Models\Traits\CategoryTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Activitylog\Models\Concerns\LogsActivity;

class Category extends Model
{
    use CategoryTrait;
    use SoftDeletes;
    use LogsActivity;


    protected $table = 'categories';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'main_cat_id',
        'branch_id',
        'name',
        'name_kh',
        'user_id',
        'user_updated',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([ 
                'main_cat_id',
                'branch_id',
                'name',
                'name_kh',
                'user_id',
                'user_updated',
            ]);
    }

    protected $scoped = ['belongs_to'];

    public function user_create()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function user_update()
    {
        return $this->belongsTo('App\Models\User', 'user_updated');
    }

    public function main_category()
    {
        return $this->belongsTo('App\Models\MainCategory', 'main_cat_id');
    }

    /**
     * The hasMany inventories relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany('App\Models\Product', 'category_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id');
    }

    public function scopeBranch($query){
        if(auth()->user()->is_admin != 1){
            $branch = auth()->user()->branch->pluck('id');
            return $query->whereIn('branch_id', $branch);
        }
    }
}
