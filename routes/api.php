<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ExamController;
use App\Http\Controllers\API\ResultController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\Api\ClassController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Exam Management Routes
    Route::prefix('exams')->group(function () {
        Route::get('/', [ExamController::class, 'index']);
        Route::post('/', [ExamController::class, 'store']);
        Route::get('/{exam}', [ExamController::class, 'show']);
        Route::put('/{exam}', [ExamController::class, 'update']);
        Route::delete('/{exam}', [ExamController::class, 'destroy']);
        Route::post('/{exam}/results', [ExamController::class, 'storeResults']);
    });

    // Result Management Routes
    Route::prefix('results')->group(function () {
        Route::get('/student/{student}', [ResultController::class, 'studentResults']);
        Route::get('/class/{class}', [ResultController::class, 'classResults']);
        Route::get('/exam/{exam}', [ResultController::class, 'examResults']);
    });

    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/student/{student}', [ReportController::class, 'studentReportApi']);
    });

    // Class subjects
    Route::get('/classes/{class}/subjects', [ClassController::class, 'subjects']);

    // Exam routes
    Route::get('/exams/get-exam', [ExamController::class, 'getExam']);
});