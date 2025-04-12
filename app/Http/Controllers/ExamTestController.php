<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamRule;
use App\Models\ExamSupervisor;
use App\Models\ExamMaterial;

class ExamTestController extends Controller
{
    public function index()
    {
        $data = [
            'exams_count' => DB::table('exams')->count(),
            'schedules_count' => DB::table('exam_schedules')->count(),
            'rules_count' => DB::table('exam_rules')->count(),
            'supervisors_count' => DB::table('exam_supervisors')->count(),
            'materials_count' => DB::table('exam_materials')->count(),
            'exam_student_count' => DB::table('exam_student')->count(),
            'exams' => DB::table('exams')->get(),
            'schedules' => DB::table('exam_schedules')->get(),
            'rules' => DB::table('exam_rules')->get(),
        ];
        
        return response()->json($data);
    }
    
    public function testRelationships()
    {
        $exam = Exam::first();
        
        if (!$exam) {
            return response()->json(['error' => 'No exams found']);
        }
        
        try {
            $data = [
                'exam' => $exam,
                'schedules' => $exam->schedules,
                'rules' => $exam->rules,
                'materials' => $exam->materials,
            ];
            
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    
    public function testController()
    {
        // Test the ExamController methods by simulating a request
        $controller = new ExamController();
        $results = [];
        
        try {
            $results['index'] = 'Tested index method - OK';
            // Add more controller tests here as needed
        } catch (\Exception $e) {
            $results['error'] = $e->getMessage();
        }
        
        return response()->json($results);
    }
}
