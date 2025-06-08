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

        $ageWiseDataResult = DB::select("select t1.age, count(t1.age) as total
                                    from (
                                            select
                                                id, first_name, CASE
                                                    WHEN date_of_birth IS NOT NULL THEN TIMESTAMPDIFF(YEAR, date_of_birth, NOW())
                                                    ELSE '--'
                                                END as age
                                            from members
                                        ) as t1
                                    GROUP by
                                        t1.age");


        $ageWiseData = collect($ageWiseDataResult)->mapWithKeys(function ($item) { return [$item->age => $item->total];   });
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
                'title' => 'Age Wise',
                'data' => $ageWiseData,
                'icon' => 'i-heroicons-chart-pie',
                'bgClass' => 'bg-purple-500',
                'borderClass' => 'border-purple-800',
            ],
        ];

        return Inertia::render('Dashboard', [
            'title' => 'Dashboard',
            'statCards' => $statCards,
            'tableCards' => $tableCards,
        ]);
    }
}
