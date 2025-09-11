<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Modules\Fund\Models\MassIntention;
use Modules\Graveyard\Models\TemporaryGraveBooking;
use Modules\Graveyard\Models\PermanentGraveBooking;
use Modules\Fund\Models\FamilyContribution;

class QueryOptimizationService
{
    /**
     * Get mass intentions with optimized relationships
     */
    public function getOptimizedMassIntentions(array $filters = []): Collection
    {
        return MassIntention::with([
            'member:id,first_name,last_name,member_no',
            'massType:id,name,default_amount',
            'massIntentionType:id,name',
            'paymentMethod:id,name',
            'createdBy:id,name',
        ])
        ->when(isset($filters['status']), function (Builder $query) use ($filters) {
            return $query->where('status', $filters['status']);
        })
        ->when(isset($filters['date_from']), function (Builder $query) use ($filters) {
            return $query->where('mass_date', '>=', $filters['date_from']);
        })
        ->when(isset($filters['date_to']), function (Builder $query) use ($filters) {
            return $query->where('mass_date', '<=', $filters['date_to']);
        })
        ->orderBy('mass_date', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();
    }

    /**
     * Get temporary grave bookings with optimized relationships
     */
    public function getOptimizedTemporaryGraveBookings(array $filters = []): Collection
    {
        return TemporaryGraveBooking::with([
            'temporaryGrave:id,grave_no,section,row_no',
            'deceasedMember:id,first_name,last_name,member_no',
            'gender:id,name',
            'parish:id,name',
            'createdBy:id,name',
            'payments' => function ($query) {
                $query->select('id', 'payable_id', 'payable_type', 'amount', 'payment_status', 'payment_date');
            }
        ])
        ->when(isset($filters['status']), function (Builder $query) use ($filters) {
            return $query->where('status', $filters['status']);
        })
        ->when(isset($filters['transfer_due']), function (Builder $query) use ($filters) {
            if ($filters['transfer_due'] === 'overdue') {
                return $query->where('transfer_due_date', '<', now());
            } elseif ($filters['transfer_due'] === 'due_soon') {
                return $query->whereBetween('transfer_due_date', [now(), now()->addDays(30)]);
            }
        })
        ->orderBy('booking_date', 'desc')
        ->get();
    }

    /**
     * Get permanent grave bookings with optimized relationships
     */
    public function getOptimizedPermanentGraveBookings(array $filters = []): Collection
    {
        return PermanentGraveBooking::with([
            'permanentGrave:id,grave_no,section,row_no',
            'validMember:id,first_name,last_name,member_no,family_no',
            'createdBy:id,name',
            'payments' => function ($query) {
                $query->select('id', 'payable_id', 'payable_type', 'amount', 'payment_status', 'payment_date');
            }
        ])
        ->when(isset($filters['status']), function (Builder $query) use ($filters) {
            return $query->where('status', $filters['status']);
        })
        ->when(isset($filters['date_from']), function (Builder $query) use ($filters) {
            return $query->where('booking_date', '>=', $filters['date_from']);
        })
        ->when(isset($filters['date_to']), function (Builder $query) use ($filters) {
            return $query->where('booking_date', '<=', $filters['date_to']);
        })
        ->orderBy('booking_date', 'desc')
        ->get();
    }

    /**
     * Get family contributions with optimized relationships
     */
    public function getOptimizedFamilyContributions(array $filters = []): Collection
    {
        return FamilyContribution::with([
            'member:id,first_name,last_name,member_no,family_no',
            'paymentMethod:id,name',
            'fundCategory:id,name',
            'createdBy:id,name',
        ])
        ->when(isset($filters['family_no']), function (Builder $query) use ($filters) {
            return $query->where('family_no', 'LIKE', '%' . $filters['family_no'] . '%');
        })
        ->when(isset($filters['status']), function (Builder $query) use ($filters) {
            return $query->where('status', $filters['status']);
        })
        ->when(isset($filters['year']), function (Builder $query) use ($filters) {
            return $query->whereYear('start_date', $filters['year']);
        })
        ->orderBy('start_date', 'desc')
        ->get();
    }

    /**
     * Get dashboard statistics with optimized queries
     */
    public function getDashboardStats(): array
    {
        return [
            'mass_intentions' => [
                'total' => MassIntention::count(),
                'pending' => MassIntention::where('status', 'pending')->count(),
                'completed' => MassIntention::where('status', 'completed')->count(),
                'this_month' => MassIntention::whereMonth('created_at', now()->month)->count(),
            ],
            'grave_bookings' => [
                'temporary_total' => TemporaryGraveBooking::count(),
                'temporary_pending' => TemporaryGraveBooking::where('status', 'pending')->count(),
                'permanent_total' => PermanentGraveBooking::count(),
                'permanent_confirmed' => PermanentGraveBooking::where('status', 'confirmed')->count(),
                'overdue_transfers' => TemporaryGraveBooking::where('transfer_due_date', '<', now())->count(),
            ],
            'contributions' => [
                'total_families' => FamilyContribution::distinct('family_no')->count(),
                'paid_this_year' => FamilyContribution::where('status', 'paid')
                    ->whereYear('start_date', now()->year)->count(),
                'pending_payments' => FamilyContribution::where('status', 'pending')->count(),
            ]
        ];
    }

    /**
     * Search members with optimized query
     */
    public function searchMembersOptimized(string $query, int $limit = 20): Collection
    {
        $searchTerm = '%' . $query . '%';
        
        return \Modules\Members\Models\Member::select([
            'id', 'first_name', 'last_name', 'member_no', 'family_no', 'phone', 'email'
        ])
        ->where(function ($q) use ($searchTerm) {
            $q->where('first_name', 'LIKE', $searchTerm)
              ->orWhere('last_name', 'LIKE', $searchTerm)  
              ->orWhere('member_no', 'LIKE', $searchTerm)
              ->orWhere('family_no', 'LIKE', $searchTerm)
              ->orWhere('phone', 'LIKE', $searchTerm);
        })
        ->orderBy('first_name')
        ->orderBy('last_name')
        ->limit($limit)
        ->get();
    }

    /**
     * Get recent activity with optimized relationships
     */
    public function getRecentActivityOptimized(int $days = 7, int $limit = 50): array
    {
        $since = now()->subDays($days);
        
        $recentMassIntentions = MassIntention::with([
            'member:id,first_name,last_name',
            'createdBy:id,name'
        ])
        ->where('created_at', '>=', $since)
        ->latest()
        ->limit($limit)
        ->get();

        $recentBookings = TemporaryGraveBooking::with([
            'temporaryGrave:id,grave_no,section',
            'createdBy:id,name'
        ])
        ->where('created_at', '>=', $since)
        ->latest()
        ->limit($limit)
        ->get();

        $recentContributions = FamilyContribution::with([
            'member:id,first_name,last_name',
            'createdBy:id,name'
        ])
        ->where('created_at', '>=', $since)
        ->latest()
        ->limit($limit)
        ->get();

        return [
            'mass_intentions' => $recentMassIntentions,
            'bookings' => $recentBookings,
            'contributions' => $recentContributions,
        ];
    }

    /**
     * Preload commonly used lookup data
     */
    public function preloadLookupData(): array
    {
        return [
            'mass_types' => \Modules\Fund\Models\MassType::select('id', 'name', 'default_amount')->get(),
            'intention_types' => \Modules\Fund\Models\MassIntentionType::select('id', 'name')->get(),
            'payment_methods' => \Modules\Fund\Models\PaymentMethod::select('id', 'name')->get(),
            'fund_categories' => \Modules\Fund\Models\FundCategory::select('id', 'name')->get(),
            'genders' => \Modules\Members\Models\Gender::select('id', 'name')->get(),
            'parishes' => \Modules\Members\Models\Parish::select('id', 'name')->get(),
        ];
    }
}