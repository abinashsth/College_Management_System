<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Program;
use App\Models\User;
use App\Models\Classes;
use App\Models\Department;
use App\Models\AcademicSession;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AdmissionController extends Controller
{
    /**
     * Constructor to add middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'permission:manage students'])->except(['apply', 'submitApplication']);
    }

    /**
     * Display a listing of applications.
     */
    public function index(Request $request)
    {
        try {
            $query = Student::query();
            
            // Filter by enrollment status
            if ($request->has('status') && $request->status) {
                $query->where('enrollment_status', $request->status);
            } else {
                // Default to showing only applications (not admitted students)
                $query->whereIn('enrollment_status', ['applied']);
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
            
            // Search by name or student_id
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }
            
            $applications = $query->with(['program', 'department'])
                                ->orderBy('created_at', 'desc')
                                ->paginate(10);
                
            $programs = Program::all();
            $departments = Department::all();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 5, $currentYear + 1);
            
            return view('admissions.index', compact('applications', 'programs', 'departments', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error fetching applications: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load applications: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new application.
     */
    public function create()
    {
        try {
            $programs = Program::all();
            $departments = Department::all();
            $classes = Classes::all();
            $academicSessions = AcademicSession::where('is_current', true)->get();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 5, $currentYear + 1);
            
            return view('admissions.create', compact('programs', 'departments', 'classes', 'academicSessions', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error loading create application form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load application form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created application.
     */
    public function store(Request $request)
    {
        // Log the incoming request data
        Log::info('Admission application submitted with data: ' . json_encode($request->all()));
        
        try {
            // Only validate fields that are required for the Student model
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:students',
                'password' => 'required|string|min:8|confirmed',
                'gender' => 'required|in:male,female,other',
                'dob' => 'required|date',
                'student_address' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'program_id' => 'required|exists:programs,id',
                'department_id' => 'required|exists:departments,id',
                'batch_year' => 'required|integer',
                'profile_photo' => 'nullable|image|max:2048',
                'father_name' => 'required|string|max:255',
                'mother_name' => 'required|string|max:255',
                'last_qualification' => 'nullable|string|max:255',
                'last_qualification_marks' => 'nullable|string|max:100',
                'enrollment_status' => 'required|in:applied,admitted',
            ]);
            
            Log::info('Validation passed for admission application');
            
            DB::beginTransaction();
            
            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')->store('students', 'public');
                Log::info('Profile photo uploaded to: ' . $profilePhotoPath);
            }
            
            // Get program code for student ID generation
            $program = Program::findOrFail($request->program_id);
            $programCode = $program->code ?? substr($program->name, 0, 3);
            
            // Generate student ID based on program and batch year
            $studentsCount = Student::where('program_id', $request->program_id)
                                  ->where('batch_year', $request->batch_year)
                                  ->count();
                                  
            $studentId = Student::generateStudentId($programCode, $request->batch_year, $studentsCount);
            Log::info('Generated student ID: ' . $studentId);
            
            // Create the student record with only fields in the fillable array
            $studentData = [
                'student_id' => $studentId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'dob' => $request->dob,
                'student_address' => $request->student_address,
                'phone_number' => $request->phone_number,
                'city' => $request->city,
                'state' => $request->state,
                'program_id' => $request->program_id,
                'department_id' => $request->department_id,
                'batch_year' => $request->batch_year,
                'profile_photo' => $profilePhotoPath,
                'enrollment_status' => $request->enrollment_status,
                'admission_date' => $request->enrollment_status === 'admitted' ? now() : null,
                'last_qualification' => $request->last_qualification,
                'last_qualification_marks' => $request->last_qualification_marks,
            ];
            
            // Add optional fields if they exist in the request
            if ($request->has('father_name')) {
                $studentData['father_name'] = $request->father_name;
            }
            
            if ($request->has('mother_name')) {
                $studentData['mother_name'] = $request->mother_name;
            }
            
            Log::info('Attempting to create student record with data: ' . json_encode($studentData));
            $student = Student::create($studentData);
            
            Log::info('Student record created with ID: ' . $student->id);
            
            // Create user account if the student is admitted
            if ($request->enrollment_status === 'admitted') {
                $user = User::create([
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'email' => $student->email,
                    'password' => Hash::make($request->password),
                ]);
                $user->assignRole('student');
                Log::info('User account created for admitted student');
            }
            
            DB::commit();
            Log::info('Admission application process completed successfully');
            return redirect()->route('admissions.index')->with('success', 'Student application created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed for admission application: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating student application: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Failed to create student application: ' . $e->getMessage());
        }
    }

    /**
     * Show the application details.
     */
    public function show(Student $application)
    {
        try {
            $application->load(['program', 'department', 'class', 'academicSession']);
            return view('admissions.show', compact('application'));
        } catch (\Exception $e) {
            Log::error('Error showing application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load application details: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the application.
     */
    public function edit(Student $application)
    {
        try {
            $programs = Program::all();
            $departments = Department::all();
            $classes = Classes::all();
            $academicSessions = AcademicSession::all();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 5, $currentYear + 1);
            
            return view('admissions.edit', compact('application', 'programs', 'departments', 'classes', 'academicSessions', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error loading edit application form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load application edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the application.
     */
    public function update(Request $request, Student $application)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('students')->ignore($application->id),
            ],
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'student_address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'program_id' => 'required|exists:programs,id',
            'department_id' => 'required|exists:departments,id',
            'batch_year' => 'required|integer',
            'profile_photo' => 'nullable|image|max:2048',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'enrollment_status' => 'required|in:applied,admitted,active,inactive,graduated,transferred,withdrawn,expelled',
            'last_qualification' => 'nullable|string|max:255',
            'last_qualification_marks' => 'nullable|string|max:100',
        ]);

        try {
            DB::beginTransaction();
            
            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old profile photo
                if ($application->profile_photo) {
                    Storage::disk('public')->delete($application->profile_photo);
                }
                
                $profilePhotoPath = $request->file('profile_photo')->store('students', 'public');
                $application->profile_photo = $profilePhotoPath;
            }
            
            // Update student record
            $application->first_name = $request->first_name;
            $application->last_name = $request->last_name;
            $application->email = $request->email;
            $application->gender = $request->gender;
            $application->dob = $request->dob;
            $application->father_name = $request->father_name;
            $application->mother_name = $request->mother_name;
            $application->student_address = $request->student_address;
            $application->phone_number = $request->phone_number;
            $application->city = $request->city;
            $application->state = $request->state;
            $application->program_id = $request->program_id;
            $application->department_id = $request->department_id;
            $application->batch_year = $request->batch_year;
            $application->class_id = $request->class_id;
            $application->academic_session_id = $request->academic_session_id;
            $application->current_semester = $request->current_semester;
            $application->emergency_contact_name = $request->emergency_contact_name;
            $application->emergency_contact_number = $request->emergency_contact_number;
            $application->emergency_contact_relationship = $request->emergency_contact_relationship;
            $application->guardian_name = $request->guardian_name;
            $application->guardian_relation = $request->guardian_relation;
            $application->guardian_contact = $request->guardian_contact;
            $application->guardian_address = $request->guardian_address;
            $application->guardian_occupation = $request->guardian_occupation;
            $application->previous_education = $request->previous_education;
            $application->last_qualification = $request->last_qualification;
            $application->last_qualification_marks = $request->last_qualification_marks;
            $application->medical_information = $request->medical_information;
            $application->remarks = $request->remarks;
            
            // Handle status changes
            $oldStatus = $application->enrollment_status;
            $application->enrollment_status = $request->enrollment_status;
            
            // If status changed to admitted, update admission date
            if ($oldStatus === 'applied' && $request->enrollment_status === 'admitted') {
                $application->admission_date = now();
                
                // Create a user account for the newly admitted student
                if (!User::where('email', $application->email)->exists()) {
                    $user = User::create([
                        'name' => $application->first_name . ' ' . $application->last_name,
                        'email' => $application->email,
                        'password' => $application->password, // Password is already hashed
                    ]);
                    $user->assignRole('student');
                }
            }
            
            $application->save();
            
            // Update the user account if it exists
            if ($user = User::where('email', $application->getOriginal('email'))->first()) {
                $user->update([
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $application->email,
                ]);
            }
            
            DB::commit();
            return redirect()->route('admissions.index')->with('success', 'Student application updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating student application: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to update student application: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified application.
     */
    public function destroy(Student $application)
    {
        try {
            DB::beginTransaction();
            
            // Delete profile photo if exists
            if ($application->profile_photo) {
                Storage::disk('public')->delete($application->profile_photo);
            }
            
            // Delete associated user account if exists
            if ($user = User::where('email', $application->email)->first()) {
                $user->delete();
            }
            
            $application->delete();
            
            DB::commit();
            return redirect()->route('admissions.index')->with('success', 'Student application deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting student application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete student application: ' . $e->getMessage());
        }
    }

    /**
     * Show the public application form.
     */
    public function apply()
    {
        try {
            $programs = Program::all();
            $departments = Department::all();
            $currentYear = date('Y');
            $batchYears = range($currentYear - 1, $currentYear + 1);
            
            return view('admissions.apply', compact('programs', 'departments', 'batchYears'));
        } catch (\Exception $e) {
            Log::error('Error loading public application form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load application form: ' . $e->getMessage());
        }
    }

    /**
     * Submit a public application form.
     */
    public function submitApplication(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'student_address' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'program_id' => 'required|exists:programs,id',
            'department_id' => 'required|exists:departments,id',
            'batch_year' => 'required|integer',
            'profile_photo' => 'nullable|image|max:2048',
            'previous_education' => 'required|string',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_number' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:255',
            'guardian_name' => 'required|string|max:255',
            'guardian_relation' => 'required|string|max:255',
            'guardian_contact' => 'required|string|max:20',
            'guardian_address' => 'required|string',
            'guardian_occupation' => 'required|string|max:255',
            'medical_information' => 'nullable|string',
            'documents.*' => 'nullable|file|max:5120',
            'declaration' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();
            
            // Handle profile photo upload
            $profilePhotoPath = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhotoPath = $request->file('profile_photo')->store('students', 'public');
            }
            
            // Handle document uploads
            $documents = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $file) {
                    $path = $file->store('student_documents', 'public');
                    $documents[] = [
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_at' => now()->toDateTimeString(),
                    ];
                }
            }
            
            // Create the student record
            $student = new Student();
            $student->first_name = $request->first_name;
            $student->last_name = $request->last_name;
            $student->email = $request->email;
            // Generate a random password for now, they'll set their real password later
            $student->password = Hash::make(Str::random(10));
            $student->gender = $request->gender;
            $student->dob = $request->dob;
            $student->student_address = $request->student_address;
            $student->phone_number = $request->phone_number;
            $student->city = $request->city;
            $student->state = $request->state;
            $student->program_id = $request->program_id;
            $student->department_id = $request->department_id;
            $student->batch_year = $request->batch_year;
            $student->profile_photo = $profilePhotoPath;
            $student->previous_education = $request->previous_education;
            $student->emergency_contact_name = $request->emergency_contact_name;
            $student->emergency_contact_number = $request->emergency_contact_number;
            $student->emergency_contact_relationship = $request->emergency_contact_relationship;
            $student->guardian_name = $request->guardian_name;
            $student->guardian_relation = $request->guardian_relation;
            $student->guardian_contact = $request->guardian_contact;
            $student->guardian_address = $request->guardian_address;
            $student->guardian_occupation = $request->guardian_occupation;
            $student->medical_information = $request->medical_information;
            $student->documents = $documents;
            $student->enrollment_status = 'applied';
            $student->save();
            
            DB::commit();
            return redirect()->route('admissions.thanks')->with('success', 'Your application has been submitted successfully. We will contact you soon.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error submitting application: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to submit application: ' . $e->getMessage());
        }
    }

    /**
     * Show the thank you page after application submission.
     */
    public function thanks()
    {
        return view('admissions.thanks');
    }

    /**
     * Show the document verification form.
     */
    public function verifyDocuments(Student $application)
    {
        try {
            return view('admissions.verify-documents', compact('application'));
        } catch (\Exception $e) {
            Log::error('Error loading document verification form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load document verification form: ' . $e->getMessage());
        }
    }

    /**
     * Process the document verification.
     */
    public function processDocumentVerification(Request $request, Student $application)
    {
        $request->validate([
            'documents.*' => 'nullable|file|max:10240',
            'document_types.*' => 'required|string',
            'verification_notes' => 'nullable|string',
            'documents_verified' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();
            
            // Handle document uploads
            if ($request->hasFile('documents')) {
                $documents = $application->documents ?? [];
                
                foreach ($request->file('documents') as $index => $file) {
                    $documentType = $request->document_types[$index] ?? 'other';
                    $path = $file->store('student_documents/' . $application->id, 'public');
                    
                    $documents[] = [
                        'type' => $documentType,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'uploaded_at' => now()->toDateTimeString(),
                    ];
                }
                
                $application->documents = $documents;
            }
            
            // Update verification status
            if ($request->has('documents_verified') && $request->documents_verified) {
                $application->documents_verified_at = now();
            }
            
            // Update remarks
            if ($request->has('verification_notes')) {
                $application->remarks = $application->remarks . "\n\nDocument Verification Notes: " . $request->verification_notes;
            }
            
            $application->save();
            
            DB::commit();
            return redirect()->route('admissions.show', $application)->with('success', 'Documents verified successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error verifying documents: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to verify documents: ' . $e->getMessage());
        }
    }

    /**
     * Generate a student ID.
     */
    public function generateId(Student $application)
    {
        try {
            if ($application->student_id) {
                return redirect()->back()->with('warning', 'Student already has an ID: ' . $application->student_id);
            }
            
            // Get program code
            $program = Program::find($application->program_id);
            $programCode = $program->code ?? substr($program->name, 0, 3);
            
            // Count students with same program and batch
            $studentsCount = Student::where('program_id', $application->program_id)
                                  ->where('batch_year', $application->batch_year)
                                  ->where('id', '!=', $application->id)
                                  ->count();
                                  
            // Generate student ID
            $studentId = Student::generateStudentId($programCode, $application->batch_year, $studentsCount);
            
            // Update student record
            $application->student_id = $studentId;
            $application->save();
            
            return redirect()->back()->with('success', 'Student ID generated successfully: ' . $studentId);
        } catch (\Exception $e) {
            Log::error('Error generating student ID: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to generate student ID: ' . $e->getMessage());
        }
    }
    
    /**
     * Admit the student.
     */
    public function admit(Student $application)
    {
        try {
            DB::beginTransaction();
            
            if ($application->enrollment_status !== 'applied') {
                return redirect()->back()->with('warning', 'Student is not in the application status.');
            }
            
            // Update status to admitted
            $application->enrollment_status = 'admitted';
            $application->admission_date = now();
            $application->save();
            
            // Create a user account for the student
            if (!User::where('email', $application->email)->exists()) {
                $user = User::create([
                    'name' => $application->first_name . ' ' . $application->last_name,
                    'email' => $application->email,
                    'password' => $application->password, // Password is already hashed
                ]);
                $user->assignRole('student');
            }
            
            DB::commit();
            return redirect()->route('admissions.index')->with('success', 'Student admitted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error admitting student: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to admit student: ' . $e->getMessage());
        }
    }
    
    /**
     * Reject the application.
     */
    public function reject(Student $application)
    {
        try {
            if ($application->enrollment_status !== 'applied') {
                return redirect()->back()->with('warning', 'Student is not in the application status.');
            }
            
            // Update status to rejected
            $application->enrollment_status = 'withdrawn';
            $application->save();
            
            return redirect()->route('admissions.index')->with('success', 'Application rejected successfully.');
        } catch (\Exception $e) {
            Log::error('Error rejecting application: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    /**
     * Show the admission application form.
     *
     * @return \Illuminate\View\View
     */
    public function showApplicationForm()
    {
        $programs = Program::where('status', 'active')->orderBy('name')->get();
        $departments = Department::where('status', 'active')->orderBy('name')->get();
        
        // Generate batch years (current year and next 2 years)
        $currentYear = Carbon::now()->year;
        $batchYears = [$currentYear, $currentYear + 1, $currentYear + 2];
        
        return view('admissions.apply', compact('programs', 'departments', 'batchYears'));
    }
    
    /**
     * Admin view of all applications.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function listApplications(Request $request)
    {
        // This method requires authentication and authorization
        $query = Student::query()->where('enrollment_status', 'applied');
        
        // Apply filters
        if ($request->filled('program')) {
            $query->where('program_id', $request->program);
        }
        
        if ($request->filled('batch_year')) {
            $query->where('batch_year', $request->batch_year);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('application_no', 'like', "%{$search}%");
            });
        }
        
        // Get batch years with applications
        $batchYearsWithApplications = Student::distinct()
            ->where('enrollment_status', 'applied')
            ->pluck('batch_year')
            ->sort()
            ->values();
            
        $programs = Program::where('status', 'active')->orderBy('name')->get();
        $applications = $query->with(['program', 'department'])->latest('application_date')->paginate(15);
        
        return view('admin.admissions.index', compact('applications', 'programs', 'batchYearsWithApplications'));
    }
    
    /**
     * Show application details.
     *
     * @param  \App\Models\Student  $application
     * @return \Illuminate\View\View
     */
    public function showApplication(Student $application)
    {
        // Load relationships
        $application->load(['program', 'department']);
        
        return view('admin.admissions.show', compact('application'));
    }
    
    /**
     * Approve an application.
     *
     * @param  \App\Models\Student  $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approveApplication(Student $application)
    {
        try {
            DB::beginTransaction();
            
            // Get the program code for generating student ID
            $programCode = Program::find($application->program_id)->code ?? 'STD';
            
            // Generate a student ID based on program code, batch year and a sequence number
            $lastStudentId = Student::where('program_id', $application->program_id)
                ->where('batch_year', $application->batch_year)
                ->where('student_id', '!=', null)
                ->count();
                
            $sequenceNumber = str_pad($lastStudentId + 1, 3, '0', STR_PAD_LEFT);
            $batchYearSuffix = substr($application->batch_year, -2);
            
            $studentId = $programCode . $batchYearSuffix . $sequenceNumber;
            
            // Update the application
            $application->student_id = $studentId;
            $application->enrollment_status = 'enrolled';
            $application->enrollment_date = Carbon::now();
            $application->save();
            
            // Create a user account for the student
            $user = new User();
            $user->name = $application->first_name . ' ' . $application->last_name;
            $user->email = $application->email;
            $user->password = bcrypt($studentId); // Use student ID as initial password
            $user->role = 'student';
            $user->save();
            
            // Link the user to the student
            $application->user_id = $user->id;
            $application->save();
            
            DB::commit();
            
            // Send welcome email with credentials here
            
            return redirect()->route('admin.admissions.index')
                ->with('success', 'Application approved. Student ID: ' . $studentId);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Application approval error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['approval_error' => 'There was an error approving this application.']);
        }
    }
    
    /**
     * Reject an application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $application
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectApplication(Request $request, Student $application)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255',
        ]);
        
        try {
            // Update application status
            $application->enrollment_status = 'rejected';
            $application->rejection_reason = $request->rejection_reason;
            $application->rejection_date = Carbon::now();
            $application->save();
            
            // Send rejection email here
            
            return redirect()->route('admin.admissions.index')
                ->with('success', 'Application has been rejected.');
                
        } catch (\Exception $e) {
            Log::error('Application rejection error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['rejection_error' => 'There was an error rejecting this application.']);
        }
    }
}
