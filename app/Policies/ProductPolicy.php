<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:product');
    }

    public function view(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('view:product');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:product');
    }

    public function update(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('update:product');
    }

    public function delete(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('delete:product');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:product');
    }

    public function restore(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('restore:product');
    }
}
