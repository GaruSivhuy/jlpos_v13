<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Purchase;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PurchasePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:purchase');
    }

    public function view(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('view:purchase');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:purchase');
    }

    public function update(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('update:purchase');
    }

    public function delete(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('delete:purchase');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:purchase');
    }

    public function restore(AuthUser $authUser, Purchase $purchase): bool
    {
        return $authUser->can('restore:purchase');
    }
}
