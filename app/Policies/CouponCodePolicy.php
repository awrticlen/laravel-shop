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
        return $authUser->can('ViewAny:CouponCode');
    }

    public function view(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('View:CouponCode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CouponCode');
    }

    public function update(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Update:CouponCode');
    }

    public function delete(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Delete:CouponCode');
    }

    public function restore(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Restore:CouponCode');
    }

    public function forceDelete(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('ForceDelete:CouponCode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CouponCode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CouponCode');
    }

    public function replicate(AuthUser $authUser, CouponCode $couponCode): bool
    {
        return $authUser->can('Replicate:CouponCode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CouponCode');
    }

}