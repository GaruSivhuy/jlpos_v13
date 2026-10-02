<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class CustomerPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:customer');
    }

    public function view(AuthUser $authUser, Customer $customer): bool
    {
        return $authUser->can('view:customer');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:customer');
    }

    public function update(AuthUser $authUser, Customer $customer): bool
    {
        return $authUser->can('update:customer');
    }

    public function delete(AuthUser $authUser, Customer $customer): bool
    {
        return $authUser->can('delete:customer');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:customer');
    }

    public function restore(AuthUser $authUser, Customer $customer): bool
    {
        return $authUser->can('restore:customer');
    }
}
