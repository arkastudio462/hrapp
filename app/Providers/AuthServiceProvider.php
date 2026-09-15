<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\Position;
use App\Policies\DepartmentPolicy;
use App\Policies\EmployeePolicy;
use App\Policies\LeaveRequestPolicy;
use App\Policies\PayrollPolicy;
use App\Policies\PositionPolicy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy map for the application.
     *
     * @var array<class-string<Model>, class-string<AuthorizesRequests>>
     */
    protected $policies = [
        Employee::class => EmployeePolicy::class,
        Department::class => DepartmentPolicy::class,
        Position::class => PositionPolicy::class,
        LeaveRequest::class => LeaveRequestPolicy::class,
        Payroll::class => PayrollPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
