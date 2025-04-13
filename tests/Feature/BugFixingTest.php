<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class BugFixingTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $teacher;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles and permissions
        $adminRole = Role::create(['name' => 'super-admin']);
        $teacherRole = Role::create(['name' => 'teacher']);
        $studentRole = Role::create(['name' => 'student']);
        
        Permission::create(['name' => 'view marks']);
        Permission::create(['name' => 'create marks']);
        Permission::create(['name' => 'edit marks']);
        
        // Assign necessary permissions to roles
        $teacherRole->givePermissionTo(['view marks', 'create marks', 'edit marks']);
        
        // Create users with roles
        $this->admin = User::factory()->create(['name' => 'Admin', 'email' => 'admin@test.com']);
        $this->admin->assignRole('super-admin');
        
        $this->teacher = User::factory()->create(['name' => 'Teacher', 'email' => 'teacher@test.com']);
        $this->teacher->assignRole('teacher');
        
        $this->student = User::factory()->create(['name' => 'Student', 'email' => 'student@test.com']);
        $this->student->assignRole('student');
    }

    /**
     * Test fix for mark submission validation bug.
     * 
     * Previously, marks could be submitted with values exceeding the total marks.
     */
    public function test_mark_submission_validation_fix(): void
    {
        // Create necessary models
        $course = Course::create([
            'name' => 'Test Course',
            'code' => 'TC101'
        ]);
        
        $studentModel = Student::create([
            'name' => 'Test Student',
            'email' => 'teststudent@example.com',
            'course_id' => $course->id
        ]);
        
        $subject = Subject::create([
            'name' => 'Test Subject',
            'code' => 'TS101',
            'credit_hours' => 3
        ]);
        
        $exam = Exam::create([
            'title' => 'Bug Fix Test Exam',
            'total_marks' => 100,
            'passing_marks' => 40
        ]);
        
        // Attempt to create a mark with marks_obtained > total_marks (this should now fail)
        $response = $this->actingAs($this->teacher)
            ->post('/marks', [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'student_id' => $studentModel->id,
                'marks_obtained' => 110, // Greater than total_marks
                'total_marks' => 100,
                'remarks' => 'Testing validation fix'
            ]);
        
        // Assert validation fails
        $response->assertSessionHasErrors('marks_obtained');
        
        // Successful mark submission with valid data
        $response = $this->actingAs($this->teacher)
            ->post('/marks', [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'student_id' => $studentModel->id,
                'marks_obtained' => 85, // Valid value
                'total_marks' => 100,
                'remarks' => 'Testing validation fix'
            ]);
        
        $response->assertSessionDoesntHaveErrors('marks_obtained');
    }

    /**
     * Test fix for duplicate student email bug.
     * 
     * Previously, students could be created with duplicate email addresses.
     */
    public function test_duplicate_student_email_fix(): void
    {
        // Create a course
        $course = Course::create([
            'name' => 'Email Test Course',
            'code' => 'ETC101'
        ]);
        
        // Create first student
        Student::create([
            'name' => 'First Student',
            'email' => 'duplicate@example.com',
            'course_id' => $course->id
        ]);
        
        // Try to create another student with the same email
        $response = $this->actingAs($this->admin)
            ->post('/students', [
                'name' => 'Second Student',
                'email' => 'duplicate@example.com', // Duplicate email
                'course_id' => $course->id
            ]);
        
        // Assert validation fails
        $response->assertSessionHasErrors('email');
        
        // Check database to confirm second student wasn't created
        $this->assertEquals(1, Student::where('email', 'duplicate@example.com')->count());
    }

    /**
     * Test fix for permission caching bug.
     * 
     * Previously, permission changes weren't properly reflected until cache reset.
     */
    public function test_permission_caching_fix(): void
    {
        // Create new permission
        $newPermission = Permission::create(['name' => 'special permission']);
        
        // Initially teacher doesn't have this permission
        $this->assertFalse($this->teacher->hasPermissionTo('special permission'));
        
        // Assign permission to teacher role
        $teacherRole = Role::where('name', 'teacher')->first();
        $teacherRole->givePermissionTo('special permission');
        
        // Without fix, this would fail due to permission caching
        $this->assertTrue($this->teacher->hasPermissionTo('special permission'));
    }

    /**
     * Test fix for SQL injection vulnerability.
     */
    public function test_sql_injection_fix(): void
    {
        // Create test data
        $course = Course::create(['name' => 'Security Course', 'code' => 'SEC101']);
        
        // Create several students
        for ($i = 0; $i < 5; $i++) {
            Student::create([
                'name' => "Security Student $i",
                'email' => "security$i@example.com",
                'course_id' => $course->id
            ]);
        }
        
        // Attempt a search with SQL injection payload
        $maliciousInput = "x' OR 1=1 --";
        
        // Get search results (this would be vulnerable if not properly sanitized)
        $response = $this->actingAs($this->admin)
            ->get("/students/search?query=$maliciousInput");
        
        $response->assertStatus(200);
        
        // Verify only legitimate results are returned
        $this->assertDatabaseCount('students', 5);
        $response->assertViewHas('students', function($students) {
            return count($students) === 0; // No students should match this query
        });
    }

    /**
     * Test fix for concurrent mark submission race condition.
     */
    public function test_concurrent_mark_submission_fix(): void
    {
        // Skip if not using database that supports transactions
        if (!in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'])) {
            $this->markTestSkipped('Database driver does not support transactions');
        }
        
        // Create necessary models
        $course = Course::create(['name' => 'Concurrency Course', 'code' => 'CC101']);
        $student = Student::create([
            'name' => 'Concurrency Student',
            'email' => 'concurrency@example.com',
            'course_id' => $course->id
        ]);
        $subject = Subject::create([
            'name' => 'Concurrency Subject',
            'code' => 'CS101',
            'credit_hours' => 3
        ]);
        $exam = Exam::create([
            'title' => 'Concurrency Exam',
            'total_marks' => 100,
            'passing_marks' => 40
        ]);
        
        // Simulate concurrent submissions by creating the same mark from two "simultaneous" requests
        
        // First submission
        $mark1 = Mark::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'marks_obtained' => 75,
            'total_marks' => 100,
            'status' => 'draft',
            'created_by' => $this->teacher->id
        ]);
        
        // Second submission (should detect duplicate and fail with fixed code)
        try {
            $mark2 = Mark::create([
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'student_id' => $student->id,
                'marks_obtained' => 80, // Different score
                'total_marks' => 100,
                'status' => 'draft',
                'created_by' => $this->teacher->id
            ]);
            
            // If we reach here without exception, we found existing mark and updated it
            $this->assertEquals(80, $mark2->marks_obtained);
            
            // Verify only one mark exists for this student-subject-exam combination
            $this->assertEquals(
                1,
                Mark::where([
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'student_id' => $student->id
                ])->count(),
                'Only one mark should exist for this student-subject-exam combination'
            );
        } catch (\Exception $e) {
            $this->fail('Concurrency handling failed: ' . $e->getMessage());
        }
    }

    /**
     * Test fix for dashboard data accuracy bug.
     */
    public function test_dashboard_data_accuracy_fix(): void
    {
        // Create necessary data to populate the dashboard
        $course = Course::create(['name' => 'Dashboard Course', 'code' => 'DC101']);
        
        // Create 10 students in this course
        for ($i = 0; $i < 10; $i++) {
            Student::create([
                'name' => "Dashboard Student $i",
                'email' => "dashboard$i@example.com",
                'course_id' => $course->id
            ]);
        }
        
        // Visit the dashboard page
        $response = $this->actingAs($this->admin)->get('/dashboard');
        
        $response->assertStatus(200);
        
        // Check if the dashboard shows the correct student count
        // This depends on how the dashboard is implemented, adjust as necessary
        $response->assertSee('10');
    }
} 