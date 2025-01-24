<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'mathematics_theory',
        'mathematics_practical',
        'programming_theory',
        'programming_practical',
        'oops_theory',
        'oops_practical',
        'data_structure_theory',
        'data_structure_practical',
        'organization_behavior_theory',
        'organization_behavior_practical',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    // Calculate total marks for each subject
    public function getSubjectTotals()
    {
        return [
            'mathematics' => $this->mathematics_theory + $this->mathematics_practical,
            'programming' => $this->programming_theory + $this->programming_practical,
            'oops' => $this->oops_theory + $this->oops_practical,
            'data_structure' => $this->data_structure_theory + $this->data_structure_practical,
            'organization_behavior' => $this->organization_behavior_theory + $this->organization_behavior_practical,
        ];
    }

    // Calculate total marks
    public function getTotalMarks()
    {
        return array_sum($this->getSubjectTotals());
    }

    // Calculate percentage
    public function getPercentage()
    {
        $totalMarks = $this->getTotalMarks();
        $maxMarks = 500; // Assuming each subject has 100 marks
        return ($totalMarks / $maxMarks) * 100;
    }
}
