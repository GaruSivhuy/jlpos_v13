<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ServiceFee;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class ServiceFeePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:service_fee');
    }

    public function view(AuthUser $authUser, ServiceFee $serviceFee): bool
    {
        return $authUser->can('view:service_fee');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:service_fee');
    }

    public function update(AuthUser $authUser, ServiceFee $serviceFee): bool
    {
        return $authUser->can('update:service_fee');
    }

    public function delete(AuthUser $authUser, ServiceFee $serviceFee): bool
    {
        return $authUser->can('delete:service_fee');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:service_fee');
    }

    public function restore(AuthUser $authUser, ServiceFee $serviceFee): bool
    {
        return $authUser->can('restore:service_fee');
    }
}
