<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\ClassesController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\ExamController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Academic Management Routes
    Route::resource('sessions', SessionController::class);
    Route::resource('courses', CourseController::class);
    Route::resource('faculties', FacultyController::class);
    Route::resource('classes', ClassesController::class);
    Route::resource('schools', SchoolController::class);
    
    // Student Management Routes
    Route::resource('students', StudentController::class);
    
    // Subject Management Routes
    Route::resource('subjects', SubjectController::class);
    
    // Finance Management Routes
    Route::resource('ledgers', LedgerController::class);
    Route::get('ledgers/student/{student}', [LedgerController::class, 'studentSummary'])->name('ledgers.student-summary');

    // Account Management
    Route::middleware(['permission:view accounts'])->group(function () {
        Route::resource('accounts', AccountController::class);
    });

    // User Management
    Route::middleware(['permission:view users'])->group(function () {
        Route::resource('users', UserController::class);
        Route::get('/change-password', [UserController::class, 'showChangePasswordForm'])->name('change.password.form');
        Route::post('/change-password', [UserController::class, 'changePassword'])->name('change.password');
    });

    // Role Management
    Route::middleware(['permission:view roles'])->group(function () {
        Route::resource('roles', RoleController::class);
    });

    // Permission Management
    Route::middleware(['permission:view permissions'])->group(function () {
        Route::resource('permissions', PermissionController::class);
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Exam Management Routes
    Route::resource('exams', ExamController::class);
    Route::get('exams/view', [ExamController::class, 'view'])->name('exams.view');
    // Route::get('exams/{exam}/enter-marks', [ExamController::class, 'enterMarks'])->name('exams.enter-marks');
    // Route::get('exams/{exam}/view-results', [ExamController::class, 'viewResults'])->name('exams.view-results');
});

require __DIR__.'/auth.php';
