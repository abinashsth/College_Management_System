<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Classes;
use Illuminate\Http\Request;

class TabulationController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        $classes = Classes::all();
        return view('exams.tabulation.index', compact('exams', 'classes'));
    }
}
