<?php

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
    protected $exam;
    protected $subject;
    protected $withStudentInfo;
    
    /**
     * Create a new export instance.
     *
     * @param Exam $exam
     * @param Subject $subject
     * @param bool $withStudentInfo
     * @return void
     */
    public function __construct(Exam $exam, Subject $subject, bool $withStudentInfo = true)
    {
        $this->exam = $exam;
        $this->subject = $subject;
        $this->withStudentInfo = $withStudentInfo;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Get all active students in the class
        $students = Student::where('class_id', $this->exam->class_id)
            ->where('enrollment_status', 'active')
            ->orderBy('roll_number')
            ->get();
        
        // Get all marks for these students in this exam and subject
        $marksData = Mark::where('exam_id', $this->exam->id)
            ->where('subject_id', $this->subject->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()
            ->keyBy('student_id');
        
        // Combine student data with marks
        return $students->map(function ($student) use ($marksData) {
            $mark = $marksData->get($student->id);
            
            $student->mark_data = [
                'marks_obtained' => $mark ? ($mark->is_absent ? 'AB' : $mark->marks_obtained) : null,
                'is_absent' => $mark ? $mark->is_absent : false,
                'grade' => $mark ? $mark->grade : null,
                'remarks' => $mark ? $mark->remarks : null,
            ];
            
            return $student;
        });
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        $headings = [
            'Roll No.',
            'Admission No.',
        ];
        
        if ($this->withStudentInfo) {
            $headings = array_merge($headings, [
                'Student Name',
            ]);
        }
        
        return array_merge($headings, [
            'Marks Obtained',
            'Grade',
            'Absent',
            'Remarks'
        ]);
    }
    
    /**
     * @param mixed $student
     * @return array
     */
    public function map($student): array
    {
        $data = [
            $student->roll_number,
            $student->admission_number,
        ];
        
        if ($this->withStudentInfo) {
            $data[] = $student->name;
        }
        
        return array_merge($data, [
            $student->mark_data['marks_obtained'],
            $student->mark_data['grade'],
            $student->mark_data['is_absent'] ? 'Yes' : 'No',
            $student->mark_data['remarks'],
        ]);
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
        $sheet->setCellValue('A1', 'Exam: ' . $this->exam->name . ' - Subject: ' . $this->subject->name);
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(14);
    }
} 