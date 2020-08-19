<?php

namespace App\Policies;

use App\Flow;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlowPolicy
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
        return $user->hasPermission('flow', 'viewAny');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Flow  $flow
     * @return mixed
     */
    public function view(User $user, Flow $flow)
    {
        return $user->hasPermission('flow', 'viewAny') || ($user->id === $flow->user_id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->hasPermission('flow', 'create');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Flow  $flow
     * @return mixed
     */
    public function update(User $user, Flow $flow)
    {
        return $user->hasPermission('flow', 'update') || ($user->id === $flow->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Flow  $flow
     * @return mixed
     */
    public function delete(User $user, Flow $flow)
    {
        return $user->hasPermission('flow', 'delete') || ($user->id === $flow->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Flow  $flow
     * @return mixed
     */
    public function restore(User $user, Flow $flow)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Flow  $flow
     * @return mixed
     */
    public function forceDelete(User $user, Flow $flow)
    {
        //
    }
}
