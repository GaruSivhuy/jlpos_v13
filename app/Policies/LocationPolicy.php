<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Location;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class LocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:location');
    }

    public function view(AuthUser $authUser, Location $location): bool
    {
        return $authUser->can('view:location');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:location');
    }

    public function update(AuthUser $authUser, Location $location): bool
    {
        return $authUser->can('update:location');
    }

    public function delete(AuthUser $authUser, Location $location): bool
    {
        return $authUser->can('delete:location');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:location');
    }

    public function restore(AuthUser $authUser, Location $location): bool
    {
        return $authUser->can('restore:location');
    }
}
