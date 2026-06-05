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
        return $authUser->can('ViewAny:SeckillProductsResource');
    }

    public function view(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('View:SeckillProductsResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SeckillProductsResource');
    }

    public function update(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Update:SeckillProductsResource');
    }

    public function delete(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Delete:SeckillProductsResource');
    }

    public function restore(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Restore:SeckillProductsResource');
    }

    public function forceDelete(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('ForceDelete:SeckillProductsResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SeckillProductsResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SeckillProductsResource');
    }

    public function replicate(AuthUser $authUser, Product $product): bool
    {
        return $authUser->can('Replicate:SeckillProductsResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SeckillProductsResource');
    }

}