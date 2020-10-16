<?php

namespace App\Policies;

use App\Domain;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DomainPolicy
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
        return $user->hasPermission('domain', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Domain  $domain
     * @return mixed
     */
    public function view(User $user, Domain $domain)
    {
        return $user->hasPermission('domain', 'viewAny');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('domain', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Domain  $domain
     * @return mixed
     */
    public function update(User $user, Domain $domain)
    {
        return $user->hasPermission('domain', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Domain  $domain
     * @return mixed
     */
    public function delete(User $user, Domain $domain)
    {
        return $user->hasPermission('domain', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Domain  $domain
     * @return mixed
     */
    public function restore(User $user, Domain $domain)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Domain  $domain
     * @return mixed
     */
    public function forceDelete(User $user, Domain $domain)
    {
        //
    }
}
