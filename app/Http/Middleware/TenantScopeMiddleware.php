<?php

declare(strict_types=1);

namespace App\Http\Middleware;

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
        config(['tenant.id' => $user->tenant_id]);

        return $next($request);
    }
}
