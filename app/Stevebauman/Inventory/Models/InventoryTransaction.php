<?php

namespace App\Stevebauman\Inventory\Models;

use App\Stevebauman\Inventory\Interfaces\StateableInterface;
use App\Stevebauman\Inventory\Traits\InventoryTransactionTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class InventoryTransaction.
 */
class InventoryTransaction extends BaseModel implements StateableInterface
{
    use InventoryTransactionTrait;

    protected $table = 'inventory_transactions';

    protected $fillable = [
        'user_id',
        'stock_id',
        'name',
        'state',
        'quantity',
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
     * The hasMany histories relationship.
     *
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany('App\Stevebauman\Inventory\Models\InventoryTransactionHistory', 'transaction_id', 'id');
    }
}
