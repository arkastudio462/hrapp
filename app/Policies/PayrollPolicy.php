<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;

class PayrollPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function view(User $user, Payroll $payroll): bool
    {
        if ($user->isAdmin() || $user->isHr()) {
            return true;
        }

        return $user->employee_id === $payroll->employee_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function update(User $user, Payroll $payroll): bool
    {
        return $user->isAdmin() || $user->isHr();
    }

    public function delete(User $user, Payroll $payroll): bool
    {
        return $user->isAdmin() || $user->isHr();
    }
}
