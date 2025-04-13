<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use App\Notifications\ClassCreated;
use App\Notifications\SectionCreated;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class ClassService
{
    /**
     * Create a new class with basic information.
     *
     * @param array $data
     * @return Classes
     */
    public function createClass(array $data): Classes
    {
        $class = Classes::create($data);
        
        // Notify admin users about the new class
        $admins = User::permission('manage classes')->get();
        Notification::send($admins, new ClassCreated($class));
        
        return $class;
    }
    
    /**
     * Create a new section within a class.
     *
     * @param array $data
     * @return Section
     */
    public function createSection(array $data): Section
    {
        $section = Section::create($data);
        
        // Notify admin users and teachers about the new section
        $admins = User::permission('manage sections')->get();
        
        if (!empty($data['teacher_id'])) {
            $teacher = User::find($data['teacher_id']);
            if ($teacher) {
                $teacher->notify(new SectionCreated($section));
            }
        }
        
        Notification::send($admins, new SectionCreated($section));
        
        return $section;
    }
    
    /**
     * Assign students to a class.
     *
     * @param Classes $class
     * @param array $studentIds
     * @return Collection
     */
    public function assignStudentsToClass(Classes $class, array $studentIds): Collection
    {
        $students = Student::whereIn('id', $studentIds)->get();
        
        // Use transaction to ensure all changes are successful or none
        DB::beginTransaction();
        
        try {
            foreach ($students as $student) {
                $oldClassId = $student->class_id;
                $student->class_id = $class->id;
                $student->save();
                
                // If student tracking/logging is needed, add code here to log the class change
                if ($oldClassId) {
                    $student->logChange(
                        'academic',
                        ['class_id' => $class->id],
                        ['class_id' => $oldClassId],
                        auth()->id(),
                        "Moved from class ID {$oldClassId} to class ID {$class->id}"
                    );
                }
            }
            
            DB::commit();
            return $students;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get class statistics and information.
     *
     * @param Classes $class
     * @return array
     */
    public function getClassStatistics(Classes $class): array
    {
        // Get students in the class
        $students = Student::where('class_id', $class->id)->get();
        
        // Get sections in the class
        $sections = Section::where('class_id', $class->id)->get();
        
        // Calculate gender distribution
        $maleCount = $students->where('gender', 'male')->count();
        $femaleCount = $students->where('gender', 'female')->count();
        $otherCount = $students->whereNotIn('gender', ['male', 'female'])->count();
        
        // Calculate enrollment status distribution
        $activeCount = $students->where('enrollment_status', 'active')->count();
        $inactiveCount = $students->where('enrollment_status', '!=', 'active')->count();
        
        // Calculate section statistics
        $sectionData = [];
        foreach ($sections as $section) {
            $sectionStudents = $students->count(); // In a real implementation, we'd filter by section
            
            $sectionData[] = [
                'section' => $section,
                'student_count' => $sectionStudents,
                'teacher' => $section->teacher,
                'utilization' => $section->capacity > 0 ? ($sectionStudents / $section->capacity) * 100 : 0,
            ];
        }
        
        return [
            'class' => $class,
            'total_students' => $students->count(),
            'total_sections' => $sections->count(),
            'gender_distribution' => [
                'male' => $maleCount,
                'female' => $femaleCount,
                'other' => $otherCount,
                'male_percentage' => $students->count() > 0 ? ($maleCount / $students->count()) * 100 : 0,
                'female_percentage' => $students->count() > 0 ? ($femaleCount / $students->count()) * 100 : 0,
                'other_percentage' => $students->count() > 0 ? ($otherCount / $students->count()) * 100 : 0,
            ],
            'enrollment_distribution' => [
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'active_percentage' => $students->count() > 0 ? ($activeCount / $students->count()) * 100 : 0,
                'inactive_percentage' => $students->count() > 0 ? ($inactiveCount / $students->count()) * 100 : 0,
            ],
            'section_data' => $sectionData,
            'capacity_utilization' => $class->capacity > 0 ? ($students->count() / $class->capacity) * 100 : 0,
        ];
    }
    
    /**
     * Get section-specific information and statistics.
     *
     * @param Section $section
     * @return array
     */
    public function getSectionDetails(Section $section): array
    {
        // In a real implementation, you'd get students specifically assigned to this section
        // For now, we'll use all students from the associated class
        $students = Student::where('class_id', $section->class_id)->get();
        
        // Get classroom allocations
        $classroomAllocations = $section->classroomAllocations;
        
        // Map classroom allocations by day for easier display
        $scheduleByDay = [
            'monday' => [],
            'tuesday' => [],
            'wednesday' => [],
            'thursday' => [],
            'friday' => [],
            'saturday' => [],
            'sunday' => [],
        ];
        
        foreach ($classroomAllocations as $allocation) {
            $day = strtolower($allocation->day);
            if (isset($scheduleByDay[$day])) {
                $scheduleByDay[$day][] = $allocation;
            }
        }
        
        return [
            'section' => $section,
            'class' => $section->class,
            'teacher' => $section->teacher,
            'student_count' => $students->count(),
            'capacity_utilization' => $section->capacity > 0 ? ($students->count() / $section->capacity) * 100 : 0,
            'classroom_allocations' => $classroomAllocations,
            'schedule_by_day' => $scheduleByDay,
        ];
    }
} 