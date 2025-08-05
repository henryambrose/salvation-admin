<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;

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

        $users = $query->paginate($request->get('perPage', 10))
                       ->withQueryString();
     
        return Inertia::render('users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'isArchived', 'perPage']),
            'fetchUrl' => '/users',
        ]);
    }

    public function show(User $user): Response
    {
        $this->authorize('view', $user);
        
        return Inertia::render('users/Show', [
            'user' => $user
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
            'user' => $user
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

        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        
        $user->delete();

        return redirect()->route('users.index');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $this->authorize('restore', $user);
        
        $user->restore();

        return redirect()->route('users.index');
    }
} 