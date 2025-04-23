<?php

use App\Http\Controllers\SubjectMaskController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Subject Mask Management Routes
|--------------------------------------------------------------------------
|
| These routes handle the management of subject masks for mark entry
|
*/

Route::middleware(['auth', 'permission:view marks'])->group(function () {
    // Subject masks CRUD routes
    Route::get('/masks', [SubjectMaskController::class, 'index'])->name('masks.index');
    
    // Create/Store routes with create permission
    Route::middleware(['permission:create marks'])->group(function () {
        Route::get('/masks/create', [SubjectMaskController::class, 'create'])->name('masks.create');
        Route::post('/masks', [SubjectMaskController::class, 'store'])->name('masks.store');
    });
    
    Route::get('/masks/{mask}', [SubjectMaskController::class, 'show'])->name('masks.show');
    
    // Edit/Update routes with edit permission
    Route::middleware(['permission:edit marks'])->group(function () {
        Route::get('/masks/{mask}/edit', [SubjectMaskController::class, 'edit'])->name('masks.edit');
        Route::put('/masks/{mask}', [SubjectMaskController::class, 'update'])->name('masks.update');
    });
    
    // Delete route with verify marks permission (higher level permission)
    Route::middleware(['permission:verify marks'])->group(function () {
        Route::delete('/masks/{mask}', [SubjectMaskController::class, 'destroy'])->name('masks.destroy');
    });
    
    // API route for getting mask by exam and subject
    Route::get('/api/masks', [SubjectMaskController::class, 'getMask'])->name('masks.get');
}); 