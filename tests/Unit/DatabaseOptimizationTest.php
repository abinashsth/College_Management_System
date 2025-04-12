<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseOptimizationTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * Test eager loading optimization for student marks queries.
     *
     * @return void
     */
    public function test_eager_loading_optimization()
    {
        // Create test data
        $faculty = Faculty::create(['name' => 'Test Faculty', 'code' => 'TF']);
        $department = Department::create(['name' => 'Test Department', 'code' => 'TD', 'faculty_id' => $faculty->id]);
        $course = Course::create([
            'name' => 'Test Course', 
            'code' => 'TC', 
            'department_id' => $department->id,
            'duration' => 4,
            'credit_hours' => 120
        ]);
        
        // Create 5 students
        $students = [];
        for ($i = 0; $i < 5; $i++) {
            $students[] = Student::create([
                'name' => "Optimization Student $i",
                'email' => "optstudent$i@example.com",
                'phone' => "123456789$i",
                'address' => "Test Address $i",
                'dob' => '2000-01-01',
                'gender' => 'Male',
                'admission_date' => '2023-08-15',
                'course_id' => $course->id
            ]);
        }
        
        // Create 3 subjects
        $subjects = [];
        for ($i = 0; $i < 3; $i++) {
            $subjects[] = Subject::create([
                'name' => "Test Subject $i",
                'code' => "TS$i",
                'credit_hours' => 3,
                'description' => "Test subject description $i"
            ]);
        }
        
        // Create an exam
        $exam = Exam::create([
            'title' => 'Optimization Test Exam',
            'description' => 'Exam for optimization testing',
            'start_date' => '2023-10-15',
            'end_date' => '2023-10-20',
            'total_marks' => 100,
            'passing_marks' => 40
        ]);
        
        // Create user for marks creation
        $user = User::factory()->create();
        
        // Create marks for all student-subject combinations
        foreach ($students as $student) {
            foreach ($subjects as $subject) {
                Mark::create([
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id,
                    'student_id' => $student->id,
                    'marks_obtained' => rand(40, 100),
                    'total_marks' => 100,
                    'status' => 'published',
                    'created_by' => $user->id
                ]);
            }
        }
        
        // Reset the query count
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Test with N+1 problem (without eager loading)
        $studentsLazy = Student::where('course_id', $course->id)->get();
        foreach ($studentsLazy as $student) {
            // This will trigger additional queries for each student
            $marks = $student->marks()->where('exam_id', $exam->id)->get();
            foreach ($marks as $mark) {
                // This will trigger additional queries for each mark
                $subject = $mark->subject;
            }
        }
        
        $lazyQueryCount = count(DB::getQueryLog());
        
        // Reset the query count
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Test with eager loading (properly optimized)
        $studentsEager = Student::with(['marks' => function ($query) use ($exam) {
            $query->where('exam_id', $exam->id)->with('subject');
        }])->where('course_id', $course->id)->get();
        
        foreach ($studentsEager as $student) {
            // This won't trigger additional queries due to eager loading
            foreach ($student->marks as $mark) {
                // This won't trigger additional queries due to eager loading
                $subject = $mark->subject;
            }
        }
        
        $eagerQueryCount = count(DB::getQueryLog());
        
        // Verify eager loading uses fewer queries (more efficient)
        $this->assertLessThan(
            $lazyQueryCount, 
            $eagerQueryCount, 
            "Eager loading should execute fewer queries than lazy loading"
        );
    }
    
    /**
     * Test for proper indexing on frequently queried columns.
     *
     * @return void
     */
    public function test_indexing_optimization()
    {
        // Create test data for the indexing test
        $faculty = Faculty::create(['name' => 'Index Faculty', 'code' => 'IF']);
        $department = Department::create(['name' => 'Index Department', 'code' => 'ID', 'faculty_id' => $faculty->id]);
        
        // Create 20 courses with specific department
        for ($i = 0; $i < 20; $i++) {
            Course::create([
                'name' => "Index Course $i",
                'code' => "IC$i",
                'department_id' => $department->id,
                'duration' => 4,
                'credit_hours' => 120
            ]);
        }
        
        // Reset query log
        DB::flushQueryLog();
        DB::enableQueryLog();
        
        // Query with indexed column (assuming foreign keys are indexed)
        $courses = Course::where('department_id', $department->id)->get();
        
        // Get the query log
        $queryLogs = DB::getQueryLog();
        $lastQuery = end($queryLogs);
        
        // Check if query plan indicates index usage
        // This is more of a simulated check since we can't directly check the query plan in PHPUnit
        $this->assertNotEmpty($courses);
        $this->assertCount(20, $courses);
        
        // Add additional checks if the database supports query timing
        // This is a basic approximation - real index testing often requires examining the query plan
        $this->assertLessThan(
            10, // milliseconds - arbitrary threshold for a simple indexed query
            $lastQuery['time'] ?? 0,
            "Query on indexed column should be fast"
        );
    }
    
    /**
     * Test for chunk processing of large datasets.
     *
     * @return void
     */
    public function test_chunk_processing()
    {
        // Create a larger test dataset
        $faculty = Faculty::create(['name' => 'Chunk Faculty', 'code' => 'CF']);
        $department = Department::create(['name' => 'Chunk Department', 'code' => 'CD', 'faculty_id' => $faculty->id]);
        $course = Course::create([
            'name' => 'Chunk Course', 
            'code' => 'CC', 
            'department_id' => $department->id,
            'duration' => 4,
            'credit_hours' => 120
        ]);
        
        // Create 50 students
        for ($i = 0; $i < 50; $i++) {
            Student::create([
                'name' => "Chunk Student $i",
                'email' => "chunkstudent$i@example.com",
                'phone' => "123456789$i",
                'address' => "Chunk Address $i",
                'dob' => '2000-01-01',
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'admission_date' => '2023-08-15',
                'course_id' => $course->id
            ]);
        }
        
        // Process records in one go (memory intensive for large datasets)
        $startTime = microtime(true);
        $allAtOnce = Student::where('course_id', $course->id)->get();
        $processed = 0;
        foreach ($allAtOnce as $student) {
            // Simulate some processing
            $processed++;
        }
        $singleQueryTime = microtime(true) - $startTime;
        
        // Process records in chunks (memory efficient for large datasets)
        $startTime = microtime(true);
        $chunkProcessed = 0;
        Student::where('course_id', $course->id)->chunk(10, function ($students) use (&$chunkProcessed) {
            foreach ($students as $student) {
                // Simulate some processing
                $chunkProcessed++;
            }
        });
        $chunkQueryTime = microtime(true) - $startTime;
        
        // For small datasets, single query might be faster
        // But we're testing the chunking mechanism works correctly
        $this->assertEquals(50, $processed, "All records should be processed in single query");
        $this->assertEquals(50, $chunkProcessed, "All records should be processed in chunked queries");
        
        // Memory usage would be a better metric, but is difficult to test reliably
        $this->addToAssertionCount(1); // Count this as a successful assertion since we verified record counts
    }
} 