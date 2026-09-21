<?php

namespace App\Stevebauman\Inventory\Models;

use App\Stevebauman\Inventory\Traits\AssemblyTrait;
use App\Stevebauman\Inventory\Traits\InventoryTrait;
use App\Stevebauman\Inventory\Traits\InventoryVariantTrait;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Inventory.
 */
class Inventory extends BaseModel
{
    use AssemblyTrait;
    use InventoryTrait;
    use InventoryVariantTrait;

    protected $table = 'inventories';

    protected $fillable = [
        'user_id',
        'category_id',
        'metric_id',
        'name',
        'description',
    ];

    /**
     * The hasOne category relationship.
     *
     * @return HasOne
     */
    public function category()
    {
        return $this->hasOne('App\Stevebauman\Inventory\Models\Category', 'id', 'category_id');
    }

    /**
     * The hasOne metric relationship.
     *
     * @return HasOne
     */
    public function metric()
    {
        return $this->hasOne('App\Stevebauman\Inventory\Models\Metric', 'id', 'metric_id');
    }

    /**
     * The hasOne sku relationship.
     *
     * @return HasOne
     */
    public function sku()
    {
        return $this->hasOne('App\Stevebauman\Inventory\Models\InventorySku', 'inventory_id', 'id');
    }

    /**
     * The hasMany stocks relationship.
     *
     * @return HasMany
     */
    public function stocks()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\InventoryStock', 'inventory_id', 'id');
    }

    /**
     * The belongsToMany suppliers relationship.
     *
     * @return BelongsToMany
     */
    public function suppliers()
    {
        return $this->belongsToMany('App\Stevebauman\Inventory\Models\Supplier', 'inventory_suppliers', 'inventory_id')->withTimestamps();
    }

    /**
     * The belongsToMany assemblies relationship.
     *
     * @return BelongsToMany
     */
    public function assemblies()
    {
        return $this->belongsToMany($this, 'inventory_assemblies', 'inventory_id', 'part_id')->withPivot(['quantity'])->withTimestamps();
    }
}
