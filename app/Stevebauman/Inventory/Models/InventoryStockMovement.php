<?php

namespace App\Stevebauman\Inventory\Models;

use App\Stevebauman\Inventory\Traits\InventoryStockMovementTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventoryStockMovement.
 */
class InventoryStockMovement extends BaseModel
{
    use InventoryStockMovementTrait;

    protected $table = 'inventory_stock_movements';

    protected $fillable = [
        'stock_id',
        'user_id',
        'before',
        'after',
        'cost',
        'reason',
    ];

    /**
     * The belongsTo stock relationship.
     *
     * @return BelongsTo
     */
    public function stock()
    {
        return $this->belongsTo('App\Stevebauman\Inventory\Models\InventoryStock', 'stock_id', 'id');
    }

    /**
     * The belongsTo user relationship.
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
