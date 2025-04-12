<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Models\Program;
use App\Models\Department;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use App\Models\Classes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Services\StudentRecordService;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeStudent;
use App\Mail\StudentRegistrationConfirmation;

class StudentController extends Controller
{
    /**
     * Constructor with middleware.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view students']);
    }

    /**
     * Display a listing of students.
     */
    public function index(Request $request)
    {
        try {
            $query = Student::query();
            
            // Filter by enrollment status
            if ($request->has('status') && $request->status) {
                $query->where('enrollment_status', $request->status);
            } else {
                // Only active students by default
                $query->whereIn('enrollment_status', ['active', 'admitted']);
            }
            
            // Filter by program
            if ($request->has('program_id') && $request->program_id) {
                $query->where('program_id', $request->program_id);
            }
            
            // Filter by batch year
            if ($request->has('batch_year') && $request->batch_year) {
                $query->where('batch_year', $request->batch_year);
            }
            
            // Filter by department
            if ($request->has('department_id') && $request->department_id) {
                $query->where('department_id', $request->department_id);
            }
            
            // Filter by class
            if ($request->has('class_id') && $request->class_id) {
                $query->where('class_id', $request->class_id);
            }
            
            // Search by name, ID, or email
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            // Make sure we select all important fields including guardian_name
            $students = $query->with(['program', 'department', 'class', 'academicSession'])
                              ->select('id', 'first_name', 'last_name', 'student_id', 'registration_number', 
                                      'gender', 'program_id', 'department_id', 'class_id', 'academic_session_id', 
                                      'batch_year', 'admission_date', 'guardian_name', 'father_name', 'mother_name', 
                                      'fee_status', 'enrollment_status', 'profile_photo', 'created_at')
                              ->orderBy('created_at', 'desc')
                              ->paginate(10);
                
            $programs = Program::all();
            $departments = Department::all();
            $classes = Classes::all();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 5, $currentYear + 1);
            
            return view('students.index', compact('students', 'programs', 'departments', 'classes', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load students: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        try {
            $programs = Program::all();
            $departments = Department::all();
            $classes = Classes::all();
            $academicSessions = AcademicSession::all();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 5, $currentYear + 1);
            
            return view('students.create', compact('programs', 'departments', 'classes', 'academicSessions', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error loading create student form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load student creation form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created student resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // Validate student data
            $validated = $request->validate([
                'first_name' => 'required|string|max:50',
                'last_name' => 'required|string|max:50',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'gender' => 'required|in:male,female,other',
                'dob' => 'required|date|before:today',
                'phone_number' => 'required|string|max:20',
                'student_address' => 'required|string|max:255',
                'program_id' => 'required|exists:programs,id',
                'department_id' => 'required|exists:departments,id',
                'class_id' => 'required|exists:classes,id',
                'batch_year' => 'required|integer|digits:4',
                'admission_date' => 'required|date',
                'academic_session_id' => 'nullable|exists:academic_sessions,id',
                'years_of_study' => 'nullable|integer|min:1|max:10',
                'guardian_name' => 'required|string|max:100',
                'guardian_relation' => 'required|string|max:50',
                'guardian_contact' => 'nullable|string|max:20',
                'guardian_email' => 'nullable|email',
                'guardian_address' => 'nullable|string|max:255',
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            // Begin transaction
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Assign student role
            $user->assignRole('student');

            // Handle photo upload
            $photoPath = null;
            if ($request->hasFile('profile_photo')) {
                $photoPath = $request->file('profile_photo')->store('students/photos', 'public');
            }
            
            // Get department code for registration number
            $departmentId = $request->department_id;
            $department = Department::findOrFail($departmentId);
            $departmentCode = strtoupper(substr($department->name, 0, 3)); // First 3 letters of department name
            
            // Get batch year
            $batchYear = $request->batch_year ?? date('Y');
            
            // Get student count for the department in this batch year to generate sequential number
            $studentCount = Student::where('department_id', $department->id)
                                  ->where('batch_year', $batchYear)
                                  ->count();
            
            // Generate unique registration number
            $registrationNumber = Student::generateRegistrationNumber($departmentCode, $batchYear, $studentCount);

            // Create student record
            $student = Student::create([
                'user_id' => $user->id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'],
                'email' => $validated['email'],
                'dob' => $validated['dob'],
                'phone_number' => $validated['phone_number'],
                'student_address' => $validated['student_address'],
                'registration_number' => $registrationNumber,
                'admission_date' => $validated['admission_date'],
                'class_id' => $validated['class_id'],
                'academic_session_id' => $validated['academic_session_id'] ?? null,
                'department_id' => $departmentId,
                'program_id' => $validated['program_id'],
                'batch_year' => $batchYear,
                'years_of_study' => $validated['years_of_study'] ?? null,
                'guardian_name' => $validated['guardian_name'],
                'guardian_relation' => $validated['guardian_relation'],
                'guardian_contact' => $validated['guardian_contact'] ?? null,
                'guardian_email' => $validated['guardian_email'] ?? null,
                'guardian_address' => $validated['guardian_address'] ?? null,
                'profile_photo' => $photoPath,
                'enrollment_status' => 'active',
                'created_by' => auth()->id(),
            ]);

            // Handle document uploads
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $document) {
                    $path = $document->store('students/documents', 'public');
                    $student->documents()->create([
                        'name' => $document->getClientOriginalName(),
                        'path' => $path,
                        'type' => $document->getClientOriginalExtension(),
                        'uploaded_by' => auth()->id(),
                    ]);
                }
            }

            // Create fee structure for the student
            try {
                $this->createStudentFeeStructure($student);
            } catch (\Exception $e) {
                // Log error but continue with registration
                Log::warning('Failed to create fee structure: ' . $e->getMessage());
            }

            // Assign subjects based on class
            try {
                $this->assignSubjectsToStudent($student);
            } catch (\Exception $e) {
                // Log error but continue with registration
                Log::warning('Failed to assign subjects: ' . $e->getMessage());
            }

            DB::commit();

            // Send welcome email to student and guardian
            try {
                Mail::to($validated['email'])->send(new WelcomeStudent($student));
                if (!empty($validated['guardian_email'])) {
                    Mail::to($validated['guardian_email'])->send(new StudentRegistrationConfirmation($student));
                }
            } catch (\Exception $e) {
                // Just log email errors but don't fail the request
                \Log::warning('Failed to send welcome email: ' . $e->getMessage());
            }

            return redirect()->route('students.index')
                ->with('success', 'Student registered successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error
            \Log::error('Student registration error: ' . $e->getMessage());
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while registering the student. Please try again.');
        }
    }

    /**
     * Create fee structure for a student based on their class
     *
     * @param  \App\Models\Student  $student
     * @return void
     */
    private function createStudentFeeStructure(Student $student)
    {
        try {
            // Get fee structure for the class
            $feeStructure = \App\Models\FeeStructure::where('class_id', $student->class_id)
                ->where('academic_session_id', $student->academic_session_id)
                ->where('is_active', true)
                ->first();
                
            if (!$feeStructure) {
                throw new \Exception("No active fee structure found for the selected class and academic session");
            }
            
            // Get fee components
            $feeComponents = $feeStructure->feeComponents;
            
            if ($feeComponents->isEmpty()) {
                throw new \Exception("No fee components found in the fee structure");
            }
            
            // Create student fee record for each component
            foreach ($feeComponents as $component) {
                \App\Models\StudentFee::create([
                    'student_id' => $student->id,
                    'fee_structure_id' => $feeStructure->id,
                    'fee_component_id' => $component->id,
                    'amount' => $component->amount,
                    'due_date' => now()->addDays(30), // Default due date 30 days from now
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                ]);
            }
        } catch (\Exception $e) {
            // Just log the error but don't fail the whole registration process
            \Log::error('Error creating student fee structure: ' . $e->getMessage());
        }
    }

    /**
     * Assign subjects to student based on class
     *
     * @param  \App\Models\Student  $student
     * @return void
     */
    private function assignSubjectsToStudent(Student $student)
    {
        try {
            // Get all subjects for the class
            $subjects = \App\Models\Subject::where('class_id', $student->class_id)
                ->where('is_active', true)
                ->get();
                
            if ($subjects->isEmpty()) {
                \Log::warning('No active subjects found for class ID: ' . $student->class_id);
                return;
            }
            
            // Create student subject enrollment for each subject
            foreach ($subjects as $subject) {
                \App\Models\StudentSubject::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'academic_session_id' => $student->academic_session_id,
                    'enrollment_date' => now(),
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]);
            }
        } catch (\Exception $e) {
            // Just log the error but don't fail the whole registration process
            \Log::error('Error assigning subjects to student: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        try {
            $student->load(['program', 'department', 'class', 'academicSession', 'exams']);
            return view('students.show', compact('student'));
        } catch (\Exception $e) {
            Log::error('Error showing student: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load student details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        try {
            $programs = Program::all();
            $departments = Department::all();
            $classes = Classes::all();
            $academicSessions = AcademicSession::all();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 5, $currentYear + 1);
            
            return view('students.edit', compact('student', 'programs', 'departments', 'classes', 'academicSessions', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error loading edit student form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load student edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('students')->ignore($student->id),
            ],
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'student_address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'program_id' => 'required|exists:programs,id',
            'department_id' => 'required|exists:departments,id',
            'batch_year' => 'required|integer',
            'class_id' => 'required|exists:classes,id',
            'academic_session_id' => 'nullable|exists:academic_sessions,id',
            'current_semester' => 'nullable|string|max:10',
            'profile_photo' => 'nullable|image|max:2048',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'last_qualification' => 'nullable|string|max:255',
            'last_qualification_marks' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            
            // Store the original data for change tracking
            $originalData = $student->toArray();
            
            // Handle profile photo upload
            $profilePhotoPath = $student->profile_photo;
            if ($request->hasFile('profile_photo')) {
                // Delete the old profile photo if it exists
                if ($profilePhotoPath && Storage::disk('public')->exists($profilePhotoPath)) {
                    Storage::disk('public')->delete($profilePhotoPath);
                }
                $profilePhotoPath = $request->file('profile_photo')->store('students', 'public');
            }
            
            // Update student data
            $student->update([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'gender' => $request->gender,
                'dob' => $request->dob,
                'student_address' => $request->student_address,
                'phone_number' => $request->phone_number,
                'city' => $request->city,
                'state' => $request->state,
                'father_name' => $request->father_name,
                'mother_name' => $request->mother_name,
                'program_id' => $request->program_id,
                'department_id' => $request->department_id,
                'batch_year' => $request->batch_year,
                'class_id' => $request->class_id,
                'academic_session_id' => $request->academic_session_id,
                'profile_photo' => $profilePhotoPath,
                'enrollment_status' => $request->enrollment_status,
                'current_semester' => $request->current_semester,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_number' => $request->emergency_contact_number,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
                'guardian_name' => $request->guardian_name,
                'guardian_relation' => $request->guardian_relation,
                'guardian_contact' => $request->guardian_contact,
                'guardian_address' => $request->guardian_address,
                'guardian_occupation' => $request->guardian_occupation,
                'previous_education' => $request->previous_education,
                'last_qualification' => $request->last_qualification,
                'last_qualification_marks' => $request->last_qualification_marks,
                'medical_information' => $request->medical_information,
                'remarks' => $request->remarks,
            ]);
            
            // Update the user's name and email if they exist
            if ($student->user) {
                $student->user->update([
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'email' => $student->email,
                ]);
            }
            
            // Track changes using StudentRecordService
            $recordService = new StudentRecordService();
            $recordService->trackPersonalInfoChanges($student, $originalData, $student->toArray(), 'Student personal information updated');
            $recordService->trackAcademicChanges($student, $originalData, $student->toArray(), 'Student academic information updated');
            
            // Track enrollment status change if it was updated
            if ($request->has('enrollment_status') && $request->enrollment_status !== $originalData['enrollment_status']) {
                $recordService->trackEnrollmentChange(
                    $student, 
                    $originalData['enrollment_status'], 
                    $request->enrollment_status,
                    'Enrollment status updated',
                    $request->status_change_reason ?? null
                );
            }
            
            DB::commit();
            return redirect()->route('students.show', $student)->with('success', 'Student updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating student: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update student: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();
            
            // Delete profile photo if exists
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            
            // Delete associated user account if exists
            if ($user = User::where('email', $student->email)->first()) {
                $user->delete();
            }
            
            $student->delete();
            
            DB::commit();
            return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting student: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete student: ' . $e->getMessage());
        }
    }
}
