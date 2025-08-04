<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'church_code' => config('app.church_code'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
                'permissions' => $request->user() ? $request->user()->getAllPermissions()->pluck('name') : [],
                'roles' => $request->user() ? $request->user()->getRoleNames() : [],
            ],
            'modules' => Module::where('is_active', true)
                ->orderBy('sort_order')
                ->get()
                ->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'name' => $module->name,
                        'icon' => $module->icon,
                        'slug' => $module->slug,
                        'route' => $module->route,
                        'actions' => $module->actions->map(function ($action) {
                            return [
                                'id' => $action->id,
                                'name' => $action->name,
                                'slug' => $action->slug,
                                'icon' => $action->icon,
                            ];
                        }),
                    ];
                }),
            'ziggy' => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
                'info'    => fn () => $request->session()->get('info'),
            ],
        ];
    }
}
