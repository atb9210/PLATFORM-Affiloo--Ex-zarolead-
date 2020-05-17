<?php

namespace App\Policies;

use App\TrafficSource;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrafficSourcePolicy
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
        return $user->hasPermission('traffic_source', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\TrafficSource  $trafficSource
     * @return mixed
     */
    public function view(User $user, TrafficSource $trafficSource)
    {
        return $user->hasPermission('traffic_source', 'viewAny');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('traffic_source', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\TrafficSource  $trafficSource
     * @return mixed
     */
    public function update(User $user, TrafficSource $trafficSource)
    {
        return $user->hasPermission('traffic_source', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\TrafficSource  $trafficSource
     * @return mixed
     */
    public function delete(User $user, TrafficSource $trafficSource)
    {
        return $user->hasPermission('traffic_source', 'delete');
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\TrafficSource  $trafficSource
     * @return mixed
     */
    public function restore(User $user, TrafficSource $trafficSource)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\TrafficSource  $trafficSource
     * @return mixed
     */
    public function forceDelete(User $user, TrafficSource $trafficSource)
    {
        //
    }
}
