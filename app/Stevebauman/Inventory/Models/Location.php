<?php

namespace App\Stevebauman\Inventory\Models;

use App\Baum\Node;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Location.
 */
class Location extends Node
{
    protected $table = 'locations';

    protected $fillable = [
        'name',
    ];

    protected $scoped = ['belongs_to'];

    /**
     * The hasMany stocks relationship.
     *
     * @return HasMany
     */
    public function stocks()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\InventoryStock', 'location_id', 'id');
    }
}
