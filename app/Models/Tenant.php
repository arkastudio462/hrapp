<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Models\TenantWithDomain;

class Tenant extends TenantWithDomain
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'package_id',
        'subscription_status',
        'trial_ends_at',
        'settings',
        'limits',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
            'settings' => 'array',
            'limits' => 'array',
        ];
    }

    /**
     * Get the users in the tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the employees in the tenant.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * Get the departments in the tenant.
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    /**
     * Get the positions in the tenant.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }

    /**
     * Check if the tenant is on a trial period.
     */
    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial' && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if the tenant subscription is active.
     */
    public function isActive(): bool
    {
        return $this->subscription_status === 'active';
    }

    /**
     * Check if the tenant can add more employees.
     */
    public function canAddEmployee(): bool
    {
        $maxEmployees = $this->limits['max_employees'] ?? 10;
        $currentEmployees = $this->employees()->count();

        return $currentEmployees < $maxEmployees;
    }

    /**
     * Get the tenant's storage limit in bytes.
     */
    public function getStorageLimit(): int
    {
        $storageGb = $this->limits['max_storage_gb'] ?? 1;

        return $storageGb * 1024 * 1024 * 1024; // Convert GB to bytes
    }
}
