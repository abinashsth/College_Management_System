<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\ClassroomAllocationController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\CollegeSettingsController;
use App\Http\Controllers\AcademicStructureController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\FacultyDeanController;
use App\Http\Controllers\FacultyEventController;
use App\Http\Controllers\FacultyStaffController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentHeadController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExamScheduleController;
use App\Http\Controllers\ExamSupervisorController;
use App\Http\Controllers\ExamRuleController;
use App\Http\Controllers\ExamMaterialController;
use App\Http\Controllers\GradeSystemController;
use App\Http\Controllers\StudentRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Analytics Dashboards
    Route::middleware(['auth'])->group(function () {
        // Dashboard Management
        Route::get('/dashboards', [DashboardController::class, 'index'])->name('dashboard.index');
        Route::get('/dashboards/create', [DashboardController::class, 'create'])->name('dashboard.create');
        Route::post('/dashboards', [DashboardController::class, 'store'])->name('dashboard.store');
        Route::get('/dashboards/{id}', [DashboardController::class, 'showDashboard'])->name('dashboard.show');
        Route::get('/dashboards/{id}/edit', [DashboardController::class, 'edit'])->name('dashboard.edit');
        Route::put('/dashboards/{id}', [DashboardController::class, 'update'])->name('dashboard.update');
        Route::delete('/dashboards/{id}', [DashboardController::class, 'destroy'])->name('dashboard.destroy');
        
        // Dashboard Widgets
        Route::post('/dashboards/{dashboardId}/widgets', [DashboardController::class, 'addWidget'])->name('dashboard.widgets.add');
        Route::put('/dashboard-widgets/{instanceId}', [DashboardController::class, 'updateWidgetInstance'])->name('dashboard.widgets.update');
        Route::delete('/dashboard-widgets/{instanceId}', [DashboardController::class, 'removeWidget'])->name('dashboard.widgets.remove');
        Route::get('/dashboard-widgets/{instanceId}/data', [DashboardController::class, 'getWidgetData'])->name('dashboard.widgets.data');
    });

    // Reports
    Route::middleware(['auth'])->group(function () {
        // Report Management
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/my-reports', [ReportController::class, 'myReports'])->name('reports.my-reports');
        Route::get('/reports/search', [ReportController::class, 'search'])->name('reports.search');
        Route::get('/reports/create/{templateId}', [ReportController::class, 'create'])->name('reports.create');
        Route::post('/reports/generate/{templateId}', [ReportController::class, 'generate'])->name('reports.generate');
        Route::get('/reports/{id}', [ReportController::class, 'show'])->name('reports.show');
        Route::get('/reports/{id}/download', [ReportController::class, 'download'])->name('reports.download');
        Route::delete('/reports/{id}', [ReportController::class, 'destroy'])->name('reports.destroy');
        
        // Custom Reports
        Route::get('/reports/custom/builder', [ReportController::class, 'customReport'])->name('reports.custom');
        Route::post('/reports/custom/generate', [ReportController::class, 'generateCustomReport'])->name('reports.custom.generate');
        
        // Comparison Reports
        Route::get('/reports/comparison/builder', [ReportController::class, 'comparisonReport'])->name('reports.comparison');
        Route::post('/reports/comparison/generate', [ReportController::class, 'generateComparisonReport'])->name('reports.comparison.generate');
    });

    // Faculty Management
    Route::middleware(['permission:manage faculty'])->group(function () {
        Route::resource('faculties', FacultyController::class);
        Route::get('faculties/{faculty}/dashboard', [FacultyController::class, 'dashboard'])->name('faculties.dashboard');
        
        // Faculty Dean Management
        Route::resource('faculty-deans', FacultyDeanController::class);
        Route::get('faculties/{faculty}/deans/create', [FacultyDeanController::class, 'createForFaculty'])->name('faculties.deans.create');
        Route::post('faculties/{faculty}/deans', [FacultyDeanController::class, 'storeForFaculty'])->name('faculties.deans.store');
        
        // Faculty Staff Management
        Route::get('faculties/{faculty}/staff', [FacultyStaffController::class, 'index'])->name('faculties.staff.index');
        Route::get('faculties/{faculty}/staff/create', [FacultyStaffController::class, 'create'])->name('faculties.staff.create');
        Route::post('faculties/{faculty}/staff', [FacultyStaffController::class, 'store'])->name('faculties.staff.store');
        Route::delete('faculties/{faculty}/staff/{user}', [FacultyStaffController::class, 'destroy'])->name('faculties.staff.destroy');
        
        // Faculty Events Management
        Route::get('faculties/{faculty}/events', [FacultyEventController::class, 'index'])->name('faculties.events.index');
        Route::get('faculties/{faculty}/events/create', [FacultyEventController::class, 'create'])->name('faculties.events.create');
        Route::post('faculties/{faculty}/events', [FacultyEventController::class, 'store'])->name('faculties.events.store');
        Route::get('faculties/{faculty}/events/{event}/edit', [FacultyEventController::class, 'edit'])->name('faculties.events.edit');
        Route::put('faculties/{faculty}/events/{event}', [FacultyEventController::class, 'update'])->name('faculties.events.update');
        Route::delete('faculties/{faculty}/events/{event}', [FacultyEventController::class, 'destroy'])->name('faculties.events.destroy');
    });
    
    // Department Management
    Route::middleware(['permission:manage departments'])->group(function () {
        Route::resource('departments', DepartmentController::class);
        Route::get('departments/{department}/dashboard', [DepartmentController::class, 'dashboard'])->name('departments.dashboard');
        
        // Department Courses
        Route::get('departments/{department}/courses', [DepartmentController::class, 'courses'])->name('departments.courses');
        
        // Department Head Management
        Route::resource('department-heads', DepartmentHeadController::class);
        Route::get('departments/{department}/heads/create', [DepartmentHeadController::class, 'createForDepartment'])->name('departments.heads.create');
        Route::post('departments/{department}/heads', [DepartmentHeadController::class, 'storeForDepartment'])->name('departments.heads.store');
        
        // Department Teacher Management
        Route::get('departments/{department}/teachers', [DepartmentController::class, 'showTeachers'])->name('departments.teachers.index');
        Route::get('departments/{department}/teachers/assign', [DepartmentController::class, 'assignTeachersForm'])->name('departments.teachers.assign');
        Route::post('departments/{department}/teachers/assign', [DepartmentController::class, 'assignTeachers'])->name('departments.teachers.store');
        Route::delete('departments/{department}/teachers/{teacher}', [DepartmentController::class, 'removeTeacher'])->name('departments.teachers.remove');
    });

    // Student Management
    Route::middleware(['permission:view students'])->group(function () {
        Route::resource('students', StudentController::class);
        
        // Student Records Management
        Route::get('/student-records', [StudentRecordController::class, 'index'])->name('student-records.index');
        Route::get('/student-records/{student}', [StudentRecordController::class, 'show'])->name('student-records.show');
        Route::get('/student-records/{student}/create', [StudentRecordController::class, 'create'])->name('student-records.create');
        Route::post('/student-records/{student}', [StudentRecordController::class, 'store'])->name('student-records.store');
        Route::get('/student-records/{student}/export-pdf', [StudentRecordController::class, 'exportPDF'])->name('student-records.export-pdf');
        Route::get('/student-records/{student}/export-excel', [StudentRecordController::class, 'exportExcel'])->name('student-records.export-excel');
        Route::get('/student-records-import', [StudentRecordController::class, 'importForm'])->name('student-records.import-form');
        Route::post('/student-records-import', [StudentRecordController::class, 'import'])->name('student-records.import');
    });

    // Class Management
    Route::middleware(['permission:view classes'])->group(function () {
        Route::resource('classes', ClassController::class);
        // Class Course Management
        Route::get('classes/{class}/courses', [ClassController::class, 'manageCourses'])->name('classes.courses');
        Route::put('classes/{class}/courses', [ClassController::class, 'updateCourses'])->name('classes.update-courses');
    });
    
    // Section Management
    Route::middleware(['permission:view sections'])->group(function () {
        Route::resource('sections', SectionController::class);
    });
    
    // Classroom Allocation Management
    Route::middleware(['permission:view classroom allocations'])->group(function () {
        Route::resource('classroom-allocations', ClassroomAllocationController::class);
    });
    
    // Exam Management
    Route::middleware(['permission:view exams'])->group(function () {
        Route::resource('exams', ExamController::class);
        Route::get('/student-grades', [ExamController::class, 'studentGrades'])->name('student.grades');
    });

    // Account Management
    Route::middleware(['permission:view accounts'])->group(function () {
        Route::resource('accounts', AccountController::class);
    });


    // Fee Management Routes
Route::middleware(['auth', 'permission:manage fees'])->group(function () {
    Route::resource('fee-structures', FeeStructureController::class);
    Route::resource('fees', FeeController::class);
    Route::get('/fees', [FeeController::class, 'index'])->name('fees.index');
    Route::get('fees/history', [FeeController::class, 'history'])->name('fees.history');
    Route::post('fees/{fee}/record-payment', [FeeController::class, 'recordPayment'])->name('fees.record-payment');
});

    // Employee Management
    Route::middleware(['permission:view employees'])->group(function () {
            
        Route::resource('employee', EmployeeController::class); 
        
    } );

        //Salary Management
     Route::middleware(['permission:view salary'])->group(function () {
            
        Route::resource('salary', SalaryController::class);
        
    } );


    // Fee Structure Management
    Route::middleware(['permission:view fee structures'])->group(function () {
        Route::resource('fee-structures', FeeStructureController::class);
        // Route::get('fee-structures', [FeeStructureController::class,'index'])->name('fee-structures.index');
        // Route::get('fee-structures/create', [FeeStructureController::class, 'create'])->name('fee-structures.create');
        // Route::post('fee-structures', [FeeStructureController::class,'store'])->name('fee-structures.store');   
        // Route::get('fee-structures/{feeStructure}', [FeeStructureController::class, 'show'])->name('fee-structures.show');
        // Route::get('fee-structures/{feeStructure}/edit', [FeeStructureController::class, 'edit'])->name('fee-structures.edit');
        // Route::put('fee-structures/{feeStructure}', [FeeStructureController::class, 'update'])->name('fee-structures.update');
        
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

    // College Settings
    Route::middleware(['permission:manage settings'])->group(function () {
        // Configuration Dashboard
        Route::get('/settings/dashboard', [ConfigurationController::class, 'index'])->name('settings.dashboard');
        Route::get('/settings/export', [ConfigurationController::class, 'export'])->name('settings.export');
        Route::get('/settings/import', [ConfigurationController::class, 'showImport'])->name('settings.import');
        Route::post('/settings/import', [ConfigurationController::class, 'import'])->name('settings.import.process');
        
        // College Profile
        Route::get('/settings/college', [CollegeSettingsController::class, 'index'])->name('settings.college');
        Route::put('/settings/college', [CollegeSettingsController::class, 'update'])->name('settings.college.update');
        Route::delete('/settings/college/reset-logo', [CollegeSettingsController::class, 'resetLogo'])->name('settings.college.reset-logo');
        Route::get('/settings/college/export', [CollegeSettingsController::class, 'export'])->name('settings.college.export');
        Route::post('/settings/college/import', [CollegeSettingsController::class, 'import'])->name('settings.college.import');
        
        // Academic Structure
        Route::resource('settings/academic-structure', AcademicStructureController::class)->names([
            'index' => 'settings.academic-structure.index',
            'create' => 'settings.academic-structure.create',
            'store' => 'settings.academic-structure.store',
            'show' => 'settings.academic-structure.show',
            'edit' => 'settings.academic-structure.edit',
            'update' => 'settings.academic-structure.update',
            'destroy' => 'settings.academic-structure.destroy',
        ]);
        Route::get('settings/academic-structure-sync', [AcademicStructureController::class, 'synchronizeAll'])
            ->name('settings.academic-structure.sync');
        
        // Academic Years
        Route::resource('settings/academic-year', AcademicYearController::class)->names([
            'index' => 'settings.academic-year.index',
            'create' => 'settings.academic-year.create',
            'store' => 'settings.academic-year.store',
            'show' => 'settings.academic-year.show',
            'edit' => 'settings.academic-year.edit',
            'update' => 'settings.academic-year.update',
            'destroy' => 'settings.academic-year.destroy',
        ]);
        
        // Academic Sessions
        Route::get('/settings/academic-year/{academicYear}/sessions/create', [AcademicYearController::class, 'createSession'])
            ->name('settings.academic-year.sessions.create');
        Route::post('/settings/academic-year/{academicYear}/sessions', [AcademicYearController::class, 'storeSession'])
            ->name('settings.academic-year.sessions.store');
        Route::get('/settings/academic-year/{academicYear}/sessions/{session}', [AcademicYearController::class, 'showSession'])
            ->name('settings.academic-year.sessions.show');
        Route::get('/settings/academic-year/{academicYear}/sessions/{session}/edit', [AcademicYearController::class, 'editSession'])
            ->name('settings.academic-year.sessions.edit');
        Route::put('/settings/academic-year/{academicYear}/sessions/{session}', [AcademicYearController::class, 'updateSession'])
            ->name('settings.academic-year.sessions.update');
        Route::delete('/settings/academic-year/{academicYear}/sessions/{session}', [AcademicYearController::class, 'destroySession'])
            ->name('settings.academic-year.sessions.destroy');
        Route::post('/settings/academic-year/{academicYear}/sessions/{session}/set-current', [AcademicYearController::class, 'setCurrentSession'])
            ->name('settings.academic-year.sessions.set-current');
        
        // System Settings
        Route::resource('settings/system', SystemSettingsController::class)->names([
            'index' => 'settings.system.index',
            'create' => 'settings.system.create',
            'store' => 'settings.system.store',
            'show' => 'settings.system.show',
            'edit' => 'settings.system.edit',
            'update' => 'settings.system.update',
            'destroy' => 'settings.system.destroy',
        ]);
        Route::post('/settings/system/bulk-update', [SystemSettingsController::class, 'bulkUpdate'])
            ->name('settings.system.bulk-update');
        
        // Grade System Management
        Route::get('/settings/grade-systems', [GradeSystemController::class, 'index'])->name('admin.grade-systems.index');
        Route::get('/settings/grade-systems/create', [GradeSystemController::class, 'create'])->name('admin.grade-systems.create');
        Route::post('/settings/grade-systems', [GradeSystemController::class, 'store'])->name('admin.grade-systems.store');
        Route::get('/settings/grade-systems/{gradeSystem}', [GradeSystemController::class, 'show'])->name('admin.grade-systems.show');
        Route::get('/settings/grade-systems/{gradeSystem}/edit', [GradeSystemController::class, 'edit'])->name('admin.grade-systems.edit');
        Route::put('/settings/grade-systems/{gradeSystem}', [GradeSystemController::class, 'update'])->name('admin.grade-systems.update');
        Route::delete('/settings/grade-systems/{gradeSystem}', [GradeSystemController::class, 'destroy'])->name('admin.grade-systems.destroy');
        Route::post('/settings/grade-systems/{gradeSystem}/set-default', [GradeSystemController::class, 'setDefault'])->name('admin.grade-systems.set-default');
        
        // Grade Scale Management
        Route::get('/settings/grade-systems/{gradeSystem}/scales', [GradeSystemController::class, 'scales'])->name('admin.grade-systems.scales');
        Route::get('/settings/grade-systems/{gradeSystem}/scales/create', [GradeSystemController::class, 'createScale'])->name('admin.grade-systems.scales.create');
        Route::post('/settings/grade-systems/{gradeSystem}/scales', [GradeSystemController::class, 'storeScale'])->name('admin.grade-systems.scales.store');
        Route::get('/settings/grade-systems/{gradeSystem}/scales/{scale}/edit', [GradeSystemController::class, 'editScale'])->name('admin.grade-systems.scales.edit');
        Route::put('/settings/grade-systems/{gradeSystem}/scales/{scale}', [GradeSystemController::class, 'updateScale'])->name('admin.grade-systems.scales.update');
        Route::delete('/settings/grade-systems/{gradeSystem}/scales/{scale}', [GradeSystemController::class, 'destroyScale'])->name('admin.grade-systems.scales.destroy');
    });

    // Activity Logs
    Route::middleware(['permission:view activity logs'])->group(function () {
        Route::get('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{activityLog}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-logs.show');
        Route::delete('/activity-logs', [App\Http\Controllers\ActivityLogController::class, 'clear'])
            ->name('activity-logs.clear')
            ->middleware('permission:clear activity logs');
    });

    // Program & Course Management
    Route::middleware(['permission:manage programs'])->group(function () {
        // Programs
        Route::resource('programs', ProgramController::class);
        Route::get('/programs/{program}/dashboard', [ProgramController::class, 'dashboard'])->name('programs.dashboard');
        Route::get('/programs/{program}/students', [ProgramController::class, 'students'])->name('programs.students');
        Route::get('/programs/{program}/courses', [ProgramController::class, 'courses'])->name('programs.courses');
        Route::post('/programs/{program}/courses', [ProgramController::class, 'addCourses'])->name('programs.courses.add');
        Route::delete('/programs/{program}/courses/{course}', [ProgramController::class, 'removeCourse'])->name('programs.courses.remove');
        
        // Courses
        Route::resource('courses', CourseController::class);
        Route::get('/courses/{course}/prerequisites', [CourseController::class, 'managePrerequisites'])->name('courses.prerequisites');
        Route::post('/courses/{course}/prerequisites', [CourseController::class, 'updatePrerequisites'])->name('courses.prerequisites.update');
        Route::get('/courses/{course}/programs', [CourseController::class, 'managePrograms'])->name('courses.programs');
        Route::post('/courses/{course}/programs', [CourseController::class, 'updatePrograms'])->name('courses.programs.update');
        
        // Subjects
        Route::resource('subjects', SubjectController::class);
        Route::get('/subjects/{subject}/prerequisites', [SubjectController::class, 'managePrerequisites'])->name('subjects.prerequisites');
        Route::post('/subjects/{subject}/prerequisites', [SubjectController::class, 'updatePrerequisites'])->name('subjects.prerequisites.update');
        Route::get('/subjects/{subject}/teachers', [SubjectController::class, 'manageTeachers'])->name('subjects.teachers');
        Route::post('/subjects/{subject}/teachers', [SubjectController::class, 'updateTeachers'])->name('subjects.teachers.update');
        Route::get('/subjects/{subject}/courses', [SubjectController::class, 'manageCourses'])->name('subjects.courses');
        Route::post('/subjects/{subject}/courses', [SubjectController::class, 'updateCourses'])->name('subjects.courses.update');
        Route::get('/subjects/{subject}/syllabus', [SubjectController::class, 'manageSyllabus'])->name('subjects.syllabus');
        Route::post('/subjects/{subject}/syllabus', [SubjectController::class, 'updateSyllabus'])->name('subjects.syllabus.update');
        Route::get('/subjects/import', [SubjectController::class, 'importForm'])->name('subjects.import');
        Route::post('/subjects/import', [SubjectController::class, 'import'])->name('subjects.import.process');
        Route::get('/subjects/export', [SubjectController::class, 'export'])->name('subjects.export');
    });

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes Group
Route::middleware(['auth', 'verified', 'role:Super Admin|Admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');

    // User Management (Example)
    // Route::resource('users', UserController::class);

    // Student Management
    Route::resource('students', StudentController::class);

    // Add other admin routes here...
});

// Test routes for exam system
Route::get('/exam-test', [App\Http\Controllers\ExamTestController::class, 'index']);
Route::get('/exam-test/relationships', [App\Http\Controllers\ExamTestController::class, 'testRelationships']);
Route::get('/exam-test/controller', [App\Http\Controllers\ExamTestController::class, 'testController']);

/*
 * Exam Management Routes
 */
Route::middleware(['auth'])->group(function () {
    // Exam Routes
    Route::resource('exams', ExamController::class);
    Route::post('exams/{exam}/enroll-students', [ExamController::class, 'enrollStudents'])->name('exams.enroll-students');
    Route::put('exams/{exam}/toggle-status', [ExamController::class, 'toggleStatus'])->name('exams.toggle-status');
    Route::put('exams/{exam}/toggle-published', [ExamController::class, 'togglePublished'])->name('exams.toggle-published');
    
    // Exam Schedule Routes
    Route::resource('exam-schedules', ExamScheduleController::class);
    Route::get('exams/{exam}/schedules', [ExamScheduleController::class, 'examSchedules'])->name('exam.schedules');
    Route::put('exam-schedules/{examSchedule}/update-status', [ExamScheduleController::class, 'updateStatus'])->name('exam-schedules.update-status');
    
    // Exam Supervisor Routes
    Route::resource('exam-supervisors', ExamSupervisorController::class)->except(['create', 'edit']);
    Route::get('exam-supervisors/my-supervisions', [ExamSupervisorController::class, 'mySupervisionsAssignments'])->name('exam-supervisors.my-supervisions');
    Route::put('exam-supervisors/{supervisor}/confirm', [ExamSupervisorController::class, 'confirm'])->name('exam-supervisors.confirm');
    Route::put('exam-supervisors/{supervisor}/mark-attended', [ExamSupervisorController::class, 'markAttended'])->name('exam-supervisors.mark-attended');
    
    // Exam Rule Routes
    Route::resource('exam-rules', ExamRuleController::class);
    Route::get('exams/{exam}/rules', [ExamRuleController::class, 'examRules'])->name('exam.rules');
    Route::put('exam-rules/{rule}/toggle-status', [ExamRuleController::class, 'toggleStatus'])->name('exam-rules.toggle-status');
    Route::post('exam-rules/update-order', [ExamRuleController::class, 'updateOrder'])->name('exam-rules.update-order');
    
    // Exam Material Routes
    Route::resource('exam-materials', ExamMaterialController::class);
    Route::get('exams/{exam}/materials', [ExamMaterialController::class, 'examMaterials'])->name('exam.materials');
    Route::get('exam-materials/{material}/download', [ExamMaterialController::class, 'download'])->name('exam-materials.download');
    Route::put('exam-materials/{material}/approve', [ExamMaterialController::class, 'approve'])->name('exam-materials.approve');
    Route::put('exam-materials/{material}/toggle-status', [ExamMaterialController::class, 'toggleStatus'])->name('exam-materials.toggle-status');
});

// Results routes
Route::middleware(['auth'])->prefix('results')->name('results.')->group(function () {
    Route::get('/', [App\Http\Controllers\ResultController::class, 'index'])->name('index');
    Route::get('/process', [App\Http\Controllers\ResultController::class, 'process'])->name('process');
    Route::post('/process-section', [App\Http\Controllers\ResultController::class, 'processSection'])->name('process-section');
    Route::post('/verify', [App\Http\Controllers\ResultController::class, 'verify'])->name('verify');
    Route::post('/publish', [App\Http\Controllers\ResultController::class, 'publish'])->name('publish');
    Route::get('/{result}', [App\Http\Controllers\ResultController::class, 'show'])->name('show');
    Route::get('/student/{student}/{exam}', [App\Http\Controllers\ResultController::class, 'showStudentResult'])->name('student');
    Route::get('/analysis/{exam}', [App\Http\Controllers\ResultController::class, 'analysis'])->name('analysis');
    Route::get('/export/pdf/{result}', [App\Http\Controllers\ResultController::class, 'exportPdf'])->name('export.pdf');
    Route::get('/export/excel', [App\Http\Controllers\ResultController::class, 'exportExcel'])->name('export.excel');
});

/*
 * Finance & Fee Management Routes
 */
Route::middleware(['auth'])->prefix('finance')->name('finance.')->group(function () {
    // Finance Dashboard
    Route::get('/dashboard', [App\Http\Controllers\FinanceController::class, 'dashboard'])->name('dashboard');
    Route::get('/reports', [App\Http\Controllers\FinanceController::class, 'report'])->name('reports');
});

// Fee Categories Routes
Route::middleware(['auth', 'permission:manage finances'])->group(function () {
    Route::resource('fee-categories', App\Http\Controllers\FeeCategoryController::class);
});

// Fee Types Routes
Route::middleware(['auth', 'permission:manage finances'])->group(function () {
    Route::resource('fee-types', App\Http\Controllers\FeeTypeController::class);
});

// Fee Allocations Routes
Route::middleware(['auth', 'permission:manage finances'])->group(function () {
    Route::resource('fee-allocations', App\Http\Controllers\FeeAllocationController::class);
});

// Invoice Routes
Route::middleware(['auth', 'permission:manage finances'])->group(function () {
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    Route::get('/invoices/{invoice}/print', [App\Http\Controllers\InvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/generate-invoices', [App\Http\Controllers\InvoiceController::class, 'generateBulk'])->name('invoices.generate-bulk');
    Route::post('/generate-invoices', [App\Http\Controllers\InvoiceController::class, 'processBulk'])->name('invoices.process-bulk');
});

// Payment Routes
Route::middleware(['auth', 'permission:manage finances'])->group(function () {
    Route::resource('payments', App\Http\Controllers\PaymentController::class);
    Route::get('/invoices/{invoice}/make-payment', [App\Http\Controllers\PaymentController::class, 'create'])->name('invoices.make-payment');
    Route::get('/payments/{payment}/receipt', [App\Http\Controllers\PaymentController::class, 'printReceipt'])->name('payments.receipt');
});

// Scholarship Routes
Route::middleware(['auth', 'permission:manage finances'])->group(function () {
    Route::resource('scholarships', App\Http\Controllers\ScholarshipController::class);
    Route::get('/scholarships/{scholarship}/assign', [App\Http\Controllers\ScholarshipController::class, 'assignForm'])->name('scholarships.assign-form');
    Route::post('/scholarships/{scholarship}/assign', [App\Http\Controllers\ScholarshipController::class, 'assign'])->name('scholarships.assign');
    Route::delete('/student-scholarships/{studentScholarship}', [App\Http\Controllers\ScholarshipController::class, 'removeAssignment'])->name('student-scholarships.destroy');
});

// Public admission application routes
Route::get('/apply', [App\Http\Controllers\AdmissionController::class, 'apply'])->name('admissions.apply');
Route::post('/apply/submit', [App\Http\Controllers\AdmissionController::class, 'submitApplication'])->name('admissions.submit');
Route::get('/apply/thanks', [App\Http\Controllers\AdmissionController::class, 'thanks'])->name('admissions.thanks');

// Admin admission management routes
Route::middleware(['auth', 'permission:manage students'])->prefix('admissions')->name('admissions.')->group(function () {
    Route::get('/', [App\Http\Controllers\AdmissionController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\AdmissionController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\AdmissionController::class, 'store'])->name('store');
    Route::get('/{application}', [App\Http\Controllers\AdmissionController::class, 'show'])->name('show');
    Route::get('/{application}/edit', [App\Http\Controllers\AdmissionController::class, 'edit'])->name('edit');
    Route::put('/{application}', [App\Http\Controllers\AdmissionController::class, 'update'])->name('update');
    Route::delete('/{application}', [App\Http\Controllers\AdmissionController::class, 'destroy'])->name('destroy');
    Route::get('/{application}/verify-documents', [App\Http\Controllers\AdmissionController::class, 'verifyDocuments'])->name('verify-documents');
    Route::post('/{application}/verify-documents', [App\Http\Controllers\AdmissionController::class, 'processDocumentVerification'])->name('process-document-verification');
    Route::post('/{application}/generate-id', [App\Http\Controllers\AdmissionController::class, 'generateId'])->name('generate-id');
    Route::post('/{application}/admit', [App\Http\Controllers\AdmissionController::class, 'admit'])->name('admit');
    Route::post('/{application}/reject', [App\Http\Controllers\AdmissionController::class, 'reject'])->name('reject');
});

require __DIR__.'/auth.php';
require __DIR__.'/marks.php';
