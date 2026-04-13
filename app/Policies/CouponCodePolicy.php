<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CouponCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponCodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CouponCodeResource');
    }

    public function view(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('View:CouponCodeResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CouponCodeResource');
    }

    public function update(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Update:CouponCodeResource');
    }

    public function delete(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Delete:CouponCodeResource');
    }

    public function restore(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Restore:CouponCodeResource');
    }

    public function forceDelete(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('ForceDelete:CouponCodeResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CouponCodeResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CouponCodeResource');
    }

    public function replicate(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Replicate:CouponCodeResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CouponCodeResource');
    }

}