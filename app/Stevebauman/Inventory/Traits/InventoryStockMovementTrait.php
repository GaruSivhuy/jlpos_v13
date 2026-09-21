<?php

namespace App\Stevebauman\Inventory\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trait InventoryStockMovementTrait.
 */
trait InventoryStockMovementTrait
{
    use DatabaseTransactionTrait;
    use UserIdentificationTrait;

    /**
     * The belongsTo stock relationship.
     *
     * @return BelongsTo
     */
    abstract public function stock();

    /**
     * Overrides the models boot function to set
     * the user ID automatically to every new record.
     */
    public static function bootInventoryStockMovementTrait()
    {
        static::creating(function (Model $record) {
            $record->user_id = static::getCurrentUserId();
        });
    }

    /**
     * Rolls back the current movement.
     *
     * @param  bool  $recursive
     * @return mixed
     */
    public function rollback($recursive = false)
    {
        $stock = $this->stock;

        return $stock->rollback($this, $recursive);
    }
}
