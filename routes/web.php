<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamTypeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AcademicSessionController;
use App\Http\Controllers\ExamResultController;
use App\Http\Controllers\EmployeeSalaryController;
use App\Http\Controllers\SalaryIncrementController;
use App\Http\Controllers\SalaryComponentController;
use App\Http\Controllers\ExaminerAssignmentController;
use App\Http\Controllers\MarksEntryController;
use App\Http\Controllers\SalaryGenerationController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeCategoryController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Student Management
    Route::middleware(['permission:view students'])->group(function () {
        Route::resource('students', StudentController::class);
    });

    // Class Management
    Route::middleware(['permission:view classes'])->group(function () {
        Route::resource('classes', ClassController::class);
    });

    // Result Management Routes
    Route::middleware(['auth'])->group(function () {
        // Add Subjects to Class
        Route::get('/class/add-subjects', [ResultController::class, 'addSubjectsForm'])->name('class.add-subjects.form');
        Route::post('/class/{classId}/add-subjects', [ResultController::class, 'addSubjectsToClass'])->name('class.add-subjects');
        
        // Marks Management
        Route::get('/marks/add', [ResultController::class, 'addMarksForm'])->name('marks.add.form');
        Route::post('/marks/add', [ResultController::class, 'addMarks'])->name('marks.add');
        
        // Marksheets
        Route::get('/marksheet/student/{studentId}', [ResultController::class, 'generateStudentMarksheet'])->name('marksheet.student');
        Route::get('/marksheet/class/{classId}', [ResultController::class, 'generateClassMarksheet'])->name('marksheet.class');
    });

    // Exam Management
    Route::middleware(['permission:view exams'])->group(function () {
        // Academic Sessions
        Route::resource('academic-sessions', AcademicSessionController::class);
        
        // Subjects with class assignments
        Route::resource('subjects', SubjectController::class);
        Route::post('subjects/{subject}/assign-class', [SubjectController::class, 'assignClass'])->name('subjects.assign-class');
        Route::delete('subjects/{subject}/remove-class/{class}', [SubjectController::class, 'removeClass'])->name('subjects.remove-class');
        
        // Exam Types
        Route::resource('exam-types', ExamTypeController::class);
        
        // Examiner Assignments
        Route::resource('examiner-assignments', ExaminerAssignmentController::class);
        
        // Exams
        Route::resource('exams', ExamController::class);
        Route::post('exams/{exam}/assign-subjects', [ExamController::class, 'assignSubjects'])->name('exams.assign-subjects');
        Route::post('exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');
        
        // Marks Entry
        Route::get('marks/entry', [MarksEntryController::class, 'showEntryForm'])->name('marks.entry');
        Route::post('marks/entry', [MarksEntryController::class, 'store'])->name('marks.store');
        Route::get('marks/batch-entry', [MarksEntryController::class, 'showBatchForm'])->name('marks.batch-entry');
        Route::post('marks/batch-entry', [MarksEntryController::class, 'storeBatch'])->name('marks.store-batch');
        
        // Exam Results
        Route::resource('exam-results', ExamResultController::class);
        Route::get('exam-results/exam/{exam}', [ExamResultController::class, 'examResults'])->name('exam-results.exam');
        Route::get('exam-results/student/{student}', [ExamResultController::class, 'studentResults'])->name('exam-results.student');
        Route::post('exam-results/calculate-ranks/{exam}', [ExamResultController::class, 'calculateRanks'])->name('exam-results.calculate-ranks');
        Route::get('exam-results/{exam}/summary', [ExamResultController::class, 'examSummary'])->name('exam-results.summary');
        Route::get('exam-results/{student}/marksheet', [ExamResultController::class, 'studentMarksheet'])->name('exam-results.marksheet');
    });

    // Result Management
    Route::middleware(['permission:view results'])->group(function () {
        Route::prefix('results')->name('results.')->group(function () {
            // Admin Routes
            Route::middleware(['role:super-admin|admin'])->group(function () {
                Route::get('/publish', [ResultController::class, 'publishIndex'])->name('publish');
                Route::post('/publish/{exam}', [ResultController::class, 'publishResults'])->name('publish-exam');
                Route::get('/classes', [ResultController::class, 'classesIndex'])->name('classes');
                Route::get('/analysis', [ResultController::class, 'analysisIndex'])->name('analysis');
            });

            // Examiner Routes
            Route::middleware(['role:examiner'])->group(function () {
                Route::get('/my-classes', [ResultController::class, 'myClassesIndex'])->name('my-classes');
                Route::get('/my-subjects', [ResultController::class, 'mySubjectsIndex'])->name('my-subjects');
            });

            // Student Routes
            Route::middleware(['role:student'])->group(function () {
                Route::get('/view', [ResultController::class, 'viewResults'])->name('view');
                Route::get('/download', [ResultController::class, 'downloadMarksheet'])->name('download');
            });
        });
    });

    // Report Management
    Route::middleware(['permission:view reports'])->group(function () {
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/students', [ReportController::class, 'studentReport'])->name('students');
            Route::get('/classes', [ReportController::class, 'classIndex'])->name('classes');
            Route::get('/class/{class}', [ReportController::class, 'classReport'])->name('class');
            Route::get('/subjects', [ReportController::class, 'subjectIndex'])->name('subjects');
            Route::get('/subject/{subject}', [ReportController::class, 'subjectShow'])->name('subject.show');
            Route::get('/exams', [ReportController::class, 'examIndex'])->name('exams');
            Route::get('/exam/{exam}', [ReportController::class, 'examShow'])->name('exam.show');
            
            // Download Reports
            Route::get('/download/student/{student}', [ReportController::class, 'downloadStudentReport'])->name('download.student');
            Route::get('/download/class/{class}', [ReportController::class, 'downloadClassReport'])->name('download.class');
            Route::get('/download/subject/{subject}', [ReportController::class, 'downloadSubjectReport'])->name('download.subject');
            Route::get('/download/exam/{exam}', [ReportController::class, 'downloadExamReport'])->name('download.exam');
        });
    });

   //Employee Management

    Route::prefix('account')->group(function () {
        Route::resource('account/employee', EmployeeController::class);
        Route::get('/account/employee', [EmployeeController::class, 'index'])->name('account.employee.index');
        Route::get('/account/employee/create', [EmployeeController::class, 'create'])->name('account.employee.create');
        Route::post('/account/employee/store', [EmployeeController::class, 'store'])->name('account.employee.store');
        Route::post('/account/employee/update', [EmployeeController::class, 'update'])->name('account.employee.update');
        Route::get('account/employee/{id}/edit', [EmployeeController::class, 'edit'])->name('account.employee.edit');
        Route::delete('account/employee/{id}', [EmployeeController::class, 'destroy'])->name('account.employee.destroy');


    });

    
    // Salary Management
  
Route::prefix('account/salary_management')->group(function () {
    Route::resource('employee_salary', EmployeeSalaryController::class);
    Route::get('/employee_salary', [EmployeeSalaryController::class, 'index'])->name('account.salary_management.employee_salary.index');
    Route::get('/employee_salary/create', [EmployeeSalaryController::class, 'create'])->name('account.salary_management.employee_salary.create');
    Route::post('/employee_salary', [EmployeeSalaryController::class, 'store'])->name('account.salary_management.employee_salary.store');
    Route::get('/employee_salary/{employeeSalary}', [EmployeeSalaryController::class, 'show'])->name('account.salary_management.employee_salary.show');
    Route::get('/employee_salary/{employeeSalary}/edit', [EmployeeSalaryController::class, 'edit'])->name('account.salary_management.employee_salary.edit');
    Route::put('/employee_salary/{employeeSalary}', [EmployeeSalaryController::class, 'update'])->name('account.salary_management.employee_salary.update');
    Route::delete('/employee_salary/{employeeSalary}', [EmployeeSalaryController::class, 'destroy'])->name('account.salary_management.employee_salary.destroy');
});


// Salary Increment
Route::prefix('account/salary_increment')->group(function () {
    Route::resource('salary_increment', SalaryIncrementController::class);
    Route::get('/salary_increment', [SalaryIncrementController::class, 'index'])->name('account.salary_management.salary_increment.index');
    Route::get('/salary_increment/create', [SalaryIncrementController::class, 'create'])->name('account.salary_management.salary_increment.create');
    Route::post('/salary_increment', [SalaryIncrementController::class, 'store'])->name('account.salary_management.salary_increment.store');
    Route::get('/salary_increment/{salaryIncrement}/edit', [SalaryIncrementController::class, 'edit'])->name('account.salary_management.salary_increment.edit');
    Route::put('/salary_increment/{salaryIncrement}', [SalaryIncrementController::class, 'update'])->name('account.salary_management.salary_increment.update');
    Route::delete('/salary_increment/{salaryIncrement}', [SalaryIncrementController::class, 'destroy'])->name('account.salary_management.salary_increment.destroy');    
}); 


// Salary Component
Route::prefix('account/salary_component')->group(function () {
    Route::resource('salary_component', SalaryComponentController::class);
    Route::get('/salary_component', [SalaryComponentController::class, 'index'])->name('account.salary_management.salary_component.index');
    Route::get('/salary_component/create', [SalaryComponentController::class, 'create'])->name('account.salary_management.salary_component.create');    
    Route::post('/salary_component', [SalaryComponentController::class, 'store'])->name('account.salary_management.salary_component.store');
    Route::get('/salary_component/{salaryComponent}/edit', [SalaryComponentController::class, 'edit'])->name('account.salary_management.salary_component.edit');
    Route::put('/salary_component/{salaryComponent}', [SalaryComponentController::class, 'update'])->name('account.salary_management.salary_component.update');
    Route::delete('/salary_component/{salaryComponent}', [SalaryComponentController::class, 'destroy'])->name('account.salary_management.salary_component.destroy');
}); 


// Salary Generation
    Route::prefix('account/generate_salary')->group(function () {
    Route::resource('generate_salary', SalaryGenerationController::class);
    Route::get('/generate_salary', [SalaryGenerationController::class, 'index'])->name('account.salary_management.generate_salary.index');
    Route::get('/generate_salary/create', [SalaryGenerationController::class, 'create'])->name('account.salary_management.generate_salary.create'); 
    Route::post('/generate_salary', [SalaryGenerationController::class, 'store'])->name('account.salary_management.generate_salary.store');
    Route::get('/generate_salary/{salaryGeneration}/edit', [SalaryGenerationController::class, 'edit'])->name('account.salary_management.generate_salary.edit');
    Route::put('/generate_salary/{salaryGeneration}', [SalaryGenerationController::class, 'update'])->name('account.salary_management.generate_salary.update');
    Route::delete('/generate_salary/{salaryGeneration}', [SalaryGenerationController::class, 'destroy'])->name('account.salary_management.generate_salary.destroy');
    Route::get('/generate-salary', [SalaryGenerationController::class, 'generate'])->name('account.salary_management.generate_salary.generate');  
});  


// Fee Management
Route::prefix('account/fee_management')->group(function () {
    Route::resource('fee_structure', FeeStructureController::class);
    Route::get('/fee_structure', [FeeStructureController::class, 'index'])->name('account.fee_management.fee_structure.index');
    Route::get('/fee_structure/create', [FeeStructureController::class, 'create'])->name('account.fee_management.fee_structure.create');        
    Route::post('/fee_structure', [FeeStructureController::class, 'store'])->name('account.fee_management.fee_structure.store');
    Route::get('/fee_structure/{feeStructure}/edit', [FeeStructureController::class, 'edit'])->name('account.fee_management.fee_structure.edit');
    Route::put('/fee_structure/{feeStructure}', [FeeStructureController::class, 'update'])->name('account.fee_management.fee_structure.update');
    Route::delete('/fee_structure/{feeStructure}', [FeeStructureController::class, 'destroy'])->name('account.fee_management.fee_structure.destroy');
}); 

    
// Fee Category
Route::prefix('account/fee_category')->group(function () {
    Route::resource('fee_category', FeeCategoryController::class);
    Route::get('/fee_category', [FeeCategoryController::class, 'index'])->name('account.fee_management.fee_category.index');
    Route::get('/fee_category/create', [FeeCategoryController::class, 'create'])->name('account.fee_management.fee_category.create');
    Route::post('/fee_category', [FeeCategoryController::class, 'store'])->name('account.fee_management.fee_category.store');
    Route::get('/fee_category/{feeCategory}/edit', [FeeCategoryController::class, 'edit'])->name('account.fee_management.fee_category.edit');
    Route::put('/fee_category/{feeCategory}', [FeeCategoryController::class, 'update'])->name('account.fee_management.fee_category.update');
    Route::delete('/fee_category/{feeCategory}', [FeeCategoryController::class, 'destroy'])->name('account.fee_management.fee_category.destroy');
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
});

require __DIR__.'/auth.php';
