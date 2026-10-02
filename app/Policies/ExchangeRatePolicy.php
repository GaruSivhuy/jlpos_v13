<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ExchangeRate;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ExchangeRatePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:exchange_rate');
    }

    public function view(AuthUser $authUser, ExchangeRate $exchangeRate): bool
    {
        return $authUser->can('view:exchange_rate');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:exchange_rate');
    }

    public function update(AuthUser $authUser, ExchangeRate $exchangeRate): bool
    {
        return $authUser->can('update:exchange_rate');
    }

    public function delete(AuthUser $authUser, ExchangeRate $exchangeRate): bool
    {
        return $authUser->can('delete:exchange_rate');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:exchange_rate');
    }

    public function restore(AuthUser $authUser, ExchangeRate $exchangeRate): bool
    {
        return $authUser->can('restore:exchange_rate');
    }
}
