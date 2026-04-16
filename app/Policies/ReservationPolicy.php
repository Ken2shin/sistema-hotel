<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('admin') || $user->hasPermission('view_reservations');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_reservations') || $user->hasRole('admin');
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('admin') || $user->hasPermission('edit_reservations');
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return $user->hasRole('admin');
    }
}
