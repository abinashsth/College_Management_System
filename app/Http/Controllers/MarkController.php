<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Exam;
use App\Models\Classes;
use Illuminate\Http\Request;

class MarkController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        $classes = Classes::all();
        return view('exams.marks.index', compact('exams', 'classes'));
    }
}
