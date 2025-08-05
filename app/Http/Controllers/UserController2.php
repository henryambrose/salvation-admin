<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController2 extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('users/Index', [
            'users' => ['data' => []],
            'filters' => [],
            'fetchUrl' => '/users2',
        ]);
    }
} 