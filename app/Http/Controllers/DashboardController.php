<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'students' => Student::count(),
            'active_students' => Student::where('status', true)->count(),
            'inactive_students' => Student::where('status', false)->count(),
        ];

        return view('dashboard', compact('stats'));
    }
}
