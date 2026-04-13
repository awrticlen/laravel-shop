<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Category;
use Illuminate\Auth\Access\HandlesAuthorization;

class CategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CategoryResource');
    }

    public function view(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('View:CategoryResource');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CategoryResource');
    }

    public function update(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('Update:CategoryResource');
    }

    public function delete(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('Delete:CategoryResource');
    }

    public function restore(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('Restore:CategoryResource');
    }

    public function forceDelete(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('ForceDelete:CategoryResource');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CategoryResource');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CategoryResource');
    }

    public function replicate(AuthUser $authUser, Category $category): bool
    {
        return $authUser->can('Replicate:CategoryResource');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CategoryResource');
    }

}