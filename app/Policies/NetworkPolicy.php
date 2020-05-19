<?php

namespace App\Policies;

use App\Network;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NetworkPolicy
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
        return $user->hasPermission('network', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Network  $network
     * @return mixed
     */
    public function view(User $user, Network $network)
    {
        return $user->hasPermission('network', 'viewAny');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('network', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Network  $network
     * @return mixed
     */
    public function update(User $user, Network $network)
    {
        return $user->hasPermission('network', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Network  $network
     * @return mixed
     */
    public function delete(User $user, Network $network)
    {
        return $user->hasPermission('network', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Network  $network
     * @return mixed
     */
    public function restore(User $user, Network $network)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Network  $network
     * @return mixed
     */
    public function forceDelete(User $user, Network $network)
    {
        //
    }
}
