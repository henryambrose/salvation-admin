<?php

namespace App\Http\Controllers;

use App\Models\Community;
use App\Models\Member;
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

        $genderData = Member::select(['gender', DB::raw("count('gender') AS total")])->groupBy('gender')
            ->get()
            ->mapWithKeys(function ($item) {
                return [ucfirst($item->gender) => $item->total];
            })
            ->toArray();

        $ageSql = "SELECT
                    t1.gender,
                    COUNT(*) AS total,
                    ag.name AS age_group
                FROM (
                    SELECT
                        id,
                        gender,
                        CASE
                            WHEN date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, date_of_birth, NOW())
                            ELSE NULL
                        END AS age
                    FROM members
                ) AS t1
                JOIN age_groups ag
                ON t1.age IS NOT NULL AND t1.age BETWEEN ag.min_age AND ag.max_age
                GROUP BY t1.gender, ag.name
                ORDER BY ag.min_age, t1.gender;
        ";
        $ageWiseDataResult = DB::select($ageSql);
        // $ageWiseData = collect($ageWiseDataResult)->mapWithKeys(function ($item) { return [$item->age => $item->total];   });

        /**
         * columns for above data set
         * AgeGroup Count Male Female
         * male	109	Young 0-15
            female	106	Young 0-15
            other	98	Young 0-15
            male	57	Youth 16-25
            female	80	Youth 16-25
            other	58	Youth 16-25
            male	205	Adult 26-59
            female	195	Adult 26-59
            other	193	Adult 26-59
         */
        $ageWiseData = [];
        foreach ($ageWiseDataResult as $row) {
            $ageGroup = $row->age_group;
            $gender = ucfirst($row->gender);
            $total = $row->total;

            if (!isset($ageWiseData[$ageGroup])) {
                $ageWiseData[$ageGroup] = ['Male' => 0, 'Female' => 0, 'Other' => 0];
            }

            $ageWiseData[$ageGroup][$gender] = $total;
        }

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
            ]
        ];

        return Inertia::render('Dashboard', [
            'title' => 'Dashboard',
            'statCards' => $statCards,
            'tableCards' => $tableCards,
            'ageWiseData' => $ageWiseData,
        ]);
    }
}
