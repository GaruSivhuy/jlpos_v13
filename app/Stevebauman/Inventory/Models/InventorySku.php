<?php

namespace App\Stevebauman\Inventory\Models;

use App\Stevebauman\Inventory\Traits\InventorySkuTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventorySku.
 */
class InventorySku extends BaseModel
{
    use InventorySkuTrait;

    protected $table = 'inventory_skus';

    protected $fillable = [
        'inventory_id',
        'code',
    ];

    /**
     * The belongsTo item trait.
     *
     * @return BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('App\Stevebauman\Inventory\Models\Inventory', 'inventory_id', 'id');
    }
}
