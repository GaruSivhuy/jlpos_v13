<?php

namespace App\Stevebauman\Inventory\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait InventorySkuTrait.
 */
trait InventorySkuTrait
{
    /**
     * The belongsTo inventory item relationship.
     *
     * @return BelongsTo
     */
    abstract public function item();
}
