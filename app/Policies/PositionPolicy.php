<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Position;
use App\Models\User;

class PositionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function view(User $user, Position $position): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function update(User $user, Position $position): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function delete(User $user, Position $position): bool
    {
        return $user->isAdmin() || $user->isHr();
    }
}
