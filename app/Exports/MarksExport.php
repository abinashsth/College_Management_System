<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Mark;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MarksExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected int $examId;
    protected int $subjectId;
    protected $withStudentInfo;
    
    /**
     * Create a new export instance.
     *
     * @param int $examId
     * @param int $subjectId
     * @param bool $withStudentInfo
     * @return void
     */
    public function __construct(int $examId, int $subjectId, bool $withStudentInfo = true)
    {
        $this->examId = $examId;
        $this->subjectId = $subjectId;
        $this->withStudentInfo = $withStudentInfo;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Mark::with(['student', 'subject', 'exam'])
            ->where([
                'subject_id' => $this->subjectId,
                'exam_id' => $this->examId
            ])
            ->get();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            'Roll Number',
            'Student Name',
            'Marks',
            'Grade',
            'Grade Point',
            'Remarks',
            'Verified',
            'Last Updated'
        ];
        
        if ($this->withStudentInfo) {
            $headings = array_merge($headings, [
                'Admission No.',
            ]);
        }
        
        return array_merge($headings, [
            'Absent',
        ]);
    }
    
    /**
     * @param mixed $mark
     * @return array
     */
    public function map($mark): array
    {
        $data = [
            $mark->student->roll_number,
            $mark->student->name,
            $mark->marks,
            $mark->grade,
            $mark->grade_point,
            $mark->remarks,
            $mark->verified ? 'Yes' : 'No',
            $mark->updated_at->format('Y-m-d H:i:s')
        ];
        
        if ($this->withStudentInfo) {
            $data[] = $mark->student->admission_number;
        }
        
        $data[] = $mark->is_absent ? 'Yes' : 'No';
        
        return $data;
    }
    
    /**
     * @param Worksheet $sheet
     * @return void
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFCCCCCC');
        
        // Freeze the top row
        $sheet->freezePane('A2');
        
        // Set exam and subject info in header
        $sheet->mergeCells('A1:C1');
        $sheet->setCellValue('A1', 'Exam: ' . $this->examId . ' - Subject: ' . $this->subjectId);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
    }
} 