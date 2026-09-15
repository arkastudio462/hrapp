<?php

namespace App\Http\Middleware;

use App\Models\Employee;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'tenant' => fn () => $this->getTenant($request),
        ];
    }

    private function getTenant(Request $request): ?array
    {
        $user = $request->user();

        if (! $user || ! $user->tenant_id) {
            return null;
        }

        $tenant = Tenant::with('package')->find($user->tenant_id);

        if (! $tenant) {
            return null;
        }

        return [
            'id' => $tenant->id,
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'logo' => $tenant->logo,
            'subscription_status' => $tenant->subscription_status,
            'package' => $tenant->package ? [
                'id' => $tenant->package->id,
                'name' => $tenant->package->name,
                'max_employees' => $tenant->package->max_employees,
            ] : null,
            'limits' => $tenant->limits,
            'attendance_method' => $tenant->settings['attendance_method'] ?? 'face_location',
            'employee_count' => Employee::where('tenant_id', $tenant->id)->where('is_active', true)->count(),
        ];
    }
}
