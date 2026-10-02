<?php

declare(strict_types=1);

namespace App\Stevebauman\Inventory\Policies;

use App\Stevebauman\Inventory\Models\InventoryStock;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class InventoryStockPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:inventory_stock');
    }

    public function view(AuthUser $authUser, InventoryStock $inventoryStock): bool
    {
        return $authUser->can('view:inventory_stock');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:inventory_stock');
    }

    public function update(AuthUser $authUser, InventoryStock $inventoryStock): bool
    {
        return $authUser->can('update:inventory_stock');
    }

    public function delete(AuthUser $authUser, InventoryStock $inventoryStock): bool
    {
        return $authUser->can('delete:inventory_stock');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:inventory_stock');
    }

    public function restore(AuthUser $authUser, InventoryStock $inventoryStock): bool
    {
        return $authUser->can('restore:inventory_stock');
    }
}
