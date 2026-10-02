<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Metric;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class MetricPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:metric');
    }

    public function view(AuthUser $authUser, Metric $metric): bool
    {
        return $authUser->can('view:metric');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:metric');
    }

    public function update(AuthUser $authUser, Metric $metric): bool
    {
        return $authUser->can('update:metric');
    }

    public function delete(AuthUser $authUser, Metric $metric): bool
    {
        return $authUser->can('delete:metric');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:metric');
    }

    public function restore(AuthUser $authUser, Metric $metric): bool
    {
        return $authUser->can('restore:metric');
    }
}
