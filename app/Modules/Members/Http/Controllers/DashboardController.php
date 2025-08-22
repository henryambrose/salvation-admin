<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Models\Community;
use Modules\Members\Models\Designation;
use Modules\Members\Models\Gender;
use Modules\Members\Models\Member;
use Modules\Members\Models\Relationship;
use Modules\Members\Models\Status;
use Modules\Members\Models\Zone;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display a stats of the resource.
     */
    public function index(): Response
    {
        $communityCount = Community::count();
        $memberCount = Member::count();
        $familyCount = DB::table('members')
            ->whereNotNull('family_no')
            ->whereRaw('TRIM(family_no) != ?', [''])
            ->distinct('family_no')
            ->count('family_no');
        $genders = Gender::all()->pluck('name', 'id')->map(function ($gender) {
            return ucfirst($gender);
        })->toArray();
        $genderData = Member::select(['gender_id', DB::raw("count('gender_id') AS total")])->groupBy('gender_id')
            ->get()
            ->mapWithKeys(function ($item) use ($genders) {
                return [$genders[$item->gender_id] ?? 'Unknown' => $item->total];
            })
            ->toArray();

        $ageSql = 'SELECT
                    t1.gender_id,
                    COUNT(*) AS total,
                    ag.name AS age_group
                FROM (
                    SELECT
                        id,
                        gender_id,
                        CASE
                            WHEN date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, date_of_birth, NOW())
                            ELSE NULL
                        END AS age
                    FROM members
                ) AS t1
                JOIN age_groups ag
                ON t1.age IS NOT NULL AND t1.age BETWEEN ag.min_age AND ag.max_age
                GROUP BY t1.gender_id, ag.name
                ORDER BY ag.min_age, t1.gender_id;
        ';
        $ageWiseDataResult = DB::select($ageSql);
        $ageWiseData = [];
        foreach ($ageWiseDataResult as $row) {
            $ageGroup = $row->age_group;
            $gender = $genders[$row->gender_id] ?? 'Unknown';
            $total = $row->total;

            if (! isset($ageWiseData[$ageGroup])) {
                $ageWiseData[$ageGroup] = ['Male' => 0, 'Female' => 0, 'Other' => 0];
            }

            $ageWiseData[$ageGroup][$gender] = $total;
        }

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $dayAfterTomorrow = Carbon::today()->addDays(2);

        $birthdays = Member::select(['id', 'first_name', 'middle_name', 'last_name', 'date_of_birth', DB::raw('CASE
                            WHEN date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, date_of_birth, NOW())
                            ELSE NULL
                        END AS age'), 'contact_no_1', 'email'])->where(function ($query) use ($today) {
            $query->whereMonth('date_of_birth', $today->month)
                ->whereDay('date_of_birth', $today->day);
        })->orWhere(function ($query) use ($tomorrow) {
            $query->whereMonth('date_of_birth', $tomorrow->month)
                ->whereDay('date_of_birth', $tomorrow->day);
        })->orWhere(function ($query) use ($dayAfterTomorrow) {
            $query->whereMonth('date_of_birth', $dayAfterTomorrow->month)
                ->whereDay('date_of_birth', $dayAfterTomorrow->day);
        })
            ->with('community')->addSelect(['community_id'])
            ->orderByRaw('MONTH(date_of_birth), DAY(date_of_birth)')
            ->get(['name', 'date_of_birth']);

        /** COMMUNITY WISE STATISTICS (MEMBERS AND FAMILIES) */
        $communityWiseStats = Community::with('members')
            ->get()
            ->mapWithKeys(function ($community) {
                $memberCount = $community->members->count();
                $familyCount = $community->members
                    ->filter(fn ($m) => ! is_null($m->family_no) && trim($m->family_no) !== '')
                    ->pluck('family_no')
                    ->unique()
                    ->count();

                return [$community->name => [
                    'members' => $memberCount,
                    'families' => $familyCount,
                ]];
            })
            ->filter(function ($stats) {
                return $stats['members'] > 0 || $stats['families'] > 0; // Only show communities with data
            });

        // ZONE WISE STATISTICS (MEMBERS AND FAMILIES)
        $zoneWiseStats = Zone::with('communities.members')
            ->get()
            ->mapWithKeys(function ($zone) {
                $memberCount = $zone->communities->sum(function ($community) {
                    return $community->members->count();
                });

                $familyCount = $zone->communities->flatMap(function ($community) {
                    return $community->members
                        ->filter(fn ($m) => ! is_null($m->family_no) && trim($m->family_no) !== '')
                        ->pluck('family_no');
                })->unique()->count();

                return [$zone->name => [
                    'members' => $memberCount,
                    'families' => $familyCount,
                ]];
            })
            ->filter(function ($stats) {
                return $stats['members'] > 0 || $stats['families'] > 0; // Only show zones with data
            });


        // STATUS WISE
        $statuses = Status::all()->pluck('name', 'id')->map(function ($status) {
            return ucfirst($status);
        })->toArray();
        $statusWiseMembers = Member::select(['status_id', DB::raw("count('status_id') AS total")])
            ->groupBy('status_id')
            ->get()
            ->mapWithKeys(function ($item) use ($statuses) {
                return [$statuses[$item->status_id] ?? 'Unknown' => $item->total];
            })
            ->toArray();

        // designation wise members
        $designations = Designation::all()->pluck('name', 'id')->map(function ($status) {
            return ucfirst($status);
        })->toArray();
        $designationWiseMembers = Member::select(['designation_id', DB::raw("count('designation_id') AS total")])
            ->groupBy('designation_id')
            ->get()
            ->mapWithKeys(function ($item) use ($designations) {
                return [$designations[$item->designation_id] ?? 'Unknown' => $item->total];
            })
            ->toArray();

        // latest_qualifications wise members
        $latestQualificationsWiseMembers = Member::select(['latest_qualifications', DB::raw("count('latest_qualifications') AS total")])
            ->groupBy('latest_qualifications')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->latest_qualifications => $item->total];
            })
            ->toArray();
       
        // relationship wise members
        $relationships = Relationship::all()->pluck('name', 'id')->map(function ($relationship) {
            return ucfirst($relationship);
        })->toArray();
        $relationshipWiseMembers = Member::select(['relationship_id', DB::raw("count('relationship_id') AS total")])
            ->groupBy('relationship_id')
            ->get()
            ->mapWithKeys(function ($item) use ($relationships) {
                return [$relationships[$item->relationship_id] ?? 'Unknown' => $item->total];
            })
            ->toArray();
      
        $statCards = [
            [
                'title' => 'Communities',
                'count' => $communityCount,
                'icon' => 'i-heroicons-users',
                'bgClass' => 'bg-indigo-500',
                'borderClass' => 'border-indigo-800',
            ],
            [
                'title' => 'Members',
                'count' => $memberCount,
                'icon' => 'i-heroicons-user-group',
                'bgClass' => 'bg-purple-500',
                'borderClass' => 'border-purple-800',
            ],
            [
                'title' => 'Families',
                'count' => $familyCount,
                'icon' => 'i-heroicons-home',
                'bgClass' => 'bg-sky-500',
                'borderClass' => 'border-sky-800',
            ],
        ];
        $tableCards = [
            [
                'title' => 'Zone-wise Statistics',
                'data' => $zoneWiseStats,
                'icon' => 'i-heroicons-map',
                'bgClass' => 'bg-orange-500',
                'borderClass' => 'border-orange-800',
            ],
            [
                'title' => 'Community-wise Statistics',
                'data' => $communityWiseStats,
                'icon' => 'i-heroicons-users',
                'bgClass' => 'bg-purple-500',
                'borderClass' => 'border-purple-800',
            ],
            [
                'title' => 'Gender Wise',
                'data' => $genderData,
                'icon' => 'i-heroicons-chart-bar',
                'bgClass' => 'bg-indigo-500',
                'borderClass' => 'border-indigo-800',
            ],
            [
                'title' => 'Relationship Wise Members',
                'data' => $relationshipWiseMembers,
                'icon' => 'i-heroicons-users',
                'bgClass' => 'bg-red-500',
                'borderClass' => 'border-red-800',
            ],
            [
                'title' => 'Status Wise Members',
                'data' => $statusWiseMembers,
                'icon' => 'i-heroicons-chart-pie',
                'bgClass' => 'bg-green-500',
                'borderClass' => 'border-green-800',
            ],
            [
                'title' => 'Designation Wise Members',
                'data' => $designationWiseMembers,
                'icon' => 'i-heroicons-briefcase',
                'bgClass' => 'bg-yellow-500',
                'borderClass' => 'border-yellow-800',
            ],
            [
                'title' => 'Latest Qualifications Wise Members',
                'data' => $latestQualificationsWiseMembers,
                'icon' => 'i-heroicons-graduation-cap',
                'bgClass' => 'bg-blue-500',
                'borderClass' => 'border-blue-800',
            ],
        ];

        return Inertia::render('Dashboard', [
            'title' => 'Dashboard',
            'statCards' => $statCards,
            'tableCards' => $tableCards,
            'ageWiseData' => $ageWiseData,
            'birthdays' => $birthdays,
        ]);
    }
}
