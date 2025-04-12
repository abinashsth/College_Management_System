<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Classes;
use App\Models\AcademicSession;
use App\Exceptions\ExamException;
use App\Services\ExamScheduleService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExamErrorHandlingTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $examScheduleService;
    
    public function setUp(): void
    {
        parent::setUp();
        
        // Create permissions
        Permission::create(['name' => 'manage exam schedules']);
        Permission::create(['name' => 'view exams']);
        Permission::create(['name' => 'create exams']);
        Permission::create(['name' => 'edit exams']);
        Permission::create(['name' => 'delete exams']);
        
        // Create admin role and assign permissions
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'manage exam schedules',
            'view exams',
            'create exams',
            'edit exams',
            'delete exams'
        ]);
        
        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $this->admin->assignRole('Admin');
        
        $this->examScheduleService = app(ExamScheduleService::class);
    }
    
    /**
     * Test schedule conflict detection.
     *
     * @return void
     */
    public function testScheduleConflictDetection()
    {
        // Create necessary data for testing
        $class = Classes::create(['name' => 'Test Class']);
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS101']);
        $academicSession = AcademicSession::create([
            'name' => 'Test Session',
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
            'is_active' => true
        ]);
        $section = Section::create([
            'name' => 'Section A',
            'class_id' => $class->id
        ]);
        
        // Create an exam
        $exam = Exam::create([
            'title' => 'Test Exam',
            'description' => 'Test Exam Description',
            'exam_date' => now()->addDays(10)->format('Y-m-d'),
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_session_id' => $academicSession->id,
            'exam_type' => 'midterm',
            'duration_minutes' => 120,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'total_marks' => 100,
            'passing_marks' => 40,
            'created_by' => $this->admin->id,
            'is_active' => true
        ]);
        
        // Create an initial schedule
        $scheduleData = [
            'exam_id' => $exam->id,
            'section_id' => $section->id,
            'exam_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status' => 'scheduled',
            'created_by' => $this->admin->id
        ];
        
        $schedule = $this->examScheduleService->createSchedule($scheduleData);
        $this->assertDatabaseHas('exam_schedules', [
            'id' => $schedule->id,
            'exam_id' => $exam->id,
            'section_id' => $section->id
        ]);
        
        // Now try to create a conflicting schedule and expect an exception
        $conflictingScheduleData = [
            'exam_id' => $exam->id,
            'section_id' => $section->id,
            'exam_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '10:00:00', // Overlaps with existing schedule
            'end_time' => '12:00:00',
            'status' => 'scheduled',
            'created_by' => $this->admin->id
        ];
        
        $this->expectException(ExamException::class);
        $this->expectExceptionMessage('Scheduling conflict');
        
        $this->examScheduleService->createSchedule($conflictingScheduleData);
    }
    
    /**
     * Test validation for schedule end time coming after start time.
     *
     * @return void
     */
    public function testScheduleEndTimeAfterStartTimeValidation()
    {
        // Create necessary data for testing
        $class = Classes::create(['name' => 'Test Class']);
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS101']);
        $academicSession = AcademicSession::create([
            'name' => 'Test Session',
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
            'is_active' => true
        ]);
        $section = Section::create([
            'name' => 'Section A',
            'class_id' => $class->id
        ]);
        
        // Create an exam
        $exam = Exam::create([
            'title' => 'Test Exam',
            'description' => 'Test Exam Description',
            'exam_date' => now()->addDays(10)->format('Y-m-d'),
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_session_id' => $academicSession->id,
            'exam_type' => 'midterm',
            'duration_minutes' => 120,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'total_marks' => 100,
            'passing_marks' => 40,
            'created_by' => $this->admin->id,
            'is_active' => true
        ]);
        
        // Try to create a schedule with invalid times
        $invalidScheduleData = [
            'exam_id' => $exam->id,
            'section_id' => $section->id,
            'exam_date' => now()->addDays(10)->format('Y-m-d'),
            'start_time' => '11:00:00',
            'end_time' => '09:00:00', // End time before start time
            'status' => 'scheduled',
            'created_by' => $this->admin->id
        ];
        
        $this->expectException(ExamException::class);
        $this->expectExceptionMessage('Exam end time must be after start time');
        
        $this->examScheduleService->createSchedule($invalidScheduleData);
    }
    
    /**
     * Test exam scheduling API handling of conflicts.
     *
     * @return void
     */
    public function testExamScheduleControllerHandlesConflicts()
    {
        // Create necessary data for testing
        $class = Classes::create(['name' => 'Test Class']);
        $subject = Subject::create(['name' => 'Test Subject', 'code' => 'TS101']);
        $academicSession = AcademicSession::create([
            'name' => 'Test Session',
            'start_date' => now()->subMonths(6),
            'end_date' => now()->addMonths(6),
            'is_active' => true
        ]);
        $section = Section::create([
            'name' => 'Section A',
            'class_id' => $class->id
        ]);
        
        // Create an exam
        $exam = Exam::create([
            'title' => 'Test Exam',
            'description' => 'Test Exam Description',
            'exam_date' => now()->addDays(10)->format('Y-m-d'),
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'academic_session_id' => $academicSession->id,
            'exam_type' => 'midterm',
            'duration_minutes' => 120,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'total_marks' => 100,
            'passing_marks' => 40,
            'created_by' => $this->admin->id,
            'is_active' => true
        ]);
        
        // Create an initial schedule via controller
        $this->actingAs($this->admin)
            ->post(route('exam-schedules.store'), [
                'exam_id' => $exam->id,
                'section_id' => $section->id,
                'exam_date' => now()->addDays(10)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '11:00:00',
                'status' => 'scheduled',
            ]);
            
        $this->assertDatabaseCount('exam_schedules', 1);
        
        // Try to create a conflicting schedule via controller
        $response = $this->actingAs($this->admin)
            ->post(route('exam-schedules.store'), [
                'exam_id' => $exam->id,
                'section_id' => $section->id,
                'exam_date' => now()->addDays(10)->format('Y-m-d'),
                'start_time' => '10:00:00', // Overlaps with existing schedule
                'end_time' => '12:00:00',
                'status' => 'scheduled',
            ]);
            
        // Should redirect back with error
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        
        // Should still only have one schedule
        $this->assertDatabaseCount('exam_schedules', 1);
    }
    
    /**
     * Test transaction management during multistep operations.
     *
     * @return void
     */
    public function testTransactionManagementForGradeUpdates()
    {
        // Transaction management implementation ensures either all grades are updated
        // or none, if there's an error
        
        // Create test data...
        // This is a bit complex to test completely, but we could ensure the controller
        // wraps operations in transactions
        
        $this->markTestIncomplete(
            'This test should verify the transaction management of complex operations.'
        );
    }
} 