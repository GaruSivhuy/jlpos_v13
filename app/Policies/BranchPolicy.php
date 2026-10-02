<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Branch;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class BranchPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:branch');
    }

    public function view(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('view:branch');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:branch');
    }

    public function update(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('update:branch');
    }

    public function delete(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('delete:branch');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:branch');
    }

    public function restore(AuthUser $authUser, Branch $branch): bool
    {
        return $authUser->can('restore:branch');
    }
}
