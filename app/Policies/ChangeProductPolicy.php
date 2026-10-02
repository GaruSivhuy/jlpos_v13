<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChangeProduct;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ChangeProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:change_product');
    }

    public function view(AuthUser $authUser, ChangeProduct $changeProduct): bool
    {
        return $authUser->can('view:change_product');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:change_product');
    }

    public function update(AuthUser $authUser, ChangeProduct $changeProduct): bool
    {
        return $authUser->can('update:change_product');
    }

    public function delete(AuthUser $authUser, ChangeProduct $changeProduct): bool
    {
        return $authUser->can('delete:change_product');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:change_product');
    }

    public function restore(AuthUser $authUser, ChangeProduct $changeProduct): bool
    {
        return $authUser->can('restore:change_product');
    }
}
