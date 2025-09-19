<?php

namespace Modules\Members\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Members\Http\Requests\StoreUserRequest;
use Modules\Members\Http\Requests\UpdateUserRequest;
use Modules\Members\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request): Response
    {

        $this->authorize('viewAny', User::class);

        $query = User::query();

        // Handle archived records
        if ($request->input('isArchived') === 'true') {
            $query->onlyTrashed();
        } else {
            $query->withoutTrashed();
        }

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with(['roles.permissions'])->paginate($request->get('perPage', 10))
            ->withQueryString();

        // Get all available roles for role assignment with permissions
        $roles = \Spatie\Permission\Models\Role::with('permissions')->orderBy('name')->get();

        return Inertia::render('users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'isArchived', 'perPage']),
            'fetchUrl' => '/users',
        ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);

        return Inertia::render('users/Show', [
            'user' => $user,
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('users/User');
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index');
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        return Inertia::render('users/User', [
            'user' => $user,
        ]);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Handle role updates if provided
        if ($request->input('roles')) {
            $user->syncRoles($request->input('roles'));
        }

        // For Inertia requests, return a proper Inertia response
        if (request()->header('X-Inertia')) {
            return back()->with('success', 'User updated successfully');
        }

        return redirect()->route('users.index');
    }

    public function destroy(Request $request, User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();

        // Preserve current state after deletion
        $page = $request->input('page', 1);
        $perPage = $request->input('perPage', 10);
        return redirect()->route('users.index', array_merge(
            $request->only(['search', 'sort', 'direction', 'isArchived']),
            [
                'page' => $page,
                'perPage' => $perPage,
            ]
        ))->with('success', 'User deleted successfully.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);

        $user->restore();

        return redirect()->route('users.index');
    }
}
