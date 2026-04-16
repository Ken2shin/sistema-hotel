<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Room $room): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasPermission('create_rooms');
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasRole('admin') || $user->hasPermission('edit_rooms');
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasRole('admin');
    }
}
