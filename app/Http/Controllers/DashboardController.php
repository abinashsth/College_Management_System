<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user(); // Get the currently authenticated user
        $roles = $user->roles; // Retrieve roles associated with the user
        $permissions = $user->permissions; // Retrieve permissions associated with the user

        return view('dashboard.index', compact('roles', 'permissions'));
    }
}
