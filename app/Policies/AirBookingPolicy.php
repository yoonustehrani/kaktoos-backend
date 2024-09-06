<?php

namespace App\Policies;

use App\Models\AirBooking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AirBookingPolicy
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
    public function view(User $user, AirBooking $airBooking): bool
    {
        return $user->id === $airBooking->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AirBooking $airBooking): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AirBooking $airBooking): bool
    {
        return $user->id === $airBooking->user_id;
    }
}
