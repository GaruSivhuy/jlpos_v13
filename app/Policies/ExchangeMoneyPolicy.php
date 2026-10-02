<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExchangeMoney;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ExchangeMoneyPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:exchange_money');
    }

    public function view(AuthUser $authUser, ExchangeMoney $exchangeMoney): bool
    {
        return $authUser->can('view:exchange_money');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:exchange_money');
    }

    public function update(AuthUser $authUser, ExchangeMoney $exchangeMoney): bool
    {
        return $authUser->can('update:exchange_money');
    }

    public function delete(AuthUser $authUser, ExchangeMoney $exchangeMoney): bool
    {
        return $authUser->can('delete:exchange_money');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:exchange_money');
    }

    public function restore(AuthUser $authUser, ExchangeMoney $exchangeMoney): bool
    {
        return $authUser->can('restore:exchange_money');
    }
}
