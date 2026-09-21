<?php

namespace App\Stevebauman\Inventory\Models;

use App\Baum\Node;
use App\Stevebauman\Inventory\Traits\CategoryTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Category.
 */
class Category extends Node
{
    use CategoryTrait;

    protected $table = 'categories';

    protected $fillable = [
        'name',
    ];

    protected $scoped = ['belongs_to'];

    /**
     * The hasMany inventories relationship.
     *
     * @return HasMany
     */
    public function inventories()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\Inventory', 'category_id', 'id');
    }
}
