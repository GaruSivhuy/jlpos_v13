<?php

namespace App\Stevebauman\Inventory\Models;

use App\Stevebauman\Inventory\Traits\InventoryStockTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class InventoryStock.
 */
class InventoryStock extends BaseModel
{
    use InventoryStockTrait;

    protected $table = 'inventory_stocks';

    protected $fillable = [
        'inventory_id',
        'location_id',
        'metric_id',
        'quantity',
        'aisle',
        'row',
        'bin',
    ];

    /**
     * The belongsTo inventory item relationship.
     *
     * @return BelongsTo
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Product', 'inventory_id', 'id');
    }

    /**
     * The hasMany movements relationship.
     *
     * @return HasMany
     */
    public function movements()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\InventoryStockMovement', 'stock_id', 'id');
    }

    /**
     * The hasMany transactions relationship.
     *
     * @return HasMany
     */
    public function transactions()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\InventoryTransaction', 'stock_id', 'id');
    }

    /**
     * The hasOne location relationship.
     *
     * @return HasOne
     */
    public function location()
    {
        return $this->hasOne('App\Models\Location', 'id', 'location_id');
    }

    /**
     * The hasOne metric relationship.
     *
     * @return HasOne
     */
    public function metric()
    {
        return $this->hasOne('App\Models\Metric', 'id', 'metric_id');
    }
}
