<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController; 
use App\Http\Controllers\ClassController; 

// Default Route
Route::get('/', function () {
    return view('auth.login');
});

// Authentication Routes
Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup');
Route::post('/signup', [AuthController::class, 'signup']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Route
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
});

// Role routes
Route::resource('roles', RoleController::class);

// Permission routes
Route::resource('permissions', PermissionController::class);

// User routes
Route::resource('users', UserController::class);
// Student Routes
Route::resource('students', StudentController::class);
Route::resource('classes', ClassController::class);

Route::get('/admin/dashboard', [DashboardController::class, 'adminIndex'])->name('admin.dashboard')->middleware('role:Super Admin');
Route::get('/teacher/dashboard', [DashboardController::class, 'teacherIndex'])->name('teacher.dashboard')->middleware('role:Teacher');
Route::get('/student/dashboard', [DashboardController::class, 'studentIndex'])->name('student.dashboard')->middleware('role:Student');
