<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductResource');
    }

    public function view(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('View:ProductResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductResource');
    }

    public function update(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Update:ProductResource');
    }

    public function delete(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Delete:ProductResource');
    }

    public function restore(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Restore:ProductResource');
    }

    public function forceDelete(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('ForceDelete:ProductResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductResource');
    }

    public function replicate(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Replicate:ProductResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductResource');
    }

}