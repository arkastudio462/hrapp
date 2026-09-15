<?php

declare(strict_types=1);

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->search) {
            $query->where('description', 'like', "%{$request->search}%")
                ->orWhere('action', 'like', "%{$request->search}%");
        }

        if ($request->action) {
            $query->where('action', $request->action);
        }

        $logs = $query->latest()->paginate(20)->through(fn ($log) => [
            'id' => $log->id,
            'user_name' => $log->user?->name ?? 'System',
            'action' => $log->action,
            'description' => $log->description,
            'ip_address' => $log->ip_address,
            'properties' => $log->properties,
            'created_at' => $log->created_at->format('d M Y H:i:s'),
        ]);

        $actions = ActivityLog::distinct()->pluck('action')->filter()->values();

        return Inertia::render('SuperAdmin/ActivityLogs/Index', [
            'logs' => $logs,
            'actions' => $actions,
            'filters' => $request->only(['search', 'action']),
        ]);
    }
}
