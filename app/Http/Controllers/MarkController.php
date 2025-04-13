<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\MarkComponent;
use App\Models\Exam;
use App\Models\Subject;
use App\Models\Student;
use App\Models\GradeSystem;
use App\Models\ExamGradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MarksImport;
use App\Exports\MarksExport;

class MarkController extends Controller
{
    /**
     * Constructor with middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view marks')->only(['index', 'show']);
        $this->middleware('permission:create marks')->only(['create', 'store', 'createBulk', 'storeBulk', 'import', 'processImport']);
        $this->middleware('permission:edit marks')->only(['edit', 'update', 'submitMarks']);
        $this->middleware('permission:delete marks')->only(['destroy']);
        $this->middleware('permission:verify marks')->only(['verify', 'verifyAll']);
        $this->middleware('permission:publish marks')->only(['publish', 'publishAll']);
    }

    /**
     * Display a listing of the marks.
     */
    public function index(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to view marks
        $this->authorize('viewMarks', [Mark::class, $exam, $subject]);
        
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->with('user')
            ->orderBy('roll_number')
            ->get();
            
        $marks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $subject->id)
            ->get()
            ->keyBy('student_id');
            
        return view('marks.index', compact('exam', 'subject', 'students', 'marks'));
    }

    /**
     * Show the form for creating marks.
     */
    public function create(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to create marks
        $this->authorize('createMarks', [Mark::class, $exam, $subject]);
        
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->with('user')
            ->orderBy('roll_number')
            ->get();
            
        $marks = Mark::where('exam_id', $exam->id)
            ->where('subject_id', $subject->id)
            ->get()
            ->keyBy('student_id');
            
        return view('marks.create', compact('exam', 'subject', 'students', 'marks'));
    }

    /**
     * Form to select exam and subject before creating marks.
     */
    private function showExamSubjectSelectionForm()
    {
        $exams = Exam::where('is_active', true)->get();
        $subjects = Subject::all();
        
        return view('marks.select_exam_subject', compact('exams', 'subjects'));
    }

    /**
     * Store new marks.
     */
    public function store(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|exists:students,id',
            'marks.*.marks_obtained' => 'nullable|numeric|min:0|max:' . $request->total_marks,
            'marks.*.is_absent' => 'boolean',
            'marks.*.remarks' => 'nullable|string|max:255',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to store marks
        $this->authorize('createMarks', [Mark::class, $exam, $subject]);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->marks as $data) {
                $mark = Mark::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'subject_id' => $subject->id,
                        'student_id' => $data['student_id'],
                    ],
                    [
                        'marks_obtained' => $data['is_absent'] ? null : $data['marks_obtained'],
                        'is_absent' => $data['is_absent'] ?? false,
                        'grade' => $this->calculateGrade($exam, $data['marks_obtained'] ?? 0, $data['is_absent'] ?? false),
                        'remarks' => $data['remarks'] ?? null,
                        'entered_by' => Auth::id(),
                    ]
                );
            }
            
            DB::commit();
            
            return redirect()->route('marks.index', ['exam_id' => $exam->id, 'subject_id' => $subject->id])
                ->with('success', 'Marks added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving marks: ' . $e->getMessage());
        }
    }

    /**
     * Calculate grade based on marks and exam scale.
     *
     * @param Exam $exam
     * @param float $marks
     * @param bool $isAbsent
     * @return string|null
     */
    private function calculateGrade(Exam $exam, $marks, $isAbsent)
    {
        if ($isAbsent) {
            return 'AB';
        }
        
        if (!$marks) {
            return null;
        }
        
        $percentage = ($marks / $exam->total_marks) * 100;
        
        $grade = ExamGradeScale::where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->where('school_id', $exam->school_id)
            ->first();
            
        return $grade ? $grade->grade : null;
    }

    /**
     * Show the form for creating marks in bulk.
     */
    public function createBulk(Request $request)
    {
        $examId = $request->input('exam_id');
        $subjectId = $request->input('subject_id');
        
        if (!$examId || !$subjectId) {
            return $this->showExamSubjectSelectionForm();
        }
        
        $exam = Exam::findOrFail($examId);
        $subject = Subject::findOrFail($subjectId);
        
        // Get students enrolled in this exam's class
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->get();
        
        // Check if marks already exist for some students
        $existingMarks = Mark::where('exam_id', $examId)
            ->where('subject_id', $subjectId)
            ->get()
            ->keyBy('student_id');
        
        return view('marks.create_bulk', compact('exam', 'subject', 'students', 'existingMarks'));
    }

    /**
     * Store marks in bulk.
     */
    public function storeBulk(Request $request)
    {
        // Validate the request
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
            'is_absent' => 'array',
            'remarks' => 'array',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->marks as $studentId => $marks) {
                $isAbsent = isset($request->is_absent[$studentId]) && $request->is_absent[$studentId] === 'on';
                
                // Check if mark already exists for this student, exam, and subject
                $existingMark = Mark::where('exam_id', $exam->id)
                    ->where('subject_id', $subject->id)
                    ->where('student_id', $studentId)
                    ->first();
                
                if ($existingMark) {
                    // Update existing mark
                    $existingMark->marks_obtained = $isAbsent ? null : $marks;
                    $existingMark->remarks = $request->remarks[$studentId] ?? null;
                    $existingMark->is_absent = $isAbsent;
                    $existingMark->updated_by = Auth::id();
                    
                    // Calculate grade if not absent
                    if (!$isAbsent && $marks !== null) {
                        $percentage = ($marks / $exam->total_marks) * 100;
                        $gradeSystem = GradeSystem::getDefault();
                        
                        if ($gradeSystem) {
                            $existingMark->grade = $gradeSystem->findGradeForPercentage($percentage);
                        }
                    } else {
                        $existingMark->grade = null;
                    }
                    
                    $existingMark->save();
                } else {
                    // Create new mark
                    $markData = [
                        'exam_id' => $exam->id,
                        'subject_id' => $subject->id,
                        'student_id' => $studentId,
                        'marks_obtained' => $isAbsent ? null : $marks,
                        'total_marks' => $exam->total_marks,
                        'remarks' => $request->remarks[$studentId] ?? null,
                        'is_absent' => $isAbsent,
                        'status' => 'draft',
                        'created_by' => Auth::id(),
                    ];
                    
                    // Calculate grade if not absent
                    if (!$isAbsent && $marks !== null) {
                        $percentage = ($marks / $exam->total_marks) * 100;
                        $gradeSystem = GradeSystem::getDefault();
                        
                        if ($gradeSystem) {
                            $markData['grade'] = $gradeSystem->findGradeForPercentage($percentage);
                        }
                    }
                    
                    Mark::create($markData);
                }
            }
            
            DB::commit();
            
            return redirect()->route('marks.index')
                ->with('success', 'Marks saved successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saving marks: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving marks: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified mark.
     */
    public function show(Mark $mark)
    {
        $mark->load(['exam', 'student', 'subject', 'creator', 'updater', 'verifier', 'components']);
        
        return view('marks.show', compact('mark'));
    }

    /**
     * Show the form for editing a mark.
     */
    public function edit(Mark $mark)
    {
        // Check if mark is published - can't edit published marks
        if ($mark->status === 'published') {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Cannot edit published marks.');
        }
        
        $mark->load(['exam', 'student', 'subject', 'components']);
        
        return view('marks.edit', compact('mark'));
    }

    /**
     * Update the specified mark.
     */
    public function update(Request $request, Mark $mark)
    {
        // Check if mark is published - can't update published marks
        if ($mark->status === 'published') {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Cannot update published marks.');
        }
        
        // Validate the request
        $request->validate([
            'marks_obtained' => 'nullable|numeric|min:0',
            'is_absent' => 'boolean',
            'remarks' => 'nullable|string',
            'components' => 'array',
            'components.*.marks_obtained' => 'nullable|numeric|min:0',
            'components.*.total_marks' => 'required|numeric|min:0',
            'components.*.weight_percentage' => 'required|numeric|min:0|max:100',
        ]);
        
        $isAbsent = $request->is_absent ?? false;
        
        DB::beginTransaction();
        
        try {
            // Update mark
            $mark->marks_obtained = $isAbsent ? null : $request->marks_obtained;
            $mark->remarks = $request->remarks;
            $mark->is_absent = $isAbsent;
            $mark->updated_by = Auth::id();
            
            // If status was submitted or verified, revert to draft
            if ($mark->status === 'submitted' || $mark->status === 'verified') {
                $mark->status = 'draft';
            }
            
            // Calculate grade if not absent and marks provided
            if (!$isAbsent && $request->marks_obtained !== null) {
                $percentage = ($request->marks_obtained / $mark->total_marks) * 100;
                $gradeSystem = GradeSystem::getDefault();
                
                if ($gradeSystem) {
                    $mark->grade = $gradeSystem->findGradeForPercentage($percentage);
                }
            } else {
                $mark->grade = null;
            }
            
            $mark->save();
            
            // Update components if provided
            if (isset($request->components) && is_array($request->components)) {
                // Delete existing components
                $mark->components()->delete();
                
                // Create new components
                foreach ($request->components as $componentId => $componentData) {
                    MarkComponent::create([
                        'mark_id' => $mark->id,
                        'component_name' => $componentData['name'],
                        'marks_obtained' => $isAbsent ? null : $componentData['marks_obtained'],
                        'total_marks' => $componentData['total_marks'],
                        'weight_percentage' => $componentData['weight_percentage'],
                    ]);
                }
                
                // Recalculate the total mark based on components
                $mark->calculateMarks();
            }
            
            DB::commit();
            
            return redirect()->route('marks.show', $mark)
                ->with('success', 'Mark updated successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating mark: ' . $e->getMessage());
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating mark: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified mark.
     */
    public function destroy(Mark $mark)
    {
        // Check if mark is published - can't delete published marks
        if ($mark->status === 'published') {
            return redirect()->route('marks.index')
                ->with('error', 'Cannot delete published marks.');
        }
        
        try {
            // Delete components first
            $mark->components()->delete();
            
            // Then delete the mark
            $mark->delete();
            
            return redirect()->route('marks.index')
                ->with('success', 'Mark deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting mark: ' . $e->getMessage());
            
            return redirect()->route('marks.index')
                ->with('error', 'Error deleting mark: ' . $e->getMessage());
        }
    }

    /**
     * Submit marks for verification.
     */
    public function submitMarks(Request $request, Mark $mark)
    {
        // Check if mark is already submitted or published
        if ($mark->status !== 'draft') {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Mark is already submitted or published.');
        }
        
        if ($mark->submit(Auth::id())) {
            return redirect()->route('marks.show', $mark)
                ->with('success', 'Mark submitted for verification.');
        }
        
        return redirect()->route('marks.show', $mark)
            ->with('error', 'Error submitting mark for verification.');
    }

    /**
     * Verify a mark.
     */
    public function verify(Request $request, Mark $mark)
    {
        // Check if mark is submitted
        if ($mark->status !== 'submitted') {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Mark must be submitted before verification.');
        }
        
        if ($mark->verify(Auth::id())) {
            return redirect()->route('marks.show', $mark)
                ->with('success', 'Mark verified successfully.');
        }
        
        return redirect()->route('marks.show', $mark)
            ->with('error', 'Error verifying mark.');
    }

    /**
     * Verify all submitted marks for an exam and subject.
     */
    public function verifyAll(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        
        $marks = Mark::where('exam_id', $request->exam_id)
            ->where('subject_id', $request->subject_id)
            ->where('status', 'submitted')
            ->get();
        
        if ($marks->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No submitted marks found for verification.');
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($marks as $mark) {
                $mark->verify(Auth::id());
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'All submitted marks verified successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error verifying marks: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error verifying marks: ' . $e->getMessage());
        }
    }

    /**
     * Publish a mark.
     */
    public function publish(Request $request, Mark $mark)
    {
        // Check if mark is verified
        if ($mark->status !== 'verified') {
            return redirect()->route('marks.show', $mark)
                ->with('error', 'Mark must be verified before publishing.');
        }
        
        if ($mark->publish(Auth::id())) {
            return redirect()->route('marks.show', $mark)
                ->with('success', 'Mark published successfully.');
        }
        
        return redirect()->route('marks.show', $mark)
            ->with('error', 'Error publishing mark.');
    }

    /**
     * Publish all verified marks for an exam and subject.
     */
    public function publishAll(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        
        $marks = Mark::where('exam_id', $request->exam_id)
            ->where('subject_id', $request->subject_id)
            ->where('status', 'verified')
            ->get();
        
        if ($marks->isEmpty()) {
            return redirect()->back()
                ->with('info', 'No verified marks found for publishing.');
        }
        
        DB::beginTransaction();
        
        try {
            foreach ($marks as $mark) {
                $mark->publish(Auth::id());
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'All verified marks published successfully.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error publishing marks: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error publishing marks: ' . $e->getMessage());
        }
    }

    /**
     * Show form for importing marks from Excel or CSV file.
     */
    public function import()
    {
        $exams = Exam::where('is_active', true)->get();
        $subjects = Subject::all();
        
        return view('marks.import', compact('exams', 'subjects'));
    }

    /**
     * Process import of marks from Excel or CSV file.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'import_file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        try {
            DB::beginTransaction();
            
            Excel::import(new MarksImport($exam, $subject, Auth::id()), $request->file('import_file'));
            
            DB::commit();
            
            return redirect()->route('marks.index', [
                    'exam_id' => $exam->id,
                    'subject_id' => $subject->id
                ])
                ->with('success', 'Marks imported successfully.');
        } catch (ValidationException $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error importing marks: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Error importing marks: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Export marks template for bulk import.
     */
    public function exportTemplate(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);
        
        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Get students for this exam
        $students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->get();
        
        // Return Excel file with student details
        return Excel::download(new MarksExport($students, $exam, $subject), 'marks_template.xlsx');
    }

    /**
     * Show import form.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function showImportForm(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to import marks
        $this->authorize('importMarks', [Mark::class, $exam, $subject]);
        
        return view('marks.import', compact('exam', 'subject'));
    }

    /**
     * Export marks to Excel/CSV.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'format' => 'required|in:xlsx,csv',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // Check if user has permission to export marks
        $this->authorize('exportMarks', [Mark::class, $exam, $subject]);
        
        $filename = "marks_{$exam->name}_{$subject->name}." . $request->format;
        
        return Excel::download(
            new MarksExport($exam, $subject), 
            $filename,
            $request->format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }

    /**
     * Download import template.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadTemplate(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'format' => 'required|in:xlsx,csv',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        // No grades in template
        $export = new MarksExport($exam, $subject, false);
        
        $filename = "template_{$exam->name}_{$subject->name}." . $request->format;
        
        return Excel::download(
            $export,
            $filename,
            $request->format === 'csv' ? \Maatwebsite\Excel\Excel::CSV : \Maatwebsite\Excel\Excel::XLSX
        );
    }
} 