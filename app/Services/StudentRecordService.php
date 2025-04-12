<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentRecord;
use Illuminate\Support\Facades\Auth;

class StudentRecordService
{
    /**
     * Track changes to a student's personal information.
     *
     * @param Student $student
     * @param array $originalData
     * @param array $newData
     * @param string|null $reason
     * @return StudentRecord|null
     */
    public function trackPersonalInfoChanges(Student $student, array $originalData, array $newData, ?string $reason = null): ?StudentRecord
    {
        // Filter out only the personal information fields
        $personalFields = [
            'first_name', 'last_name', 'gender', 'dob', 'phone_number', 
            'email', 'student_address', 'city', 'state', 'profile_photo',
            'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relationship',
            'father_name', 'mother_name'
        ];
        
        $original = array_intersect_key($originalData, array_flip($personalFields));
        $new = array_intersect_key($newData, array_flip($personalFields));
        
        // Only create a record if there are actual changes
        if ($original != $new) {
            return $student->logChange(
                'personal',
                $new,
                $original,
                Auth::id(),
                $reason ?? 'Personal information updated',
                null
            );
        }
        
        return null;
    }

    /**
     * Track changes to a student's academic information.
     *
     * @param Student $student
     * @param array $originalData
     * @param array $newData
     * @param string|null $reason
     * @return StudentRecord|null
     */
    public function trackAcademicChanges(Student $student, array $originalData, array $newData, ?string $reason = null): ?StudentRecord
    {
        // Filter out only the academic information fields
        $academicFields = [
            'program_id', 'department_id', 'class_id', 'academic_session_id',
            'batch_year', 'current_semester', 'previous_education', 'last_qualification',
            'last_qualification_marks', 'years_of_study'
        ];
        
        $original = array_intersect_key($originalData, array_flip($academicFields));
        $new = array_intersect_key($newData, array_flip($academicFields));
        
        // Only create a record if there are actual changes
        if ($original != $new) {
            return $student->logChange(
                'academic',
                $new,
                $original,
                Auth::id(),
                $reason ?? 'Academic information updated',
                null
            );
        }
        
        return null;
    }

    /**
     * Track changes to a student's enrollment status.
     *
     * @param Student $student
     * @param string $originalStatus
     * @param string $newStatus
     * @param string|null $reason
     * @param string|null $notes
     * @return StudentRecord
     */
    public function trackEnrollmentChange(Student $student, string $originalStatus, string $newStatus, ?string $reason = null, ?string $notes = null): StudentRecord
    {
        return $student->logChange(
            'enrollment',
            ['enrollment_status' => $newStatus],
            ['enrollment_status' => $originalStatus],
            Auth::id(),
            $reason ?? "Enrollment status changed from {$originalStatus} to {$newStatus}",
            $notes
        );
    }

    /**
     * Add an attendance record for a student.
     *
     * @param Student $student
     * @param string $date
     * @param string $status
     * @param string|null $notes
     * @return StudentRecord
     */
    public function addAttendanceRecord(Student $student, string $date, string $status, ?string $notes = null): StudentRecord
    {
        return $student->logChange(
            'attendance',
            [
                'date' => $date,
                'status' => $status
            ],
            null,
            Auth::id(),
            "Attendance marked as {$status} for {$date}",
            $notes
        );
    }

    /**
     * Add a disciplinary record for a student.
     *
     * @param Student $student
     * @param string $incident
     * @param string $action
     * @param string $date
     * @param string|null $notes
     * @return StudentRecord
     */
    public function addDisciplinaryRecord(Student $student, string $incident, string $action, string $date, ?string $notes = null): StudentRecord
    {
        return $student->logChange(
            'disciplinary',
            [
                'incident' => $incident,
                'action' => $action,
                'date' => $date
            ],
            null,
            Auth::id(),
            "Disciplinary action: {$action} for {$incident}",
            $notes
        );
    }

    /**
     * Add an achievement record for a student.
     *
     * @param Student $student
     * @param string $achievement
     * @param string $date
     * @param string|null $notes
     * @return StudentRecord
     */
    public function addAchievementRecord(Student $student, string $achievement, string $date, ?string $notes = null): StudentRecord
    {
        return $student->logChange(
            'achievement',
            [
                'achievement' => $achievement,
                'date' => $date
            ],
            null,
            Auth::id(),
            "Achievement recorded: {$achievement}",
            $notes
        );
    }

    /**
     * Add a medical record for a student.
     *
     * @param Student $student
     * @param string $medicalInfo
     * @param string $date
     * @param string|null $notes
     * @return StudentRecord
     */
    public function addMedicalRecord(Student $student, string $medicalInfo, string $date, ?string $notes = null): StudentRecord
    {
        return $student->logChange(
            'medical',
            [
                'medical_info' => $medicalInfo,
                'date' => $date
            ],
            null,
            Auth::id(),
            "Medical information updated",
            $notes
        );
    }

    /**
     * Add a general note to a student's record.
     *
     * @param Student $student
     * @param string $note
     * @param string|null $subject
     * @return StudentRecord
     */
    public function addNote(Student $student, string $note, ?string $subject = null): StudentRecord
    {
        return $student->logChange(
            'note',
            [
                'note' => $note,
                'subject' => $subject
            ],
            null,
            Auth::id(),
            $subject ?? "General note added",
            $note
        );
    }
} 