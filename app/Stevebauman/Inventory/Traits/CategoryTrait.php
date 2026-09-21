<?php

namespace App\Stevebauman\Inventory\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait CategoryTrait.
 */
trait CategoryTrait
{
    /**
     * The hasMany inventories relationship.
     *
     * @return HasMany
     */
    abstract public function inventories();
}
