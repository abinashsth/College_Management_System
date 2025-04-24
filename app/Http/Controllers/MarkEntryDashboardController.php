<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Mark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\MarkEntryRequest;

class MarkEntryDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'permission:create marks|role:admin']);
    }

    /**
     * Show the mark entry dashboard
     */
    public function index()
    {
        $academicYears = AcademicSession::orderBy('start_date', 'desc')->get();
        $faculties = collect(); // Will be populated via AJAX
        $departments = collect();
        $classes = collect();
        $subjects = collect();
        $examTerms = collect();

        return view('marks.entry-dashboard', compact(
            'academicYears',
            'faculties',
            'departments',
            'classes',
            'subjects',
            'examTerms'
        ));
    }

    /**
     * Get faculties for a specific academic year
     */
    public function getFaculties(Request $request)
    {
        $faculties = Faculty::where('academic_session_id', $request->academic_year)
            ->orderBy('name')
            ->get();

        return response()->json($faculties);
    }

    /**
     * Get departments for a specific faculty
     */
    public function getDepartments(Request $request)
    {
        $departments = Department::where('faculty_id', $request->faculty_id)
            ->orderBy('name')
            ->get();

        return response()->json($departments);
    }

    /**
     * Get classes for a specific department
     */
    public function getClasses(Request $request)
    {
        $classes = Classes::where('department_id', $request->department_id)
            ->orderBy('name')
            ->get();

        return response()->json($classes);
    }

    /**
     * Get subjects for a specific class
     */
    public function getSubjects(Request $request)
    {
        $subjects = Subject::whereHas('classes', function($query) use ($request) {
            $query->where('class_id', $request->class_id);
        })->orderBy('name')->get();

        return response()->json($subjects);
    }

    /**
     * Get exam terms for the selected academic year
     */
    public function getExamTerms(Request $request)
    {
        $examTerms = Exam::where('academic_session_id', $request->academic_year_id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($examTerms);
    }

    /**
     * Get students for mark entry
     */
    public function getStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'required|exists:exams,id',
        ]);

        try {
            $students = Student::where('class_id', $request->class_id)
                ->where('enrollment_status', 'active')
                ->with('user')
                ->orderBy('roll_number')
                ->get();

            $marks = Mark::where([
                'exam_id' => $request->exam_id,
                'subject_id' => $request->subject_id,
            ])->get()->keyBy('student_id');

            $exam = Exam::findOrFail($request->exam_id);

            return response()->json([
                'success' => true,
                'students' => $students,
                'marks' => $marks,
                'exam' => $exam,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading students: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update marks
     */
    public function store(MarkEntryRequest $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->marks as $studentId => $markData) {
                $isAbsent = isset($markData['is_absent']) && $markData['is_absent'];
                
                Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'exam_id' => $request->exam_id,
                        'subject_id' => $request->subject_id,
                    ],
                    [
                        'marks_obtained' => $isAbsent ? null : $markData['marks_obtained'],
                        'total_marks' => $request->total_marks,
                        'is_absent' => $isAbsent,
                        'remarks' => $markData['remarks'] ?? null,
                        'status' => $request->action === 'submit' ? Mark::STATUS_SUBMITTED : Mark::STATUS_DRAFT,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Marks saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving marks: ' . $e->getMessage()
            ], 500);
        }
    }
} 