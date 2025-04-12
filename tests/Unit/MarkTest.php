<?php

namespace Tests\Unit;

use App\Models\Mark;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarkTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a mark
     *
     * @return void
     */
    public function test_create_mark()
    {
        // Create necessary related models
        $user = User::factory()->create();
        $exam = Exam::factory()->create([
            'total_marks' => 100,
            'passing_marks' => 40
        ]);
        $subject = Subject::factory()->create();
        $student = Student::factory()->create();
        
        // Create mark
        $mark = Mark::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'marks_obtained' => 75.5,
            'total_marks' => 100,
            'grade' => 'A',
            'remarks' => 'Good work',
            'status' => 'draft',
            'is_absent' => false,
            'created_by' => $user->id
        ]);
        
        // Assert mark was created
        $this->assertInstanceOf(Mark::class, $mark);
        $this->assertEquals(75.5, $mark->marks_obtained);
        $this->assertEquals('draft', $mark->status);
        $this->assertEquals('Good work', $mark->remarks);
    }
    
    /**
     * Test mark status workflow
     *
     * @return void
     */
    public function test_mark_status_workflow()
    {
        // Create necessary related models
        $teacher = User::factory()->create();
        $verifier = User::factory()->create();
        $publisher = User::factory()->create();
        $exam = Exam::factory()->create();
        $subject = Subject::factory()->create();
        $student = Student::factory()->create();
        
        // Create mark
        $mark = Mark::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'marks_obtained' => 80,
            'total_marks' => 100,
            'status' => 'draft',
            'created_by' => $teacher->id
        ]);
        
        // Test submit
        $this->assertTrue($mark->submit($teacher->id));
        $this->assertEquals('submitted', $mark->status);
        $this->assertNotNull($mark->submitted_at);
        
        // Test verify
        $this->assertTrue($mark->verify($verifier->id));
        $this->assertEquals('verified', $mark->status);
        $this->assertEquals($verifier->id, $mark->verified_by);
        $this->assertNotNull($mark->verified_at);
        
        // Test publish
        $this->assertTrue($mark->publish($publisher->id));
        $this->assertEquals('published', $mark->status);
        $this->assertNotNull($mark->published_at);
    }
    
    /**
     * Test mark passing calculation
     *
     * @return void
     */
    public function test_mark_is_passing()
    {
        // Create exam with passing marks = 40
        $exam = Exam::factory()->create([
            'total_marks' => 100,
            'passing_marks' => 40
        ]);
        
        $subject = Subject::factory()->create();
        $student = Student::factory()->create();
        $user = User::factory()->create();
        
        // Create passing mark
        $passingMark = Mark::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => $student->id,
            'marks_obtained' => 50,
            'total_marks' => 100,
            'created_by' => $user->id
        ]);
        
        // Create failing mark
        $failingMark = Mark::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => Student::factory()->create()->id,
            'marks_obtained' => 30,
            'total_marks' => 100,
            'created_by' => $user->id
        ]);
        
        // Create absent mark
        $absentMark = Mark::create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'student_id' => Student::factory()->create()->id,
            'marks_obtained' => null,
            'total_marks' => 100,
            'is_absent' => true,
            'created_by' => $user->id
        ]);
        
        // Assert passing status
        $this->assertTrue($passingMark->isPassing());
        $this->assertFalse($failingMark->isPassing());
        $this->assertFalse($absentMark->isPassing());
    }
    
    /**
     * Test mark percentage calculation
     *
     * @return void
     */
    public function test_mark_percentage_calculation()
    {
        $mark = new Mark([
            'marks_obtained' => 75,
            'total_marks' => 100
        ]);
        
        $this->assertEquals(75, $mark->getPercentageAttribute());
        
        $mark->marks_obtained = 15;
        $mark->total_marks = 20;
        
        $this->assertEquals(75, $mark->getPercentageAttribute());
        
        // Test with zero total marks (should handle division by zero)
        $mark->total_marks = 0;
        $this->assertEquals(0, $mark->getPercentageAttribute());
        
        // Test with null marks (absent student)
        $mark->marks_obtained = null;
        $mark->total_marks = 100;
        $this->assertEquals(0, $mark->getPercentageAttribute());
    }
} 