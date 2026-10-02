<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\InventoryAdjustment;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class InventoryAdjustmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:inventory_adjustment');
    }

    public function view(AuthUser $authUser, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $authUser->can('view:inventory_adjustment');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:inventory_adjustment');
    }

    public function update(AuthUser $authUser, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $authUser->can('update:inventory_adjustment');
    }

    public function delete(AuthUser $authUser, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $authUser->can('delete:inventory_adjustment');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:inventory_adjustment');
    }

    public function restore(AuthUser $authUser, InventoryAdjustment $inventoryAdjustment): bool
    {
        return $authUser->can('restore:inventory_adjustment');
    }
}
