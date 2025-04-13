<?php

use App\Http\Controllers\MarkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Marks Management Routes
|--------------------------------------------------------------------------
|
| Routes for managing student marks, including entry, verification, and publishing
|
*/

// Mark Management Routes
Route::middleware(['auth'])->group(function () {
    // List and selection routes
    Route::get('/marks', [MarkController::class, 'index'])->name('marks.index');
    Route::get('/marks/select', [MarkController::class, 'select'])->name('marks.select');
    
    // Mark entry routes
    Route::get('/marks/create', [MarkController::class, 'create'])->name('marks.create');
    Route::post('/marks', [MarkController::class, 'store'])->name('marks.store');
    Route::get('/marks/create-bulk', [MarkController::class, 'createBulk'])->name('marks.createBulk');
    Route::post('/marks/store-bulk', [MarkController::class, 'storeBulk'])->name('marks.storeBulk');
    
    // Show and update individual marks
    Route::get('/marks/{mark}', [MarkController::class, 'show'])->name('marks.show');
    Route::get('/marks/{mark}/edit', [MarkController::class, 'edit'])->name('marks.edit');
    Route::put('/marks/{mark}', [MarkController::class, 'update'])->name('marks.update');
    Route::delete('/marks/{mark}', [MarkController::class, 'destroy'])->name('marks.destroy');
    
    // Submission, verification, and publishing
    Route::post('/marks/{mark}/submit', [MarkController::class, 'submitMarks'])->name('marks.submit');
    Route::post('/marks/{mark}/verify', [MarkController::class, 'verify'])->name('marks.verify');
    Route::post('/marks/{mark}/publish', [MarkController::class, 'publish'])->name('marks.publish');
    
    // Bulk operations
    Route::post('/marks/verify-all', [MarkController::class, 'verifyAll'])->name('marks.verifyAll');
    Route::post('/marks/publish-all', [MarkController::class, 'publishAll'])->name('marks.publishAll');
    Route::post('/marks/reject-verification', [MarkController::class, 'rejectVerification'])->name('marks.rejectVerification');
    
    // Import/Export routes
    Route::get('/marks/import', [MarkController::class, 'import'])->name('marks.import');
    Route::post('/marks/import', [MarkController::class, 'processImport'])->name('marks.processImport');
    Route::get('/marks/import/form', [MarkController::class, 'showImportForm'])->name('marks.showImportForm');
    Route::get('/marks/export', [MarkController::class, 'export'])->name('marks.export');
    Route::get('/marks/template', [MarkController::class, 'downloadTemplate'])->name('marks.downloadTemplate');
    Route::get('/marks/export-template', [MarkController::class, 'exportTemplate'])->name('marks.exportTemplate');
    
    // Verification interface
    Route::get('/marks/verify-interface', [MarkController::class, 'verifyInterface'])->name('marks.verifyInterface');
}); 