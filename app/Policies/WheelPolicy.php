<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Wheel;
use Illuminate\Auth\Access\Response;

class WheelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Wheel $wheel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Wheel $wheel): bool
    {
        return $user->id === $wheel->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Wheel $wheel): bool
    {
        return $user->id === $wheel->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Wheel $wheel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Wheel $wheel): bool
    {
        return false;
    }
}
