<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Classes;
use App\Models\Student;
use Illuminate\Http\Request;

class MarksheetController extends Controller
{
    public function index()
    {
        $exams = Exam::all();
        $classes = Classes::all();
        return view('exams.marksheet.index', compact('exams', 'classes'));
    }
}
