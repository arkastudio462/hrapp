<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Employee;
use App\Models\Tenant;
use Illuminate\Support\Facades\Storage;

class UsageMeteringService
{
    public function getEmployeeCount(int $tenantId): int
    {
        return Employee::where('tenant_id', $tenantId)->count();
    }

    public function getActiveEmployeeCount(int $tenantId): int
    {
        return Employee::where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->count();
    }

    public function getStorageUsage(int $tenantId): float
    {
        $path = "tenants/{$tenantId}";

        try {
            $files = Storage::disk('tenant')->files($path);
            $totalSize = 0;

            foreach ($files as $file) {
                $totalSize += Storage::disk('tenant')->size($file);
            }

            return round($totalSize / (1024 * 1024 * 1024), 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function checkLimits(Tenant $tenant): array
    {
        $limits = $tenant->limits ?? [];
        $maxEmployees = $limits['max_employees'] ?? 10;
        $maxStorage = $limits['max_storage_gb'] ?? 1;

        $currentEmployees = $this->getActiveEmployeeCount($tenant->id);
        $currentStorage = $this->getStorageUsage($tenant->id);

        return [
            'employees' => [
                'current' => $currentEmployees,
                'limit' => $maxEmployees,
                'percentage' => $maxEmployees > 0 ? round(($currentEmployees / $maxEmployees) * 100) : 0,
                'exceeded' => $currentEmployees > $maxEmployees,
            ],
            'storage' => [
                'current' => $currentStorage,
                'limit' => $maxStorage,
                'percentage' => $maxStorage > 0 ? round(($currentStorage / $maxStorage) * 100) : 0,
                'exceeded' => $currentStorage > $maxStorage,
            ],
        ];
    }

    public function getUsageStats(int $tenantId): array
    {
        $employeeCount = $this->getEmployeeCount($tenantId);
        $activeEmployeeCount = $this->getActiveEmployeeCount($tenantId);
        $storageUsage = $this->getStorageUsage($tenantId);

        return [
            'total_employees' => $employeeCount,
            'active_employees' => $activeEmployeeCount,
            'storage_gb' => $storageUsage,
        ];
    }
}
