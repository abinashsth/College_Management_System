<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\User;
use App\Models\Section;
use App\Models\AcademicSession;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\ExamRule;
use App\Models\ExamSupervisor;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ExamTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For testing purposes, we'll use dummy IDs if tables don't exist
        try {
            $adminId = 1; // Default admin ID
            
            // Try to get teacher IDs
            try {
                $teacherIds = User::role('Teacher')->pluck('id')->toArray();
            } catch (\Exception $e) {
                // Create dummy teacher IDs
                $teacherIds = [2, 3, 4, 5];
                echo "Warning: Teachers table not found. Using dummy teacher IDs.\n";
            }
            
            // Try to get class ID
            try {
                $classId = Classes::first()->id ?? 1;
            } catch (\Exception $e) {
                $classId = 1;
                echo "Warning: Classes table not found. Using dummy class ID.\n";
            }
            
            // Try to get section IDs
            try {
                $sectionIds = Section::where('class_id', $classId)->pluck('id')->toArray();
                if (empty($sectionIds)) {
                    throw new \Exception("No sections found for the class");
                }
            } catch (\Exception $e) {
                $sectionIds = [1, 2, 3];
                echo "Warning: Sections table not found or no sections for class. Using dummy section IDs.\n";
            }
            
            // Try to get subject IDs
            try {
                $subjectIds = Subject::pluck('id')->toArray();
                if (empty($subjectIds)) {
                    throw new \Exception("No subjects found");
                }
            } catch (\Exception $e) {
                $subjectIds = [1, 2, 3, 4, 5];
                echo "Warning: Subjects table not found. Using dummy subject IDs.\n";
            }
            
            // Try to get academic session ID
            try {
                $academicSessionId = AcademicSession::first()->id ?? 1;
            } catch (\Exception $e) {
                $academicSessionId = 1;
                echo "Warning: Academic sessions table not found. Using dummy session ID.\n";
            }
            
            // Seed global exam rules
            $this->seedGlobalRules($adminId);
            
            // Seed exams
            $examIds = $this->seedExams($classId, $subjectIds, $academicSessionId, $adminId);
            
            if (empty($examIds)) {
                echo "Failed to create exams.\n";
                return;
            }
            
            // Seed exam schedules
            $scheduleIds = $this->seedExamSchedules($examIds, $sectionIds, $adminId);
            
            if (empty($scheduleIds)) {
                echo "Failed to create exam schedules.\n";
                return;
            }
            
            // Seed exam supervisors
            $this->seedExamSupervisors($scheduleIds, $teacherIds, $adminId);
            
            // Seed exam-specific rules
            $this->seedExamRules($examIds, $adminId);
            
            echo "Exam tables seeded successfully.\n";
        } catch (\Exception $e) {
            echo "Error seeding exam tables: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Seed global exam rules.
     */
    private function seedGlobalRules($adminId): void
    {
        $globalRules = [
            [
                'title' => 'No Electronic Devices',
                'is_global' => true,
                'description' => 'Mobile phones, smartwatches, and other electronic devices are not allowed during the examination.',
                'is_mandatory' => true,
                'display_order' => 1,
                'category' => 'materials',
                'penalty_for_violation' => 'Confiscation of device and potential disqualification from the exam.',
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Arrival Time',
                'is_global' => true,
                'description' => 'Students must arrive at least 15 minutes before the scheduled start time.',
                'is_mandatory' => true,
                'display_order' => 2,
                'category' => 'timing',
                'penalty_for_violation' => 'Late arrivals may be denied entry.',
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'ID Requirement',
                'is_global' => true,
                'description' => 'Students must bring their college ID cards to the examination.',
                'is_mandatory' => true,
                'display_order' => 3,
                'category' => 'general',
                'penalty_for_violation' => 'No entry without proper identification.',
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'No Communication',
                'is_global' => true,
                'description' => 'Communication between students during examination is prohibited.',
                'is_mandatory' => true,
                'display_order' => 4,
                'category' => 'conduct',
                'penalty_for_violation' => 'Warning or expulsion from examination hall depending on severity.',
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Permitted Materials',
                'is_global' => true,
                'description' => 'Only blue or black pens, pencils, and approved calculators are allowed.',
                'is_mandatory' => false,
                'display_order' => 5,
                'category' => 'materials',
                'penalty_for_violation' => 'Unauthorized materials may be confiscated.',
                'is_active' => true,
                'created_by' => $adminId,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('exam_rules')->insert($globalRules);
    }

    /**
     * Seed exams.
     */
    private function seedExams($classId, $subjectIds, $academicSessionId, $adminId): array
    {
        try {
            $examTypes = ['midterm', 'final', 'quiz', 'assignment'];
            $examIds = [];

            // Create 5 exams
            for ($i = 1; $i <= 5; $i++) {
                try {
                    $examDate = Carbon::now()->addDays(rand(10, 60))->format('Y-m-d');
                    $startTime = sprintf('%02d:%02d', rand(8, 15), rand(0, 59));
                    $durationMinutes = rand(60, 180);
                    $endTime = Carbon::createFromFormat('H:i', $startTime)->addMinutes($durationMinutes)->format('H:i');
                    
                    $examType = $examTypes[array_rand($examTypes)];
                    $totalMarks = ($examType == 'midterm' || $examType == 'final') ? 100 : 20;
                    $passingMarks = $totalMarks * 0.4;

                    $examId = DB::table('exams')->insertGetId([
                        'title' => ucfirst($examType) . ' Examination - ' . Str::random(5),
                        'description' => 'This is a sample ' . $examType . ' examination for testing purposes.',
                        'exam_date' => $examDate,
                        'class_id' => $classId,
                        'subject_id' => $subjectIds[array_rand($subjectIds)],
                        'academic_session_id' => $academicSessionId,
                        'exam_type' => $examType,
                        'semester' => 'Spring ' . date('Y'),
                        'duration_minutes' => $durationMinutes,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'location' => 'Main Campus',
                        'room_number' => 'Room ' . rand(101, 305),
                        'total_marks' => $totalMarks,
                        'passing_marks' => $passingMarks,
                        'registration_deadline' => Carbon::parse($examDate)->subDays(5)->format('Y-m-d'),
                        'result_date' => Carbon::parse($examDate)->addDays(10)->format('Y-m-d'),
                        'weight_percentage' => ($examType == 'midterm') ? 30 : (($examType == 'final') ? 50 : (($examType == 'quiz') ? 10 : 15)),
                        'grading_scale' => 'Standard',
                        'is_published' => false,
                        'is_active' => true,
                        'created_by' => $adminId,
                        'updated_by' => $adminId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    $examIds[] = $examId;
                    echo "Created exam ID: $examId\n";
                } catch (\Exception $e) {
                    echo "Error creating exam {$i}: " . $e->getMessage() . "\n";
                }
            }

            return $examIds;
        } catch (\Exception $e) {
            echo "Error in seedExams: " . $e->getMessage() . "\n";
            return [];
        }
    }

    /**
     * Seed exam schedules.
     */
    private function seedExamSchedules($examIds, $sectionIds, $adminId): array
    {
        try {
            $scheduleIds = [];
            $statuses = ['scheduled', 'scheduled', 'scheduled', 'in_progress', 'completed'];

            foreach ($examIds as $examId) {
                try {
                    $exam = DB::table('exams')->where('id', $examId)->first();
                    if (!$exam) {
                        echo "Exam with ID $examId not found\n";
                        continue;
                    }
                    
                    // Create a schedule for each section
                    foreach ($sectionIds as $sectionId) {
                        try {
                            $examDate = $exam->exam_date;
                            $status = $statuses[array_rand($statuses)];
                            
                            $scheduleId = DB::table('exam_schedules')->insertGetId([
                                'exam_id' => $examId,
                                'section_id' => $sectionId,
                                'exam_date' => $examDate,
                                'start_time' => $exam->start_time,
                                'end_time' => $exam->end_time,
                                'location' => $exam->location,
                                'room_number' => $exam->room_number,
                                'seating_capacity' => rand(30, 60),
                                'is_rescheduled' => false,
                                'reschedule_reason' => null,
                                'status' => $status,
                                'notes' => 'This is a scheduled exam for section ' . $sectionId,
                                'created_by' => $adminId,
                                'updated_by' => $adminId,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);

                            $scheduleIds[] = $scheduleId;
                            echo "Created schedule ID: $scheduleId for exam $examId, section $sectionId\n";
                        } catch (\Exception $e) {
                            echo "Error creating schedule for exam $examId, section $sectionId: " . $e->getMessage() . "\n";
                        }
                    }
                } catch (\Exception $e) {
                    echo "Error processing exam $examId: " . $e->getMessage() . "\n";
                }
            }

            return $scheduleIds;
        } catch (\Exception $e) {
            echo "Error in seedExamSchedules: " . $e->getMessage() . "\n";
            return [];
        }
    }

    /**
     * Seed exam supervisors.
     */
    private function seedExamSupervisors($scheduleIds, $teacherIds, $adminId): void
    {
        $roles = ['chief_supervisor', 'supervisor', 'assistant_supervisor', 'invigilator'];

        foreach ($scheduleIds as $scheduleId) {
            $schedule = DB::table('exam_schedules')->where('id', $scheduleId)->first();
            
            // Assign 2-4 supervisors to each schedule
            $supervisorCount = rand(2, 4);
            $assignedTeachers = [];
            
            for ($i = 0; $i < $supervisorCount; $i++) {
                // Find an unassigned teacher
                $teacherId = null;
                while (true) {
                    $teacherId = $teacherIds[array_rand($teacherIds)];
                    if (!in_array($teacherId, $assignedTeachers)) {
                        $assignedTeachers[] = $teacherId;
                        break;
                    }
                    
                    // If we've tried all teachers, break
                    if (count($assignedTeachers) >= count($teacherIds)) {
                        break;
                    }
                }
                
                // If we couldn't find an unassigned teacher, break
                if ($teacherId === null) {
                    break;
                }
                
                // Choose a role based on the supervisor index
                $role = ($i === 0) ? 'chief_supervisor' : $roles[array_rand(array_slice($roles, 1))];
                
                // Calculate reporting time (30 minutes before exam)
                $reportingTime = Carbon::createFromFormat('H:i', $schedule->start_time)
                    ->subMinutes(30)
                    ->format('H:i');
                
                // Calculate leaving time (15 minutes after exam)
                $leavingTime = Carbon::createFromFormat('H:i', $schedule->end_time)
                    ->addMinutes(15)
                    ->format('H:i');
                
                // Create supervisor assignment
                DB::table('exam_supervisors')->insert([
                    'exam_schedule_id' => $scheduleId,
                    'user_id' => $teacherId,
                    'role' => $role,
                    'reporting_time' => $reportingTime,
                    'leaving_time' => $leavingTime,
                    'is_confirmed' => (bool)rand(0, 1),
                    'confirmation_time' => (bool)rand(0, 1) ? now() : null,
                    'is_attended' => false,
                    'responsibilities' => 'Supervise the exam and ensure proper conduct',
                    'notes' => 'Additional instructions for ' . $role,
                    'assigned_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Seed exam rules.
     */
    private function seedExamRules($examIds, $adminId): void
    {
        $categories = ['general', 'conduct', 'materials', 'timing', 'grading'];
        
        foreach ($examIds as $examId) {
            // Create 1-3 specific rules for each exam
            $ruleCount = rand(1, 3);
            
            for ($i = 0; $i < $ruleCount; $i++) {
                $category = $categories[array_rand($categories)];
                
                DB::table('exam_rules')->insert([
                    'title' => 'Exam Specific Rule - ' . Str::random(5),
                    'exam_id' => $examId,
                    'is_global' => false,
                    'description' => 'This is a specific rule for this examination.',
                    'is_mandatory' => (bool)rand(0, 1),
                    'display_order' => 10 + $i,
                    'category' => $category,
                    'penalty_for_violation' => 'Penalties will be applied according to the severity of the violation.',
                    'is_active' => true,
                    'created_by' => $adminId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
