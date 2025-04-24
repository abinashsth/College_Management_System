<?php

use App\Http\Controllers\MarkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Marks Management Routes
|--------------------------------------------------------------------------
|
| These routes handle the management of student marks, including entry,
| verification, publication, and reporting functionality
|
*/

Route::middleware(['auth'])->group(function () {
    // Test route - should be accessible to all authenticated users
    Route::get('/marks/welcome', [MarkController::class, 'welcome'])->name('marks.welcome');
    
    // Dashboard and selection routes
    Route::get('/marks/dashboard', [MarkController::class, 'dashboard'])->name('marks.dashboard');
    Route::get('/marks/select', [MarkController::class, 'selectExamSubject'])->name('marks.select');
    Route::get('/marks', [MarkController::class, 'index'])->name('marks.index');
    
    // Mark entry routes
    Route::middleware(['permission:create marks|role:admin'])->group(function () {
        Route::get('/marks/create', [MarkController::class, 'create'])->name('marks.create');
        Route::post('/marks', [MarkController::class, 'store'])->name('marks.store');
        Route::get('/marks/create-bulk', [MarkController::class, 'createBulk'])->name('marks.createBulk');
        Route::post('/marks/store-bulk', [MarkController::class, 'storeBulk'])->name('marks.storeBulk');
        
        // Subject-specific marks entry
        Route::get('/marks/subject-entry', [MarkController::class, 'subjectEntry'])->name('marks.subjectEntry');
        Route::post('/marks/store-subject', [MarkController::class, 'storeSubjectMarks'])->name('marks.storeSubject');
    });
    
    // Mark view and edit routes
    Route::get('/marks/{mark}', [MarkController::class, 'show'])->name('marks.show');
    Route::middleware(['permission:edit marks|role:admin'])->group(function () {
        Route::get('/marks/{mark}/edit', [MarkController::class, 'edit'])->name('marks.edit');
        Route::put('/marks/{mark}', [MarkController::class, 'update'])->name('marks.update');
        Route::delete('/marks/{mark}', [MarkController::class, 'destroy'])->name('marks.destroy');
    });
    
    // Mark workflow routes
    Route::post('/marks/{mark}/submit', [MarkController::class, 'submit'])->name('marks.submit')->middleware('permission:create marks|role:admin');
    Route::middleware(['permission:verify marks|role:admin'])->group(function () {
        Route::post('/marks/{mark}/verify', [MarkController::class, 'verify'])->name('marks.verify');
        Route::post('/marks/verify-all', [MarkController::class, 'verifyAll'])->name('marks.verifyAll');
        Route::post('/marks/reject-verification', [MarkController::class, 'rejectVerification'])->name('marks.rejectVerification');
        Route::get('/marks/verify-interface', [MarkController::class, 'verifyInterface'])->name('marks.verifyInterface');
    });
    
    Route::middleware(['permission:publish marks|role:admin'])->group(function () {
        Route::post('/marks/{mark}/publish', [MarkController::class, 'publish'])->name('marks.publish');
        Route::post('/marks/publish-all', [MarkController::class, 'publishAll'])->name('marks.publishAll');
    });
    
    // Import/Export routes
    Route::middleware(['permission:create marks|role:admin'])->group(function () {
        Route::get('/marks/import', [MarkController::class, 'import'])->name('marks.import');
        Route::post('/marks/import', [MarkController::class, 'processImport'])->name('marks.processImport');
        Route::get('/marks/template', [MarkController::class, 'downloadTemplate'])->name('marks.downloadTemplate');
    });
    
    Route::get('/marks/export', [MarkController::class, 'export'])->name('marks.export');
    
    // Student marks report routes
    Route::get('/marks/student/{student}', [MarkController::class, 'studentMarks'])->name('marks.student');
    Route::get('/marks/student/{student}/export', [MarkController::class, 'exportStudentMarks'])->name('marks.student.export');
    
    // Analysis and reporting
    Route::get('/marks/analysis', [MarkController::class, 'analysis'])->name('marks.analysis');
    Route::get('/marks/reports', [MarkController::class, 'reports'])->name('marks.reports');
}); 