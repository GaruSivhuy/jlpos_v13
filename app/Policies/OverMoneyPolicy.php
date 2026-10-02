<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\OverMoney;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class OverMoneyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:over_money');
    }

    public function view(AuthUser $authUser, OverMoney $overMoney): bool
    {
        return $authUser->can('view:over_money');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:over_money');
    }

    public function update(AuthUser $authUser, OverMoney $overMoney): bool
    {
        return $authUser->can('update:over_money');
    }

    public function delete(AuthUser $authUser, OverMoney $overMoney): bool
    {
        return $authUser->can('delete:over_money');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:over_money');
    }

    public function restore(AuthUser $authUser, OverMoney $overMoney): bool
    {
        return $authUser->can('restore:over_money');
    }
}
