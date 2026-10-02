<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Metricsables;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MetricsablesPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:metricsables');
    }

    public function view(AuthUser $authUser, Metricsables $metricsables): bool
    {
        return $authUser->can('view:metricsables');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:metricsables');
    }

    public function update(AuthUser $authUser, Metricsables $metricsables): bool
    {
        return $authUser->can('update:metricsables');
    }

    public function delete(AuthUser $authUser, Metricsables $metricsables): bool
    {
        return $authUser->can('delete:metricsables');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:metricsables');
    }

    public function restore(AuthUser $authUser, Metricsables $metricsables): bool
    {
        return $authUser->can('restore:metricsables');
    }
}
