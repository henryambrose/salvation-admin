<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Gender;
use App\Models\Member;
use App\Models\Relationship;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display a stats of the resource.
     */
    public function index() : Response
    {
        $communityCount = Community::count();
        $memberCount = Member::count();
        $familyCount = Member::distinct('family_no')->count('family_no');

        $genders = Gender::all()->pluck('name', 'id')->map(function ($gender) {
            return ucfirst($gender);
        })->toArray();
        $genderData = Member::select(['gender_id', DB::raw("count('gender_id') AS total")])->groupBy('gender_id')
            ->get()
            ->mapWithKeys(function ($item) use ($genders) {
                return [$genders[$item->gender_id] => $item->total];
            })
            ->toArray();

        $ageSql = "SELECT
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
        ";
        $ageWiseDataResult = DB::select($ageSql);
        $ageWiseData = [];
        foreach ($ageWiseDataResult as $row) {
            $ageGroup = $row->age_group;
            $gender = $genders[$row->gender_id];
            $total = $row->total;

            if (!isset($ageWiseData[$ageGroup])) {
                $ageWiseData[$ageGroup] = ['Male' => 0, 'Female' => 0, 'Other' => 0];
            }

            $ageWiseData[$ageGroup][$gender] = $total;
        }

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $dayAfterTomorrow = Carbon::today()->addDays(2);

        $birthdays = Member::select(['id', 'first_name', 'middle_name', 'last_name', 'date_of_birth', DB::raw("CASE
                            WHEN date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, date_of_birth, NOW())
                            ELSE NULL
                        END AS age"), 'contact_no', 'email'])->where(function ($query) use ($today, $tomorrow, $dayAfterTomorrow) {
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
        ->orderByRaw("MONTH(date_of_birth), DAY(date_of_birth)")
        ->get(['name', 'date_of_birth']);

        /** community wise members with community name and member count */
        $communityWiseMembers = Community::withCount('members')
            ->get()
            ->mapWithKeys(function ($community) {
                return [$community->name => $community->members_count];
            });
        // \Log::debug('Community Wise Members:', $communityWiseMembers->toArray());

        // COMMUNITY WISE FAMILY
        $communityWiseFamilies = Community::withCount(['members as family_count' => function ($query) {
            $query->select(DB::raw('COUNT(DISTINCT family_no)'));
        }])->get()
            ->mapWithKeys(function ($community) {
                return [$community->name => $community->family_count];
            });

        // \Log::debug('Community Wise Families:', $communityWiseFamilies->toArray());

        // STATUS WISE
        $statuses = Status::all()->pluck('name', 'id')->map(function ($status) {
            return ucfirst($status);
        })->toArray();
        $statusWiseMembers = Member::select(['status_id', DB::raw("count('status_id') AS total")])
            ->groupBy('status_id')
            ->get()
            ->mapWithKeys(function ($item) use ($statuses) {
                return [$statuses[$item->status_id] => $item->total];
            })
            ->toArray();
        // \Log::debug('Status Wise Members:', $statusWiseMembers);

        // designation wise members
        $designationWiseMembers = Member::select(['designation', DB::raw("count('designation') AS total")])
            ->groupBy('designation')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->designation => $item->total];
            })
            ->toArray();
        // \Log::debug('Designation Wise Members:', $designationWiseMembers);

        // latest_qualifications wise members
        $latestQualificationsWiseMembers = Member::select(['latest_qualifications', DB::raw("count('latest_qualifications') AS total")])
            ->groupBy('latest_qualifications')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->latest_qualifications => $item->total];
            })
            ->toArray();
        // \Log::debug('Latest Qualifications Wise Members:', $latestQualificationsWiseMembers);

        // relationship wise members
        $relationships = Relationship::all()->pluck('name', 'id')->map(function ($relationship) {
            return ucfirst($relationship);
        })->toArray();
        $relationshipWiseMembers = Member::select(['relationship_id', DB::raw("count('relationship_id') AS total")])
            ->groupBy('relationship_id')
            ->get()
            ->mapWithKeys(function ($item) use ($relationships) {
                return [$relationships[$item->relationship_id] => $item->total];
            })
            ->toArray();
        // \Log::debug('Relationship Wise Members:', $relationshipWiseMembers);


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
                'title' => 'Gender Wise',
                'data' => $genderData,
                'icon' => 'i-heroicons-chart-bar',
                'bgClass' => 'bg-indigo-500',
                'borderClass' => 'border-indigo-800',
            ],
            [
                'title' => 'Community Wise Members',
                'data' => $communityWiseMembers,
                'icon' => 'i-heroicons-users',
                'bgClass' => 'bg-purple-500',
                'borderClass' => 'border-purple-800',
            ],
            [
                'title' => 'Community Wise Families',
                'data' => $communityWiseFamilies,
                'icon' => 'i-heroicons-home',
                'bgClass' => 'bg-sky-500',
                'borderClass' => 'border-sky-800',
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
            [
                'title' => 'Relationship Wise Members',
                'data' => $relationshipWiseMembers,
                'icon' => 'i-heroicons-users',
                'bgClass' => 'bg-red-500',
                'borderClass' => 'border-red-800',
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
