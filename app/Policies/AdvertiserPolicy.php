<?php

namespace App\Policies;

use App\Advertiser;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdvertiserPolicy
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
        return $user->hasPermission('advertiser', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Advertiser  $advertiser
     * @return mixed
     */
    public function view(User $user, Advertiser $advertiser)
    {
        return $user->hasPermission('advertiser', 'viewAny');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('advertiser', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Advertiser  $advertiser
     * @return mixed
     */
    public function update(User $user, Advertiser $advertiser)
    {
        return $user->hasPermission('advertiser', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Advertiser  $advertiser
     * @return mixed
     */
    public function delete(User $user, Advertiser $advertiser)
    {
        return $user->hasPermission('advertiser', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Advertiser  $advertiser
     * @return mixed
     */
    public function restore(User $user, Advertiser $advertiser)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Advertiser  $advertiser
     * @return mixed
     */
    public function forceDelete(User $user, Advertiser $advertiser)
    {
        //
    }
}
