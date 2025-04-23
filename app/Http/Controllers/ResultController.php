<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\ResultDetail;
use App\Models\Exam;
use App\Models\Section;
use App\Models\Student;
use App\Models\GradeSystem;
use App\Services\ResultCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ResultsExport;
use App\Models\Mark;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Services\GpaCalculationService;
use Illuminate\Support\Facades\Gate;

class ResultController extends Controller
{
    protected $gpaService;

    /**
     * Create a new controller instance.
     *
     * @param GpaCalculationService $gpaService
     */
    public function __construct(GpaCalculationService $gpaService)
    {
        $this->middleware('auth');
        $this->gpaService = $gpaService;
        $this->middleware('permission:view results')->only(['index', 'show', 'showStudentResult', 'analysis', 'analysisDetailed']);
        $this->middleware('permission:process results')->only(['process', 'processSection']);
        $this->middleware('permission:verify results')->only(['verify', 'verifyResults']);
        $this->middleware('permission:publish results')->only(['publish']);
        $this->middleware('permission:export results')->only(['exportPdf', 'exportExcel']);
    }

    /**
     * Display a listing of the results.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Gate::allows('view-results')) {
            abort(403, 'Unauthorized');
        }
        
        $classes = SchoolClass::all();
        $exams = Exam::where('status', 'published')->get();
        
        $selectedClassId = $request->input('class_id');
        $selectedExamId = $request->input('exam_id');
        $selectedSectionId = $request->input('section_id');
        
        $sections = collect();
        if ($selectedClassId) {
            $sections = Section::where('class_id', $selectedClassId)->get();
        }
        
        $results = [];
        
        if ($selectedClassId && $selectedExamId) {
            $studentsQuery = Student::whereHas('enrollments', function ($query) use ($selectedClassId, $selectedSectionId) {
                $query->where('class_id', $selectedClassId);
                if ($selectedSectionId) {
                    $query->where('section_id', $selectedSectionId);
                }
            });
            
            $students = $studentsQuery->get();
            
            foreach ($students as $student) {
                $marks = Mark::where('student_id', $student->id)
                    ->where('exam_id', $selectedExamId)
                    ->whereHas('exam', function ($query) {
                        $query->where('status', 'published');
                    })
                    ->with(['subject'])
                    ->get();
                
                if ($marks->count() > 0) {
                    $gpaResult = $this->gpaService->calculateGpa($marks);
                    
                    $results[] = [
                        'student' => $student,
                        'gpa' => $gpaResult['gpa'],
                        'passed' => $gpaResult['passed'],
                        'marks_count' => $marks->count(),
                    ];
                }
            }
        }
        
        return view('results.index', compact(
            'classes', 
            'exams', 
            'sections', 
            'results', 
            'selectedClassId', 
            'selectedExamId', 
            'selectedSectionId'
        ));
    }

    /**
     * Display the specified result.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function show(Result $result)
    {
        $result->load(['student', 'exam', 'calculatedBy', 'verifiedBy']);
        $resultDetails = ResultDetail::with('subject')
            ->where('result_id', $result->id)
            ->get();
        
        return view('results.show', compact('result', 'resultDetails'));
    }

    /**
     * Show form for processing results.
     *
     * @return \Illuminate\Http\Response
     */
    public function process()
    {
        $exams = Exam::with('subject')->orderBy('exam_date', 'desc')->get();
        $sections = Section::with('class')->get();
        $gradeSystems = GradeSystem::orderBy('name')->get();
        
        return view('results.process', compact('exams', 'sections', 'gradeSystems'));
    }

    /**
     * Process results for a section.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processSection(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'section_id' => 'required|exists:sections,id',
            'grade_system_id' => 'nullable|exists:grade_systems,id'
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $section = Section::findOrFail($request->section_id);
        
        // Set the default grade system for the results if specified
        if ($request->grade_system_id) {
            $gradeSystem = GradeSystem::findOrFail($request->grade_system_id);
            
            // Update existing results to use this grade system
            Result::where('exam_id', $exam->id)
                ->whereHas('student', function($query) use ($section) {
                    $query->where('section_id', $section->id);
                })
                ->update(['grade_system_id' => $gradeSystem->id]);
        }
        
        // Calculate results
        $result = ResultCalculationService::calculateSectionResults(
            $exam->id,
            $section->id,
            Auth::id()
        );
        
        if ($result['success']) {
            return redirect()->route('results.index', ['exam_id' => $exam->id])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Verify results for an exam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id'
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        
        // Verify results
        $result = ResultCalculationService::batchVerifyResults(
            $exam->id,
            Auth::id()
        );
        
        if ($result['success']) {
            return redirect()->route('results.index', ['exam_id' => $exam->id])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Publish results for an exam.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function publish(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id'
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        
        // Publish results
        $result = ResultCalculationService::batchPublishResults($exam->id);
        
        if ($result['success']) {
            return redirect()->route('results.index', ['exam_id' => $exam->id])
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Show a student's result.
     *
     * @param  int  $studentId
     * @param  int  $examId
     * @return \Illuminate\Http\Response
     */
    public function showStudentResult($studentId, $examId)
    {
        $student = Student::findOrFail($studentId);
        $exam = Exam::findOrFail($examId);
        
        $result = Result::with(['calculatedBy', 'verifiedBy'])
            ->where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->firstOrFail();
        
        $resultDetails = ResultDetail::with('subject')
            ->where('result_id', $result->id)
            ->get();
        
        return view('results.student', compact('student', 'exam', 'result', 'resultDetails'));
    }

    /**
     * Show result analysis for an exam.
     *
     * @param  int  $examId
     * @return \Illuminate\Http\Response
     */
    public function analysis($examId)
    {
        $exam = Exam::findOrFail($examId);
        
        // Get statistics
        $stats = DB::table('results')
            ->where('exam_id', $examId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN is_passed = 0 THEN 1 ELSE 0 END) as failed,
                AVG(percentage) as avg_percentage,
                AVG(gpa) as avg_gpa,
                MIN(percentage) as min_percentage,
                MAX(percentage) as max_percentage,
                MIN(gpa) as min_gpa,
                MAX(gpa) as max_gpa
            ')
            ->first();
        
        // Get grade distribution
        $gradeDistribution = DB::table('results')
            ->where('exam_id', $examId)
            ->groupBy('grade')
            ->select('grade', DB::raw('COUNT(*) as count'))
            ->orderBy('grade')
            ->get();
        
        // Get section-wise statistics
        $sectionStats = DB::table('results')
            ->join('students', 'results.student_id', '=', 'students.id')
            ->join('sections', 'students.section_id', '=', 'sections.id')
            ->join('classes', 'sections.class_id', '=', 'classes.id')
            ->where('results.exam_id', $examId)
            ->groupBy('sections.id', 'sections.section_name', 'classes.class_name')
            ->select(
                'sections.id as section_id',
                'sections.section_name',
                'classes.class_name',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN results.is_passed = 1 THEN 1 ELSE 0 END) as passed'),
                DB::raw('SUM(CASE WHEN results.is_passed = 0 THEN 1 ELSE 0 END) as failed'),
                DB::raw('AVG(results.percentage) as avg_percentage'),
                DB::raw('AVG(results.gpa) as avg_gpa')
            )
            ->get();
        
        // Get subject-wise statistics
        $subjectStats = DB::table('result_details')
            ->join('results', 'result_details.result_id', '=', 'results.id')
            ->join('subjects', 'result_details.subject_id', '=', 'subjects.id')
            ->where('results.exam_id', $examId)
            ->groupBy('subjects.id', 'subjects.name')
            ->select(
                'subjects.id as subject_id',
                'subjects.name as subject_name',
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN result_details.is_passed = 1 THEN 1 ELSE 0 END) as passed'),
                DB::raw('SUM(CASE WHEN result_details.is_passed = 0 THEN 1 ELSE 0 END) as failed'),
                DB::raw('AVG(result_details.marks_obtained) as avg_marks'),
                DB::raw('AVG(result_details.grade_point) as avg_grade_point')
            )
            ->get();
        
        return view('results.analysis', compact(
            'exam', 
            'stats', 
            'gradeDistribution', 
            'sectionStats', 
            'subjectStats'
        ));
    }

    /**
     * Export result as PDF.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(Result $result)
    {
        $result->load(['student', 'exam', 'calculatedBy', 'verifiedBy']);
        $resultDetails = ResultDetail::with('subject')
            ->where('result_id', $result->id)
            ->get();
        
        $pdf = Pdf::loadView('results.pdf', compact('result', 'resultDetails'));
        return $pdf->download('result-' . $result->student->student_id . '-' . $result->exam->name . '.pdf');
    }

    /**
     * Export results as Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Request $request)
    {
        if (!Gate::allows('download-results')) {
            abort(403, 'Unauthorized');
        }
        
        $selectedClassId = $request->input('class_id');
        $selectedExamId = $request->input('exam_id');
        $selectedSectionId = $request->input('section_id');
        
        if (!$selectedClassId || !$selectedExamId) {
            return redirect()->back()->with('error', 'Please select class and exam');
        }
        
        $className = SchoolClass::find($selectedClassId)->name;
        $examName = Exam::find($selectedExamId)->name;
        $sectionName = $selectedSectionId ? Section::find($selectedSectionId)->name : 'All';
        
        $fileName = $className . '_' . $examName . '_' . $sectionName . '_Results.xlsx';
        
        return Excel::download(
            new ResultsExport($selectedClassId, $selectedExamId, $selectedSectionId, $this->gpaService),
            $fileName
        );
    }

    /**
     * Show detailed analysis for a given exam
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function analysisDetailed(Request $request)
    {
        if (!Gate::allows('view-result-analysis')) {
            abort(403, 'Unauthorized');
        }
        
        $classes = SchoolClass::all();
        $exams = Exam::where('status', 'published')->get();
        
        $selectedClassId = $request->input('class_id');
        $selectedExamId = $request->input('exam_id');
        
        $analysisData = [];
        
        if ($selectedClassId && $selectedExamId) {
            $exam = Exam::findOrFail($selectedExamId);
            $subjects = Subject::whereHas('marks', function ($query) use ($selectedExamId) {
                $query->where('exam_id', $selectedExamId);
            })->get();
            
            $studentsQuery = Student::whereHas('enrollments', function ($query) use ($selectedClassId) {
                $query->where('class_id', $selectedClassId);
            });
            
            $totalStudents = $studentsQuery->count();
            $passedStudents = 0;
            $failedStudents = 0;
            $absentStudents = 0;
            
            $passingThresholds = [
                'above_90' => 0,
                'above_80' => 0,
                'above_70' => 0,
                'above_60' => 0,
                'above_50' => 0,
                'above_40' => 0,
                'below_40' => 0,
            ];
            
            $students = $studentsQuery->get();
            
            foreach ($students as $student) {
                $marks = Mark::where('student_id', $student->id)
                    ->where('exam_id', $selectedExamId)
                    ->get();
                
                if ($marks->isEmpty()) {
                    $absentStudents++;
                    continue;
                }
                
                $gpaResult = $this->gpaService->calculateGpa($marks);
                
                if ($gpaResult['passed']) {
                    $passedStudents++;
                    
                    // Calculate percentage threshold
                    $percentage = $marks->sum('marks_obtained') / $marks->count() / $exam->total_marks * 100;
                    
                    if ($percentage >= 90) {
                        $passingThresholds['above_90']++;
                    } elseif ($percentage >= 80) {
                        $passingThresholds['above_80']++;
                    } elseif ($percentage >= 70) {
                        $passingThresholds['above_70']++;
                    } elseif ($percentage >= 60) {
                        $passingThresholds['above_60']++;
                    } elseif ($percentage >= 50) {
                        $passingThresholds['above_50']++;
                    } elseif ($percentage >= 40) {
                        $passingThresholds['above_40']++;
                    } else {
                        $passingThresholds['below_40']++;
                    }
                } else {
                    $failedStudents++;
                }
            }
            
            // Subject-wise performance
            $subjectPerformance = [];
            foreach ($subjects as $subject) {
                $subjectMarks = Mark::where('exam_id', $selectedExamId)
                    ->where('subject_id', $subject->id)
                    ->whereHas('student', function ($query) use ($selectedClassId) {
                        $query->whereHas('enrollments', function ($q) use ($selectedClassId) {
                            $q->where('class_id', $selectedClassId);
                        });
                    })
                    ->get();
                
                $passCount = 0;
                $failCount = 0;
                $totalMarks = 0;
                
                foreach ($subjectMarks as $mark) {
                    $percentage = ($mark->marks_obtained / $exam->total_marks) * 100;
                    $totalMarks += $mark->marks_obtained;
                    
                    // Assuming 40% is passing
                    if ($percentage >= 40) {
                        $passCount++;
                    } else {
                        $failCount++;
                    }
                }
                
                $avgMarks = $subjectMarks->count() > 0 ? $totalMarks / $subjectMarks->count() : 0;
                
                $subjectPerformance[] = [
                    'subject' => $subject,
                    'pass_count' => $passCount,
                    'fail_count' => $failCount,
                    'avg_marks' => $avgMarks,
                    'pass_percentage' => $subjectMarks->count() > 0 ? ($passCount / $subjectMarks->count()) * 100 : 0
                ];
            }
            
            $analysisData = [
                'total_students' => $totalStudents,
                'passed_students' => $passedStudents,
                'failed_students' => $failedStudents,
                'absent_students' => $absentStudents,
                'passing_thresholds' => $passingThresholds,
                'subject_performance' => $subjectPerformance,
                'pass_percentage' => $totalStudents > 0 ? ($passedStudents / ($totalStudents - $absentStudents)) * 100 : 0
            ];
        }
        
        return view('results.analysis', compact(
            'classes',
            'exams',
            'selectedClassId',
            'selectedExamId',
            'analysisData'
        ));
    }
    
    /**
     * Verify results by marking all results in an exam as verified
     * 
     * @param Request $request
     * @param int $examId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyResults(Request $request, $examId)
    {
        if (!Gate::allows('verify-results')) {
            abort(403, 'Unauthorized');
        }
        
        $exam = Exam::findOrFail($examId);
        
        if ($exam->status != 'published') {
            return redirect()->back()->with('error', 'Can only verify published exam results');
        }
        
        Mark::where('exam_id', $examId)
            ->update(['verified_at' => now(), 'verified_by' => auth()->id()]);
        
        return redirect()->back()->with('success', 'All results verified successfully');
    }
} 