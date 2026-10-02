<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:supplier');
    }

    public function view(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('view:supplier');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:supplier');
    }

    public function update(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('update:supplier');
    }

    public function delete(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('delete:supplier');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:supplier');
    }

    public function restore(AuthUser $authUser, Supplier $supplier): bool
    {
        return $authUser->can('restore:supplier');
    }
}
