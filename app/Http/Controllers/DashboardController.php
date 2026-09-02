<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request)
    {
        // TODO: Get actual stats from database
        $stats = [
            'totalEmployees' => 0,
            'presentToday' => 0,
            'onLeave' => 0,
            'pendingApprovals' => 0,
        ];

        return Inertia::render('Dashboard', [
            'stats' => $stats,
        ]);
    }
}
