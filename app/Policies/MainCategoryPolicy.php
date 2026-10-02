<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\MainCategory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MainCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:main_category');
    }

    public function view(AuthUser $authUser, MainCategory $mainCategory): bool
    {
        return $authUser->can('view:main_category');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:main_category');
    }

    public function update(AuthUser $authUser, MainCategory $mainCategory): bool
    {
        return $authUser->can('update:main_category');
    }

    public function delete(AuthUser $authUser, MainCategory $mainCategory): bool
    {
        return $authUser->can('delete:main_category');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:main_category');
    }

    public function restore(AuthUser $authUser, MainCategory $mainCategory): bool
    {
        return $authUser->can('restore:main_category');
    }
}
