<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\PaymentGateway;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class PaymentGatewayPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:payment_gateway');
    }

    public function view(AuthUser $authUser, PaymentGateway $paymentGateway): bool
    {
        return $authUser->can('view:payment_gateway');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:payment_gateway');
    }

    public function update(AuthUser $authUser, PaymentGateway $paymentGateway): bool
    {
        return $authUser->can('update:payment_gateway');
    }

    public function delete(AuthUser $authUser, PaymentGateway $paymentGateway): bool
    {
        return $authUser->can('delete:payment_gateway');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:payment_gateway');
    }

    public function restore(AuthUser $authUser, PaymentGateway $paymentGateway): bool
    {
        return $authUser->can('restore:payment_gateway');
    }
}
