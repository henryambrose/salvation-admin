<?php

namespace Modules\Graveyard\Http\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Graveyard\Models\Niche;
use Modules\Graveyard\Models\Payment;
use Modules\Graveyard\Models\PermanentGrave;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Graveyard\Models\TemporaryGrave;
use Modules\Graveyard\Models\TemporaryGraveBooking;

class DashboardController extends Controller
{
    /**
     * Display the graveyard dashboard
     */
    public function index(): Response
    {
        $this->authorize('read-graveyard-dashboard');

        $occupancy = fn (string $model) => [
            'total' => $model::count(),
            'occupied' => $model::where('status', 'unavailable')->count(),
            'available' => $model::where('status', 'available')->count(),
        ];

        $paid = fn () => Payment::whereIn('payment_status', ['paid', 'partial', 'completed']);

        $recentBurials = TemporaryGrave::where('status', 'unavailable')
            ->whereNotNull('last_burial_date')
            ->get(['grave_no', 'section', 'buried_name', 'last_burial_date'])
            ->map(fn ($g) => [
                'name' => $g->buried_name,
                'location' => 'Temporary '.trim("{$g->section} {$g->grave_no}"),
                'date' => (string) $g->last_burial_date,
            ])
            ->concat(
                PermanentGrave::where('status', 'unavailable')
                    ->whereNotNull('last_burial_date')
                    ->get(['block', 'row', 'column', 'owner_name', 'last_burial_date'])
                    ->map(fn ($g) => [
                        'name' => $g->owner_name,
                        'location' => "Permanent {$g->block}-{$g->row}-{$g->column}",
                        'date' => (string) $g->last_burial_date,
                    ])
            )
            ->sortByDesc('date')
            ->take(5)
            ->values();

        $dues = PermanentGrave::where('pending_amount', '>', 0)
            ->get(['block', 'row', 'column', 'owner_name', 'pending_amount'])
            ->map(fn ($g) => [
                'name' => $g->owner_name,
                'location' => "Permanent {$g->block}-{$g->row}-{$g->column}",
                'amount' => (float) $g->pending_amount,
            ]);

        $stats = [
            'permanent_graves' => $occupancy(PermanentGrave::class),
            'temporary_graves' => $occupancy(TemporaryGrave::class),
            'niches' => $occupancy(Niche::class),
            'pending_bookings' => PermanentGraveBooking::where('status', 'pending')->count()
                + TemporaryGraveBooking::where('status', 'pending')->count(),
            'recent_burials' => $recentBurials,
            'maintenance_alerts' => [
                'total_due' => (float) $dues->sum('amount'),
                'count' => $dues->count(),
                'items' => $dues->sortByDesc('amount')->take(5)->values(),
            ],
            'revenue_summary' => [
                'monthly' => (float) $paid()->whereYear('payment_date', now()->year)
                    ->whereMonth('payment_date', now()->month)->sum('paid_amount'),
                'yearly' => (float) $paid()->whereYear('payment_date', now()->year)->sum('paid_amount'),
            ],
        ];

        return Inertia::render('PagesGraveyard/Dashboard/Index', [
            'stats' => $stats,
        ]);
    }
}
