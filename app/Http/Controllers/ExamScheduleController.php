<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Section;
use App\Models\User;
use App\Services\ExamScheduleService;
use App\Exceptions\ExamException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExamScheduleController extends Controller
{
    /**
     * The exam schedule service instance.
     *
     * @var \App\Services\ExamScheduleService
     */
    protected $scheduleService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ExamScheduleService  $scheduleService
     * @return void
     */
    public function __construct(ExamScheduleService $scheduleService)
    {
        $this->middleware(['auth', 'permission:manage exam schedules']);
        $this->scheduleService = $scheduleService;
    }

    /**
     * Display a listing of exam schedules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $query = ExamSchedule::with(['exam', 'section', 'section.class', 'supervisors.user'])
                ->orderBy('exam_date', 'asc');
                
            // Apply filters if provided
            if ($request->filled('date')) {
                $query->where('exam_date', $request->date);
            }
            
            if ($request->filled('exam_id')) {
                $query->where('exam_id', $request->exam_id);
            }
            
            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
            }
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }
            
            $schedules = $query->paginate(15);
            
            // Data for filters
            $exams = Exam::where('is_active', true)->get();
            $sections = Section::all();
            
            return view('exam_schedules.index', compact('schedules', 'exams', 'sections'));
        } catch (\Exception $e) {
            Log::error('Error retrieving exam schedules: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while retrieving exam schedules. Please try again.');
        }
    }

    /**
     * Show the form for creating a new exam schedule.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            $examId = $request->exam_id;
            $exam = null;
            
            if ($examId) {
                $exam = Exam::findOrFail($examId);
            }
            
            $exams = Exam::where('is_active', true)->get();
            $sections = Section::all();
            
            return view('exam_schedules.create', compact('exams', 'sections', 'exam'));
        } catch (\Exception $e) {
            Log::error('Error loading create schedule form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while loading the form. Please try again.');
        }
    }

    /**
     * Store a newly created exam schedule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'section_id' => 'required|exists:sections,id',
                'exam_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'location' => 'nullable|string|max:255',
                'room_number' => 'nullable|string|max:50',
                'seating_capacity' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
                'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            ]);
            
            // Add creator
            $validated['created_by'] = Auth::id();
            
            // Use the service to create the schedule (includes conflict validation)
            $schedule = $this->scheduleService->createSchedule($validated);
            
            return redirect()
                ->route('exam-schedules.show', $schedule)
                ->with('success', 'Exam schedule created successfully');
                
        } catch (ExamException $e) {
            // Handle specific exam exceptions differently
            if ($e->getErrorType() === ExamException::TYPE_SCHEDULING_CONFLICT) {
                $context = $e->getContext();
                $detailedMessage = "Scheduling conflict detected! ";
                
                if (isset($context['section'])) {
                    $detailedMessage .= "Section {$context['section']} ";
                }
                
                if (isset($context['conflict_exam'])) {
                    $detailedMessage .= "already has \"{$context['conflict_exam']}\" ";
                }
                
                if (isset($context['conflict_date']) && isset($context['conflict_time'])) {
                    $detailedMessage .= "scheduled on {$context['conflict_date']} from {$context['conflict_time']}.";
                }
                
                return redirect()->back()->withInput()->with('error', $detailedMessage);
            }
            
            // Log the exception and show a generic message
            Log::error('Exam scheduling error: ' . $e->getMessage(), $e->getContext());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Log the error and return a generic message
            Log::error('Error creating exam schedule: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the exam schedule. Please try again.');
        }
    }

    /**
     * Display the specified exam schedule.
     *
     * @param  \App\Models\ExamSchedule  $examSchedule
     * @return \Illuminate\Http\Response
     */
    public function show(ExamSchedule $examSchedule)
    {
        try {
            $examSchedule->load([
                'exam', 
                'section', 
                'section.class', 
                'supervisors.user',
                'creator',
                'updater'
            ]);
            
            $students = $examSchedule->students();
            
            // Get available supervisors for this schedule using our service
            $scheduleData = [
                'exam_date' => $examSchedule->exam_date,
                'start_time' => $examSchedule->start_time,
                'end_time' => $examSchedule->end_time
            ];
            
            $availableSupervisors = $this->scheduleService->getAvailableSupervisors($scheduleData);
                
            $supervisorRoles = [
                'chief_supervisor' => 'Chief Supervisor',
                'supervisor' => 'Supervisor',
                'assistant_supervisor' => 'Assistant Supervisor',
                'invigilator' => 'Invigilator',
                'other' => 'Other'
            ];
            
            return view('exam_schedules.show', compact(
                'examSchedule', 
                'students', 
                'availableSupervisors',
                'supervisorRoles'
            ));
        } catch (\Exception $e) {
            Log::error('Error viewing exam schedule: ' . $e->getMessage());
            return redirect()->route('exam-schedules.index')
                ->with('error', 'An error occurred while retrieving the exam schedule details.');
        }
    }

    /**
     * Show the form for editing the specified exam schedule.
     *
     * @param  \App\Models\ExamSchedule  $examSchedule
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamSchedule $examSchedule)
    {
        try {
            $exams = Exam::where('is_active', true)->get();
            $sections = Section::all();
            
            return view('exam_schedules.edit', compact('examSchedule', 'exams', 'sections'));
        } catch (\Exception $e) {
            Log::error('Error loading edit schedule form: ' . $e->getMessage());
            return redirect()->route('exam-schedules.index')
                ->with('error', 'An error occurred while loading the edit form.');
        }
    }

    /**
     * Update the specified exam schedule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamSchedule  $examSchedule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamSchedule $examSchedule)
    {
        try {
            $validated = $request->validate([
                'exam_id' => 'required|exists:exams,id',
                'section_id' => 'required|exists:sections,id',
                'exam_date' => 'required|date',
                'start_time' => 'required',
                'end_time' => 'required|after:start_time',
                'location' => 'nullable|string|max:255',
                'room_number' => 'nullable|string|max:50',
                'seating_capacity' => 'nullable|integer|min:1',
                'notes' => 'nullable|string',
                'status' => 'required|in:scheduled,in-progress,completed,cancelled',
                'is_rescheduled' => 'boolean',
                'reschedule_reason' => 'nullable|string|required_if:is_rescheduled,1',
            ]);
            
            // Add updater
            $validated['updated_by'] = Auth::id();
            
            // Use service to update the schedule (includes conflict validation)
            $this->scheduleService->updateSchedule($examSchedule, $validated);
            
            return redirect()
                ->route('exam-schedules.show', $examSchedule)
                ->with('success', 'Exam schedule updated successfully');
                
        } catch (ExamException $e) {
            // Handle specific scheduling conflicts with detailed messages
            if ($e->getErrorType() === ExamException::TYPE_SCHEDULING_CONFLICT) {
                $context = $e->getContext();
                $detailedMessage = "Scheduling conflict detected! ";
                
                if (isset($context['section'])) {
                    $detailedMessage .= "Section {$context['section']} ";
                } elseif (isset($context['room'])) {
                    $detailedMessage .= "Room {$context['room']} ";
                }
                
                if (isset($context['conflict_exam'])) {
                    $detailedMessage .= "already has \"{$context['conflict_exam']}\" ";
                }
                
                if (isset($context['conflict_date']) && isset($context['conflict_time'])) {
                    $detailedMessage .= "scheduled on {$context['conflict_date']} from {$context['conflict_time']}.";
                }
                
                return redirect()->back()->withInput()->with('error', $detailedMessage);
            }
            
            // Log and show a user-friendly message for other types of exceptions
            Log::error('Exam schedule update error: ' . $e->getMessage(), $e->getContext());
            return redirect()->back()->withInput()->with('error', $e->getMessage());
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
            
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error updating exam schedule: ' . $e->getMessage());
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating the exam schedule. Please try again.');
        }
    }

    /**
     * Remove the specified exam schedule from storage.
     *
     * @param  \App\Models\ExamSchedule  $examSchedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamSchedule $examSchedule)
    {
        try {
            // Use transaction to ensure data integrity during deletion
            \DB::transaction(function () use ($examSchedule) {
                // First delete linked supervisors
                $examSchedule->supervisors()->delete();
                
                // Then delete schedule
                $examSchedule->delete();
            });
            
            return redirect()
                ->route('exam-schedules.index')
                ->with('success', 'Exam schedule deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting exam schedule: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while deleting the exam schedule. Please try again.');
        }
    }
    
    /**
     * Display the schedules for a specific exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function examSchedules(Exam $exam)
    {
        try {
            $schedules = $exam->schedules()
                ->with(['section', 'section.class', 'supervisors.user'])
                ->orderBy('exam_date', 'asc')
                ->paginate(15);
                
            $sections = Section::where('class_id', $exam->class_id)->get();
            
            return view('exam_schedules.exam_schedules', compact('exam', 'schedules', 'sections'));
        } catch (\Exception $e) {
            Log::error('Error retrieving exam schedules: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while retrieving exam schedules. Please try again.');
        }
    }
    
    /**
     * Update the status of an exam schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamSchedule  $examSchedule
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, ExamSchedule $examSchedule)
    {
        try {
            $request->validate([
                'status' => 'required|in:scheduled,in-progress,completed,cancelled',
            ]);
            
            // Use transaction to ensure data integrity
            \DB::transaction(function () use ($request, $examSchedule) {
                $examSchedule->status = $request->status;
                $examSchedule->updated_by = Auth::id();
                $examSchedule->save();
            });
            
            return redirect()
                ->back()
                ->with('success', 'Exam schedule status updated successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating exam schedule status: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'An error occurred while updating the schedule status. Please try again.');
        }
    }
} 