<?php

namespace App\Policies;

use App\ProductMedia;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductMediaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return $user->hasPermission('product_media', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\ProductMedia  $productMedia
     * @return mixed
     */
    public function view(User $user, ProductMedia $productMedia)
    {
        return $user->hasPermission('product_media', 'viewAny') || ($user->id === $productMedia->user_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('product_media', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\ProductMedia  $productMedia
     * @return mixed
     */
    public function update(User $user, ProductMedia $productMedia)
    {
        return $user->hasPermission('product_media', 'update') || ($user->id === $productMedia->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ProductMedia  $productMedia
     * @return mixed
     */
    public function delete(User $user, ProductMedia $productMedia)
    {
        return $user->hasPermission('product_media', 'delete') || ($user->id === $productMedia->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\ProductMedia  $productMedia
     * @return mixed
     */
    public function restore(User $user, ProductMedia $productMedia)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\ProductMedia  $productMedia
     * @return mixed
     */
    public function forceDelete(User $user, ProductMedia $productMedia)
    {
        //
    }
}
