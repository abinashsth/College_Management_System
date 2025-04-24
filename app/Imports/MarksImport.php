<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Mark;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ExamGradeScale;
use App\Services\GradeCalculationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class MarksImport implements ToCollection, WithHeadingRow, WithValidation, WithCalculatedFormulas, WithMultipleSheets
{
    protected $exam;
    protected $subject;
    protected $enteredBy;
    protected $students;
    protected $rollNumberMap;
    protected $admissionNumberMap;
    protected int $examId;
    protected int $subjectId;
    protected float $maxMarks;
    protected int $processedRows = 0;
    protected array $failedRows = [];
    protected GradeCalculationService $gradeService;
    
    /**
     * Create a new import instance.
     *
     * @param Exam $exam
     * @param Subject $subject
     * @param int $enteredBy
     * @return void
     */
    public function __construct(Exam $exam, Subject $subject, $enteredBy, int $examId, int $subjectId, float $maxMarks)
    {
        $this->exam = $exam;
        $this->subject = $subject;
        $this->enteredBy = $enteredBy;
        
        // Get all students in the class
        $this->students = Student::where('class_id', $exam->class_id)
            ->where('enrollment_status', 'active')
            ->get();
            
        // Create lookup maps
        $this->rollNumberMap = $this->students->keyBy('roll_number')->toArray();
        $this->admissionNumberMap = $this->students->keyBy('admission_number')->toArray();
        
        $this->examId = $examId;
        $this->subjectId = $subjectId;
        $this->maxMarks = $maxMarks;
        $this->gradeService = new GradeCalculationService();
    }
    
    /**
     * Define the sheets to import.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
    
    /**
     * @param Collection $rows
     * @return void
     * @throws ValidationException
     */
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw ValidationException::withMessages(['file' => 'The file is empty or has no valid data.']);
        }
        
        // Begin transaction
        DB::beginTransaction();
        
        try {
            foreach ($rows as $row) {
                try {
                    // Skip rows with empty identifiers
                    if (empty($row['roll_number']) && empty($row['admission_number'])) {
                        continue;
                    }
                    
                    // Find student by roll number or admission number
                    $student = null;
                    
                    if (!empty($row['roll_number']) && isset($this->rollNumberMap[$row['roll_number']])) {
                        $student = $this->rollNumberMap[$row['roll_number']];
                    } elseif (!empty($row['admission_number']) && isset($this->admissionNumberMap[$row['admission_number']])) {
                        $student = $this->admissionNumberMap[$row['admission_number']];
                    }
                    
                    if (!$student) {
                        continue; // Skip if student not found
                    }
                    
                    // Determine if absent
                    $isAbsent = strtolower($row['marks_obtained'] ?? '') === 'ab' || 
                                strtolower($row['absent'] ?? '') === 'yes' || 
                                strtolower($row['is_absent'] ?? '') === 'yes';
                    
                    // Get marks value
                    $marksObtained = null;
                    if (!$isAbsent && isset($row['marks_obtained']) && is_numeric($row['marks_obtained'])) {
                        $marksObtained = (float)$row['marks_obtained'];
                    }
                    
                    // Calculate grade
                    $grade = null;
                    if ($isAbsent) {
                        $grade = 'AB';
                    } elseif ($marksObtained !== null) {
                        $percentage = ($marksObtained / $this->exam->total_marks) * 100;
                        
                        $gradeScale = ExamGradeScale::where('min_percentage', '<=', $percentage)
                            ->where('max_percentage', '>=', $percentage)
                            ->where('school_id', $this->exam->school_id)
                            ->first();
                            
                        if ($gradeScale) {
                            $grade = $gradeScale->grade;
                        }
                    }
                    
                    // Update or create the mark
                    Mark::updateOrCreate(
                        [
                            'exam_id' => $this->exam->id,
                            'subject_id' => $this->subject->id,
                            'student_id' => $student['id'],
                        ],
                        [
                            'marks_obtained' => $marksObtained,
                            'is_absent' => $isAbsent,
                            'remarks' => $row['remarks'] ?? null,
                            'grade' => $grade,
                            'entered_by' => $this->enteredBy,
                        ]
                    );
                    
                    $this->processedRows++;
                } catch (\Exception $e) {
                    $this->failedRows[] = [
                        'roll_number' => $row['roll_number'] ?? 'Unknown',
                        'error' => $e->getMessage()
                    ];
                }
            }
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get the validation rules that apply to the import.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.roll_number' => 'nullable|string',
            '*.admission_number' => 'nullable|string',
            '*.marks_obtained' => 'nullable|string',
            '*.absent' => 'nullable|string',
            '*.is_absent' => 'nullable|string',
            '*.remarks' => 'nullable|string',
        ];
    }
    
    /**
     * Custom validation messages.
     *
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.marks_obtained.numeric' => 'Marks must be a number or "AB" for absent.',
        ];
    }

    public function getProcessedRows(): int
    {
        return $this->processedRows;
    }

    public function getFailedRows(): array
    {
        return $this->failedRows;
    }
} 