<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the graveyard dashboard
     */
    public function index(): Response
    {
        $this->authorize('read-graveyard-dashboard');

        // TODO: Add dashboard statistics and data
        $stats = [
            'total_cemeteries' => 0,
            'total_graves' => 0,
            'occupied_graves' => 0,
            'available_graves' => 0,
            'recent_burials' => [],
            'maintenance_alerts' => [],
            'revenue_summary' => [
                'monthly' => 0,
                'yearly' => 0,
            ]
        ];

        return Inertia::render('PagesGraveyard/Dashboard/Index', [
            'stats' => $stats,
        ]);
    }
}
