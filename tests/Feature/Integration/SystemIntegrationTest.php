<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Mark;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemIntegrationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        
        Permission::create(['name' => 'view students']);
        Permission::create(['name' => 'view marks']);
        Permission::create(['name' => 'create marks']);
        Permission::create(['name' => 'view exams']);
        
        // Assign necessary permissions to roles
        $teacherRole->givePermissionTo(['view students', 'view marks', 'create marks', 'view exams']);
        
        // Create users with roles
        $this->admin = User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com']);
        $this->admin->assignRole('super-admin');
        
        $this->teacher = User::factory()->create(['name' => 'Teacher User', 'email' => 'teacher@example.com']);
        $this->teacher->assignRole('teacher');
        
        $this->student = User::factory()->create(['name' => 'Student User', 'email' => 'student@example.com']);
        $this->student->assignRole('student');
    }

    /**
     * Test the complete student enrollment and mark entry flow.
     * This tests integration between multiple system components.
     */
    public function test_student_enrollment_and_mark_entry_flow(): void
    {
        // 1. Admin creates a faculty
        $response = $this->actingAs($this->admin)
            ->post('/faculties', [
                'name' => 'Test Faculty',
                'code' => 'TF',
                'description' => 'Test Faculty Description'
            ]);
        
        $response->assertRedirect();
        $faculty = Faculty::where('name', 'Test Faculty')->first();
        $this->assertNotNull($faculty);
        
        // 2. Admin creates a department under this faculty
        $response = $this->actingAs($this->admin)
            ->post('/departments', [
                'name' => 'Test Department',
                'code' => 'TD',
                'faculty_id' => $faculty->id,
                'description' => 'Test Department Description'
            ]);
        
        $response->assertRedirect();
        $department = Department::where('name', 'Test Department')->first();
        $this->assertNotNull($department);
        
        // 3. Admin creates a course under this department
        $response = $this->actingAs($this->admin)
            ->post('/courses', [
                'name' => 'Test Course',
                'code' => 'TC',
                'department_id' => $department->id,
                'description' => 'Test Course Description',
                'duration' => 4,
                'credit_hours' => 120
            ]);
        
        $response->assertRedirect();
        $course = Course::where('name', 'Test Course')->first();
        $this->assertNotNull($course);
        
        // 4. Admin creates a subject for this course
        $response = $this->actingAs($this->admin)
            ->post('/subjects', [
                'name' => 'Test Subject',
                'code' => 'TS101',
                'credit_hours' => 3,
                'description' => 'Test Subject Description'
            ]);
        
        $response->assertRedirect();
        $subject = Subject::where('name', 'Test Subject')->first();
        $this->assertNotNull($subject);
        
        // 5. Admin enrolls a student
        $response = $this->actingAs($this->admin)
            ->post('/students', [
                'name' => 'Test Student',
                'email' => 'teststudent@example.com',
                'phone' => '1234567890',
                'address' => 'Test Address',
                'dob' => '2000-01-01',
                'gender' => 'Male',
                'admission_date' => '2023-08-15',
                'course_id' => $course->id
            ]);
        
        $response->assertRedirect();
        $student = Student::where('name', 'Test Student')->first();
        $this->assertNotNull($student);
        
        // 6. Admin creates an exam
        $response = $this->actingAs($this->admin)
            ->post('/exams', [
                'title' => 'Midterm Exam',
                'description' => 'Midterm examination for all subjects',
                'start_date' => '2023-09-15',
                'end_date' => '2023-09-20',
                'total_marks' => 100,
                'passing_marks' => 40
            ]);
        
        $response->assertRedirect();
        $exam = Exam::where('title', 'Midterm Exam')->first();
        $this->assertNotNull($exam);
        
        // 7. Teacher enters marks for the student
        $response = $this->actingAs($this->teacher)
            ->post('/marks', [
                'exam_id' => $exam->id,
                'subject_id' => $subject->id,
                'student_id' => $student->id,
                'marks_obtained' => 85,
                'total_marks' => 100,
                'remarks' => 'Good performance'
            ]);
        
        $response->assertRedirect();
        $mark = Mark::where([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => $student->id
        ])->first();
        
        $this->assertNotNull($mark);
        $this->assertEquals(85, $mark->marks_obtained);
        
        // 8. Verify the student can view their own marks
        $response = $this->actingAs($this->student)
            ->get('/student-grades');
        
        $response->assertSuccessful();
        // Additional assertions to verify that marks are visible to the student
    }

    /**
     * Test database query optimization for listing students with their associated data.
     */
    public function test_student_listing_performance(): void
    {
        // Create a large batch of test data
        $faculty = Faculty::create(['name' => 'Test Faculty', 'code' => 'TF']);
        $department = Department::create(['name' => 'Test Department', 'code' => 'TD', 'faculty_id' => $faculty->id]);
        $course = Course::create([
            'name' => 'Test Course', 
            'code' => 'TC', 
            'department_id' => $department->id,
            'duration' => 4,
            'credit_hours' => 120
        ]);
        
        // Create multiple students for performance testing
        $studentCount = 10; // Keep it small for testing, can be increased for real performance testing
        for ($i = 0; $i < $studentCount; $i++) {
            Student::create([
                'name' => "Student $i",
                'email' => "student$i@example.com",
                'phone' => "123456789$i",
                'address' => "Address $i",
                'dob' => '2000-01-01',
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'admission_date' => '2023-08-15',
                'course_id' => $course->id
            ]);
        }
        
        // Measure the time taken to fetch students with eager loading
        $startTime = microtime(true);
        $studentsWithEagerLoading = Student::with(['course', 'course.department', 'course.department.faculty'])->get();
        $eagerLoadingTime = microtime(true) - $startTime;
        
        // Measure time without eager loading (this would cause N+1 query problem)
        $startTime = microtime(true);
        $studentsWithoutEagerLoading = Student::all();
        foreach ($studentsWithoutEagerLoading as $student) {
            if ($student->course) {
                $department = $student->course->department;
                if ($department) {
                    $faculty = $department->faculty;
                }
            }
        }
        $withoutEagerLoadingTime = microtime(true) - $startTime;
        
        // Assert eager loading is faster (this should almost always be true)
        $this->assertLessThan($withoutEagerLoadingTime, $eagerLoadingTime, 
            "Eager loading should be faster than lazy loading");
        
        // Also check the count to ensure data was actually loaded
        $this->assertEquals($studentCount, $studentsWithEagerLoading->count());
    }

    /**
     * Test system behavior under heavy load
     */
    public function test_system_under_load(): void
    {
        // Skip this test in CI environments as it's resource-intensive
        if (env('CI_ENVIRONMENT')) {
            $this->markTestSkipped('Skipping load test in CI environment');
        }
        
        // Create basic test data
        $faculty = Faculty::create(['name' => 'Load Test Faculty', 'code' => 'LTF']);
        $department = Department::create(['name' => 'Load Test Department', 'code' => 'LTD', 'faculty_id' => $faculty->id]);
        $course = Course::create([
            'name' => 'Load Test Course', 
            'code' => 'LTC', 
            'department_id' => $department->id,
            'duration' => 4,
            'credit_hours' => 120
        ]);
        
        // Create a larger dataset - adjust numbers based on what's reasonable for tests
        $batchSize = 5; // Create 5 of each for testing
        
        // Batch create subjects
        $subjects = [];
        for ($i = 0; $i < $batchSize; $i++) {
            $subjects[] = Subject::create([
                'name' => "Load Subject $i",
                'code' => "LS$i",
                'credit_hours' => 3,
                'description' => "Load Subject Description $i"
            ]);
        }
        
        // Batch create students
        $students = [];
        for ($i = 0; $i < $batchSize; $i++) {
            $students[] = Student::create([
                'name' => "Load Student $i",
                'email' => "loadstudent$i@example.com",
                'phone' => "987654321$i",
                'address' => "Load Address $i",
                'dob' => '2000-01-01',
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'admission_date' => '2023-08-15',
                'course_id' => $course->id
            ]);
        }
        
        // Create an exam
        $exam = Exam::create([
            'title' => 'Load Test Exam',
            'description' => 'Exam for load testing',
            'start_date' => '2023-10-01',
            'end_date' => '2023-10-05',
            'total_marks' => 100,
            'passing_marks' => 40
        ]);
        
        // Simulate many concurrent mark entries and check system behavior
        $startTime = microtime(true);
        
        // Enter marks for all combinations of students and subjects
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                Mark::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'student_id' => $student->id,
                    'marks_obtained' => rand(40, 100),
                    'total_marks' => 100,
                    'remarks' => 'Load test mark entry',
                    'created_by' => $this->teacher->id
                ]);
            }
        }
        
        $endTime = microtime(true);
        $totalTime = $endTime - $startTime;
        
        // Ensure all expected marks were created
        $this->assertEquals(
            $batchSize * $batchSize, 
            Mark::where('exam_id', $exam->id)->count(),
            "All marks should be successfully created"
        );
        
        // Time assertion should be reasonable - adjust based on environment
        $this->assertLessThan(
            10, // seconds
            $totalTime,
            "Batch marks creation should complete within reasonable time"
        );
    }
} 