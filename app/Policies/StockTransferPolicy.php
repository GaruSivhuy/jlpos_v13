<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\StockTransfer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class StockTransferPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:stock_transfer');
    }

    public function view(AuthUser $authUser, StockTransfer $stockTransfer): bool
    {
        return $authUser->can('view:stock_transfer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:stock_transfer');
    }

    public function update(AuthUser $authUser, StockTransfer $stockTransfer): bool
    {
        return $authUser->can('update:stock_transfer');
    }

    public function delete(AuthUser $authUser, StockTransfer $stockTransfer): bool
    {
        return $authUser->can('delete:stock_transfer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:stock_transfer');
    }

    public function restore(AuthUser $authUser, StockTransfer $stockTransfer): bool
    {
        return $authUser->can('restore:stock_transfer');
    }
}
