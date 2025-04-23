<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentRecord;
use App\Models\User;
use App\Models\Program;
use App\Models\Department;
use App\Models\AcademicSession;
use App\Models\Classes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\StudentRecordsExport;
use App\Notifications\StudentRecordCreated;
use App\Notifications\StudentRecordUpdated;

class StudentRecordController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view students']);
    }

    /**
     * Display a listing of student records with filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', StudentRecord::class);

        $query = StudentRecord::with(['student', 'createdBy', 'updatedBy']);

        // Filter by student if provided
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by record type if provided
        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        // Filter by creation user if provided
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in title and description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle access control for medical records
        if (!Auth::user()->hasRole(['admin', 'principal', 'counselor', 'medical_staff'])) {
            $query->nonConfidential();
        }

        $records = $query->latest()->paginate(15);
        
        // Get data for filters
        $students = Student::orderByName()->get(['id', 'first_name', 'last_name', 'admission_number']);
        $recordTypes = StudentRecord::$recordTypes;
        $staff = User::whereHas('roles')->get(['id', 'name']);

        return view('student-records.index', compact(
            'records', 
            'students', 
            'recordTypes', 
            'staff'
        ));
    }

    /**
     * Show the form for creating a new student record.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function create(Student $student)
    {
        $this->authorize('create', StudentRecord::class);

        $recordTypes = StudentRecord::$recordTypes;
        
        // Load related data if needed
        $programs = Program::all();
        $departments = Department::all();
        $academicSessions = AcademicSession::all();

        return view('student-records.create', compact('student', 'recordTypes', 'programs', 'departments', 'academicSessions'));
    }

    /**
     * Store a newly created student record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Student $student)
    {
        $this->authorize('create', StudentRecord::class);

        $validated = $request->validate([
            'record_type' => 'required|string|in:' . implode(',', array_keys(StudentRecord::$recordTypes)),
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'record_data' => 'sometimes|array',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
        ]);

        // Handle attachment if provided
        $attachmentPath = null;
        $attachmentName = null;
        
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('student-records', 'public');
        }

        $record = StudentRecord::create([
            'student_id' => $student->id,
            'record_type' => $validated['record_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'record_data' => $validated['record_data'] ?? [],
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Notify relevant staff based on record type
        $notifiableUsers = $this->getNotifiableUsers($record);
        
        foreach ($notifiableUsers as $user) {
            $user->notify(new StudentRecordCreated($record, $student));
        }

        return redirect()->route('student-records.show-record', $record)
            ->with('success', 'Student record created successfully.');
    }

    /**
     * Display the student's records.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        $this->authorize('viewAny', StudentRecord::class);

        // Get records for this student with optional filters
        $query = StudentRecord::where('student_id', $student->id);
        
        // Filter by record type if requested
        if (request()->filled('record_type')) {
            $query->where('record_type', request('record_type'));
        }
        
        // Search in title and description
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Handle access control for medical records
        if (!Auth::user()->hasRole(['admin', 'principal', 'counselor', 'medical_staff'])) {
            $query->where('record_type', '!=', 'medical');
        }
        
        $records = $query->latest()->paginate(10);
        
        return view('student-records.show', compact('student', 'records'));
    }

    /**
     * Display the specified student record.
     *
     * @param  \App\Models\StudentRecord  $studentRecord
     * @return \Illuminate\Http\Response
     */
    public function showRecord(StudentRecord $studentRecord)
    {
        $this->authorize('view', $studentRecord);

        $studentRecord->load(['student', 'createdBy', 'updatedBy']);

        return view('student-records.show-record', [
            'record' => $studentRecord,
        ]);
    }

    /**
     * Show the form for editing the specified student record.
     *
     * @param  \App\Models\StudentRecord  $studentRecord
     * @return \Illuminate\Http\Response
     */
    public function edit(StudentRecord $studentRecord)
    {
        $this->authorize('update', $studentRecord);

        $recordTypes = StudentRecord::$recordTypes;

        return view('student-records.edit', [
            'record' => $studentRecord,
            'recordTypes' => $recordTypes,
        ]);
    }

    /**
     * Update the specified student record in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\StudentRecord  $studentRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, StudentRecord $studentRecord)
    {
        $this->authorize('update', $studentRecord);

        $validated = $request->validate([
            'record_type' => 'required|string|in:' . implode(',', array_keys(StudentRecord::$recordTypes)),
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'record_data' => 'sometimes|array',
            'attachment' => 'nullable|file|max:10240', // Max 10MB
            'remove_attachment' => 'nullable|boolean',
        ]);

        // Handle attachment if provided
        $attachmentPath = $studentRecord->attachment_path;
        $attachmentName = $studentRecord->attachment_name;
        
        if ($request->hasFile('attachment')) {
            // Delete old attachment if exists
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            
            $file = $request->file('attachment');
            $attachmentName = $file->getClientOriginalName();
            $attachmentPath = $file->store('student-records', 'public');
        } elseif ($request->boolean('remove_attachment')) {
            // If remove_attachment flag is true, delete the attachment
            if ($attachmentPath && Storage::disk('public')->exists($attachmentPath)) {
                Storage::disk('public')->delete($attachmentPath);
            }
            $attachmentPath = null;
            $attachmentName = null;
        }

        $studentRecord->update([
            'record_type' => $validated['record_type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'record_data' => $validated['record_data'] ?? [],
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachmentName,
            'updated_by' => Auth::id(),
        ]);

        // Notify relevant staff about the update
        $student = $studentRecord->student;
        $notifiableUsers = $this->getNotifiableUsers($studentRecord);
        
        foreach ($notifiableUsers as $user) {
            if ($user->id !== Auth::id()) {  // Don't notify the user who made the change
                $user->notify(new StudentRecordUpdated($studentRecord, $student));
            }
        }

        return redirect()->route('student-records.show', $studentRecord)
            ->with('success', 'Student record updated successfully.');
    }

    /**
     * Remove the specified student record from storage.
     *
     * @param  \App\Models\StudentRecord  $studentRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentRecord $studentRecord)
    {
        $this->authorize('delete', $studentRecord);

        // Delete attachment if exists
        if ($studentRecord->attachment_path && Storage::disk('public')->exists($studentRecord->attachment_path)) {
            Storage::disk('public')->delete($studentRecord->attachment_path);
        }

        $studentRecord->delete();

        return redirect()->route('student-records.index')
            ->with('success', 'Student record deleted successfully.');
    }

    /**
     * Export student records to Excel
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $this->authorize('export', StudentRecord::class);

        $query = StudentRecord::with(['student', 'createdBy', 'updatedBy']);

        // Apply the same filters as in the index method
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('record_type')) {
            $query->where('record_type', $request->record_type);
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle access control for medical records
        if (!Auth::user()->hasRole(['admin', 'principal', 'counselor', 'medical_staff'])) {
            $query->nonConfidential();
        }

        $records = $query->get();

        return Excel::download(
            new StudentRecordsExport($records),
            'student_records_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Export a student's records to PDF.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function exportPDF(Student $student)
    {
        $this->authorize('export', StudentRecord::class);

        // Get records for this student
        $records = StudentRecord::where('student_id', $student->id)
            ->with(['createdBy', 'updatedBy'])
            ->latest()
            ->get();
        
        // Handle access control for medical records
        if (!Auth::user()->hasRole(['admin', 'principal', 'counselor', 'medical_staff'])) {
            $records = $records->filter(function($record) {
                return $record->record_type !== 'medical';
            });
        }
        
        $pdf = Pdf::loadView('student-records.pdf', [
            'student' => $student,
            'records' => $records
        ]);
        
        return $pdf->download('student_records_' . $student->student_id . '_' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export a student's records to Excel.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function exportExcel(Student $student)
    {
        $this->authorize('export', StudentRecord::class);

        // Get records for this student
        $records = StudentRecord::where('student_id', $student->id)
            ->with(['createdBy', 'updatedBy'])
            ->latest()
            ->get();
        
        // Handle access control for medical records
        if (!Auth::user()->hasRole(['admin', 'principal', 'counselor', 'medical_staff'])) {
            $records = $records->filter(function($record) {
                return $record->record_type !== 'medical';
            });
        }

        return Excel::download(
            new StudentRecordsExport($records),
            'student_records_' . $student->student_id . '_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get users that should be notified about record changes based on record type.
     *
     * @param  \App\Models\StudentRecord  $record
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getNotifiableUsers(StudentRecord $record)
    {
        $roles = [];

        // Determine who should be notified based on record type
        switch ($record->record_type) {
            case 'medical':
                $roles = ['medical_staff', 'principal', 'counselor'];
                break;
            case 'disciplinary':
                $roles = ['principal', 'counselor', 'discipline_committee'];
                break;
            case 'academic':
                $roles = ['principal', 'class_teacher'];
                break;
            case 'attendance':
                $roles = ['class_teacher'];
                break;
            default:
                $roles = ['admin', 'principal'];
                break;
        }

        return User::role($roles)->get();
    }
} 