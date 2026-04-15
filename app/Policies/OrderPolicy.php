<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function own(AuthUser $authUser, Order $order): bool
    {
        return (int) $order->user_id === (int) $authUser->id;
    }

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OrderResource');
    }

    public function view(AuthUser $authUser, Order $order): bool
    {
        return $authUser->can('View:OrderResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OrderResource');
    }

    public function update(AuthUser $authUser, Order $order): bool
    {
        return $authUser->can('Update:OrderResource');
    }

    public function delete(AuthUser $authUser, Order $order): bool
    {
        return $authUser->can('Delete:OrderResource');
    }

    public function restore(AuthUser $authUser, Order $order): bool
    {
        return $authUser->can('Restore:OrderResource');
    }

    public function forceDelete(AuthUser $authUser, Order $order): bool
    {
        return $authUser->can('ForceDelete:OrderResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OrderResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OrderResource');
    }

    public function replicate(AuthUser $authUser, Order $order): bool
    {
        return $authUser->can('Replicate:OrderResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OrderResource');
    }

}