<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Remark;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class RemarkPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:remark');
    }

    public function view(AuthUser $authUser, Remark $remark): bool
    {
        return $authUser->can('view:remark');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:remark');
    }

    public function update(AuthUser $authUser, Remark $remark): bool
    {
        return $authUser->can('update:remark');
    }

    public function delete(AuthUser $authUser, Remark $remark): bool
    {
        return $authUser->can('delete:remark');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:remark');
    }

    public function restore(AuthUser $authUser, Remark $remark): bool
    {
        return $authUser->can('restore:remark');
    }
}
