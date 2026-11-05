<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantScopeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->tenant_id) {
            abort(403, 'No tenant context.');
        }

        // Set tenant_id in config for query scoping
        $tenant = Tenant::find($user->tenant_id);
        if ($tenant) {
            tenancy()->initialize($tenant);
        }

        return $next($request);
    }
}
