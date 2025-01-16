<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use App\Http\Middleware\RoleMiddleware;

class ExamController extends Controller implements HasMiddleware
{
    public static function middleware(): array 
    {
        return ['role:Student'];
    }
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
