declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exports\MarksExport;
use App\Http\Requests\MarkEntry\FilterMarkEntryRequest;
use App\Http\Requests\MarkEntry\ImportMarksRequest;
use App\Http\Requests\MarkEntry\StoreMarkEntryRequest;
use App\Imports\MarksImport;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\Classes;
use App\Models\Department;
use App\Models\Exam;
use App\Models\Faculty;
use App\Models\GradeSystem;
use App\Models\Mark;
use App\Models\MarkHistory;
use App\Models\Student;
use App\Models\Subject;
use App\Services\GradeCalculationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class MarkEntryController extends Controller
{
    protected GradeCalculationService $gradeService;

    public function __construct(GradeCalculationService $gradeService)
    {
        $this->middleware(['auth', 'role:teacher|admin']);
        $this->gradeService = $gradeService;
    }

    public function index(): View
    {
        $academicYears = AcademicYear::active()->orderBy('year', 'desc')->get();
        $faculties = Faculty::active()->orderBy('name')->get();
        $gradeSystem = GradeSystem::getDefault();
        
        return view('mark-entry.index', compact('academicYears', 'faculties', 'gradeSystem'));
    }

    public function getDepartments(Faculty $faculty): JsonResponse
    {
        $departments = Department::where('faculty_id', $faculty->id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($departments);
    }

    public function getClasses(Department $department): JsonResponse
    {
        $classes = Classes::where('department_id', $department->id)
            ->active()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($classes);
    }

    public function getSubjects(Classes $class): JsonResponse
    {
        $subjects = Subject::whereHas('classes', function ($query) use ($class) {
            $query->where('class_id', $class->id);
        })
        ->active()
        ->orderBy('name')
        ->get(['id', 'name']);

        return response()->json($subjects);
    }

    public function getExamTerms(Classes $class): JsonResponse
    {
        $examTerms = Exam::where('class_id', $class->id)
            ->active()
            ->whereDate('deadline', '>=', now())
            ->orderBy('term')
            ->get(['id', 'name', 'term', 'deadline']);

        return response()->json($examTerms);
    }

    public function getStudents(FilterMarkEntryRequest $request): JsonResponse
    {
        $students = Student::with([
            'marks' => function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id)
                    ->where('exam_id', $request->exam_id);
            },
            'markHistories' => function ($query) use ($request) {
                $query->where('subject_id', $request->subject_id)
                    ->where('exam_id', $request->exam_id)
                    ->orderBy('created_at', 'desc');
            }
        ])
        ->whereHas('classes', function ($query) use ($request) {
            $query->where('class_id', $request->class_id);
        })
        ->active()
        ->orderBy('name')
        ->get();

        $maxMarks = $this->getMaxMarks($request->exam_id, $request->subject_id);
        $gradeSystem = GradeSystem::getDefault();

        return response()->json([
            'students' => $students,
            'maxMarks' => $maxMarks,
            'gradeSystem' => $gradeSystem,
            'deadline' => Exam::find($request->exam_id)->deadline
        ]);
    }

    public function store(StoreMarkEntryRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $exam = Exam::findOrFail($request->exam_id);
            
            // Check deadline
            if (Carbon::parse($exam->deadline)->isPast()) {
                return response()->json([
                    'message' => 'Mark entry deadline has passed',
                    'status' => 'error'
                ], 422);
            }

            $marks = $request->validated()['marks'];
            $maxMarks = $this->getMaxMarks($request->exam_id, $request->subject_id);

            foreach ($marks as $studentId => $markValue) {
                // Create mark history before updating
                $oldMark = Mark::where([
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'exam_id' => $request->exam_id,
                ])->first();

                if ($oldMark) {
                    MarkHistory::create([
                        'mark_id' => $oldMark->id,
                        'student_id' => $studentId,
                        'subject_id' => $request->subject_id,
                        'exam_id' => $request->exam_id,
                        'previous_marks' => $oldMark->marks,
                        'changed_by' => auth()->id(),
                        'change_reason' => $request->change_reason ?? 'Regular update'
                    ]);
                }

                // Calculate grade using service
                $gradeDetails = $this->gradeService->calculateGrade($markValue, $maxMarks);

                // Update or create mark
                Mark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject_id' => $request->subject_id,
                        'exam_id' => $request->exam_id,
                    ],
                    [
                        'marks' => $markValue,
                        'grade' => $gradeDetails['grade'],
                        'grade_point' => $gradeDetails['grade_point'],
                        'remarks' => $gradeDetails['remarks'],
                        'verified' => false
                    ]
                );
            }

            // Log activity
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'marks_entry',
                'description' => "Marks entered for Subject ID: {$request->subject_id}, Exam ID: {$request->exam_id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Marks saved successfully',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error saving marks: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function importMarks(ImportMarksRequest $request): JsonResponse
    {
        try {
            $file = $request->file('marks_file');
            $import = new MarksImport(
                $request->exam_id,
                $request->subject_id,
                $this->getMaxMarks($request->exam_id, $request->subject_id)
            );
            
            Excel::import($import, $file);

            return response()->json([
                'message' => 'Marks imported successfully',
                'status' => 'success',
                'processed' => $import->getProcessedRows(),
                'failed' => $import->getFailedRows()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error importing marks: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function exportMarks(Request $request): JsonResponse
    {
        try {
            $fileName = "marks_export_" . now()->format('Y-m-d_H-i-s') . ".xlsx";
            Excel::store(
                new MarksExport($request->exam_id, $request->subject_id),
                $fileName,
                'public'
            );

            return response()->json([
                'message' => 'Marks exported successfully',
                'status' => 'success',
                'file_url' => Storage::url($fileName)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error exporting marks: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $marks = Mark::with(['student', 'subject', 'exam'])
                ->where([
                    'subject_id' => $request->subject_id,
                    'exam_id' => $request->exam_id
                ])
                ->get();

            $pdf = PDF::loadView('mark-entry.pdf', [
                'marks' => $marks,
                'maxMarks' => $this->getMaxMarks($request->exam_id, $request->subject_id)
            ]);

            return $pdf->download('marks_sheet.pdf');
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error generating PDF: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    public function verifyMarks(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            Mark::where([
                'subject_id' => $request->subject_id,
                'exam_id' => $request->exam_id
            ])->update([
                'verified' => true,
                'verified_by' => auth()->id(),
                'verified_at' => now()
            ]);

            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'marks_verification',
                'description' => "Marks verified for Subject ID: {$request->subject_id}, Exam ID: {$request->exam_id}",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Marks verified successfully',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error verifying marks: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }

    private function getMaxMarks(int $examId, int $subjectId): int
    {
        return Exam::find($examId)->subjects()
            ->where('subject_id', $subjectId)
            ->first()
            ->pivot
            ->max_marks ?? 100;
    }
} 