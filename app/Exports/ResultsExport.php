<?php

namespace App\Exports;

use App\Models\Mark;
use App\Models\Student;
use App\Services\GpaCalculationService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ResultsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $classId;
    protected $examId;
    protected $sectionId;
    protected $gpaService;

    /**
     * Create a new export instance.
     *
     * @param int $classId
     * @param int $examId
     * @param int|null $sectionId
     * @param GpaCalculationService $gpaService
     * @return void
     */
    public function __construct(int $classId, int $examId, ?int $sectionId = null, GpaCalculationService $gpaService)
    {
        $this->classId = $classId;
        $this->examId = $examId;
        $this->sectionId = $sectionId;
        $this->gpaService = $gpaService;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $students = Student::whereHas('enrollments', function ($query) {
                $query->where('class_id', $this->classId);
                if ($this->sectionId) {
                    $query->where('section_id', $this->sectionId);
                }
            })
            ->with(['enrollments.section'])
            ->get();

        $results = collect();
        
        foreach ($students as $student) {
            $marks = Mark::where('student_id', $student->id)
                ->where('exam_id', $this->examId)
                ->with(['subject'])
                ->get();
                
            if ($marks->count() > 0) {
                $gpaResult = $this->gpaService->calculateGpa($marks);
                
                $totalObtained = 0;
                $totalPossible = 0;
                
                foreach ($marks as $mark) {
                    if (!$mark->is_absent) {
                        $totalObtained += $mark->marks_obtained;
                        $totalPossible += $mark->exam->total_marks;
                    }
                }
                
                $percentage = $totalPossible > 0 ? ($totalObtained / $totalPossible) * 100 : 0;
                
                $section = $student->enrollments->first()->section ?? null;
                
                $results->push([
                    'student' => $student,
                    'section' => $section,
                    'gpa' => $gpaResult['gpa'],
                    'passed' => $gpaResult['passed'],
                    'percentage' => $percentage,
                    'total_obtained' => $totalObtained,
                    'total_possible' => $totalPossible,
                    'marks' => $marks
                ]);
            }
        }
        
        return $results;
    }
    
    /**
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row['student']->id,
            $row['student']->name,
            $row['student']->roll_number,
            $row['section'] ? $row['section']->name : 'N/A',
            $row['total_obtained'],
            $row['total_possible'],
            number_format($row['percentage'], 2) . '%',
            $row['gpa'],
            $row['passed'] ? 'Pass' : 'Fail'
        ];
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Student Name',
            'Roll Number',
            'Section',
            'Total Marks Obtained',
            'Total Possible Marks',
            'Percentage',
            'GPA',
            'Result'
        ];
    }
    
    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
    
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Results Report';
    }
} 