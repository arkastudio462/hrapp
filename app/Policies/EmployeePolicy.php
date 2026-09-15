<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->isAdmin() || $user->isHr()) {
            return true;
        }

        return $user->employee_id === $employee->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function update(User $user, Employee $employee): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->isAdmin() || $user->isHr();
    }
}
