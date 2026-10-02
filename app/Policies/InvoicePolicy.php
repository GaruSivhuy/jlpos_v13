<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Invoice;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class InvoicePolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('view_any:invoice');
    }

    public function viewAnyInvoice(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_invoice:invoice');
    }

    public function viewAnyPos(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_pos:invoice');
    }

    public function viewAnyInvoiceDetail(AuthUser $authUser): bool
    {
        return $authUser->can('view_any_invoice_detail:invoice');
    }

    public function view(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('view:invoice');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('create:invoice');
    }

    public function update(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('update:invoice');
    }

    public function delete(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('delete:invoice');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('delete_any:invoice');
    }

    public function restore(AuthUser $authUser, Invoice $invoice): bool
    {
        return $authUser->can('restore:invoice');
    }
}
