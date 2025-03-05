<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ClassController extends Controller
{
    public function subjects(Classes $class)
    {
        if (!Gate::allows('enter marks')) {
            abort(403);
        }

        return response()->json($class->subjects);
    }
} 