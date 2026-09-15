<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\LeaveRequest;
use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function view(User $user, LeaveRequest $leaveRequest): bool
    {
        if ($user->isAdmin() || $user->isHr()) {
            return true;
        }

        return $user->employee_id === $leaveRequest->employee_id;
    }

    public function create(User $user): bool
    {
        return $user->isEmployee();
    }

    public function update(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function delete(User $user, LeaveRequest $leaveRequest): bool
    {
        return $user->isAdmin() || $user->isHr();
    }
}
