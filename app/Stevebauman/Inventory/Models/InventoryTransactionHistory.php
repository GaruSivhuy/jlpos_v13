<?php

namespace App\Stevebauman\Inventory\Models;

use App\Stevebauman\Inventory\Traits\InventoryTransactionHistoryTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class InventoryTransactionPeriod.
 */
class InventoryTransactionHistory extends BaseModel
{
    use InventoryTransactionHistoryTrait;

    protected $table = 'inventory_transaction_histories';

    protected $fillable = [
        'user_id',
        'transaction_id',
        'state_before',
        'state_after',
        'quantity_before',
        'quantity_after',
    ];

    /**
     * The belongsTo transaction relationship.
     *
     * @return BelongsTo
     */
    public function transaction()
    {
        return $this->belongsTo('App\Stevebauman\Inventory\Models\InventoryTransaction', 'transaction_id', 'id');
    }
}
