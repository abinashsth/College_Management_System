<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index()
    {
        return view('exam.index');
    }

    public function schedules()
    {
        return view('exam.schedules');
    }

    public function results()
    {
        return view('exam.results');
    }
}
