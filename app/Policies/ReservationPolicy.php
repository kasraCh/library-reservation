<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReservationPolicy
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
    public function view(User $user, Reservation $reservation): bool
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
    public function update(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        return false;
    }

    public function cancelReservation(User $user, Reservation $reservation) : Response
    {
        if ($reservation->user_id !== $user->id) {
            return Response::deny('You cannot cancel this reservation.');
        }
        if ($reservation->status !== 'active') {
            return Response::deny('reservation was canceled.');
        }

        return Response::allow();

    }

    public function returnReservation(User $user, Reservation $reservation) : Response
    {
        if ($reservation->user_id !== $user->id) {
            return Response::deny('You cannot cancel this reservation.');
        }
        if ($reservation->status !== 'active') {
            return Response::deny('reservation was returned or canceled.');
        }

        return Response::allow();
    }
}
