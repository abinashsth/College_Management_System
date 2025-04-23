<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Student;
use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Student grades API endpoint
Route::middleware('auth:sanctum')->get('/students/{student}/grades', function (Student $student) {
    // Check if user is admin or if the user is viewing their own grades
    if (!Auth::user()->hasRole(['Super Admin', 'Admin', 'super-admin']) && Auth::id() !== $student->user_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    $exams = Exam::whereHas('students', function($query) use ($student) {
        $query->where('student_id', $student->id);
    })->with([
        'class', 
        'subject', 
        'academicSession'
    ])->get();
    
    $examData = [];
    foreach ($exams as $exam) {
        $pivot = $exam->students()->where('student_id', $student->id)->first()->pivot;
        $examData[] = [
            'id' => $exam->id,
            'title' => $exam->title,
            'exam_type' => ucfirst($exam->exam_type),
            'exam_date' => $exam->exam_date->format('M d, Y'),
            'subject_name' => $exam->subject ? $exam->subject->name : 'Multiple Subjects',
            'grade' => $pivot->grade,
            'total_marks' => $exam->total_marks,
            'passing_marks' => $exam->passing_marks,
            'remarks' => $pivot->remarks,
        ];
    }
    
    return response()->json([
        'student' => [
            'id' => $student->id,
            'name' => $student->name,
            'student_id' => $student->student_id,
            'class' => $student->class ? $student->class->class_name : null
        ],
        'exams' => $examData
    ]);
}); 