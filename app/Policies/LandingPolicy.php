<?php

namespace App\Policies;

use App\Landing;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LandingPolicy
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
        return $user->hasPermission('landing', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Landing  $landing
     * @return mixed
     */
    public function view(User $user, Landing $landing)
    {
        return $user->hasPermission('landing', 'viewAny') || ($user->id === $landing->id && !$landing->archived);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('landing', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Landing  $landing
     * @return mixed
     */
    public function update(User $user, Landing $landing)
    {
        return $user->hasPermission('landing', 'update') || ($user->id === $landing->id && !$landing->archived);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Landing  $landing
     * @return mixed
     */
    public function delete(User $user, Landing $landing)
    {
        return $user->hasPermission('landing', 'delete') || ($user->id === $landing->id && !$landing->archived);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Landing  $landing
     * @return mixed
     */
    public function restore(User $user, Landing $landing)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Landing  $landing
     * @return mixed
     */
    public function forceDelete(User $user, Landing $landing)
    {
        //
    }
}
