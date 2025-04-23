<?php

namespace App\Http\Controllers;

use App\Models\SubjectMask;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectMaskController extends Controller
{
    /**
     * Constructor to apply middleware
     */
    public function __construct()
    {
        $this->middleware(['auth', 'permission:view marks']);
        $this->middleware('permission:create marks', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit marks', ['only' => ['edit', 'update']]);
        $this->middleware('permission:verify marks', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of subject masks.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Add debugging
        Log::info('Subject Mask Index accessed by: ' . Auth::user()->name);
        
        $query = SubjectMask::with(['subject', 'exam', 'creator']);
        
        // Filter by subject if provided
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Filter by exam if provided
        if ($request->has('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        $masks = $query->paginate(15);
        
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::orderBy('title')->get();
        
        return view('masks.index', compact('masks', 'subjects', 'exams'));
    }

    /**
     * Show the form for creating a new subject mask.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::orderBy('title')->get();
        
        return view('masks.create', compact('subjects', 'exams'));
    }

    /**
     * Store a newly created subject mask in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'required|exists:exams,id',
            'mask_value' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Check for existing mask for the same subject and exam
        $existing = SubjectMask::where('subject_id', $validated['subject_id'])
            ->where('exam_id', $validated['exam_id'])
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A mask already exists for this subject and exam combination.');
        }
        
        // Add creator information
        $validated['created_by'] = Auth::id();
        $validated['updated_by'] = Auth::id();
        
        SubjectMask::create($validated);
        
        return redirect()->route('masks.index')
            ->with('success', 'Subject mask created successfully.');
    }

    /**
     * Display the specified subject mask.
     *
     * @param  \App\Models\SubjectMask  $mask
     * @return \Illuminate\Http\Response
     */
    public function show(SubjectMask $mask)
    {
        $mask->load(['subject', 'exam', 'creator', 'updater']);
        
        return view('masks.show', compact('mask'));
    }

    /**
     * Show the form for editing the specified subject mask.
     *
     * @param  \App\Models\SubjectMask  $mask
     * @return \Illuminate\Http\Response
     */
    public function edit(SubjectMask $mask)
    {
        $subjects = Subject::orderBy('name')->get();
        $exams = Exam::orderBy('title')->get();
        
        return view('masks.edit', compact('mask', 'subjects', 'exams'));
    }

    /**
     * Update the specified subject mask in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SubjectMask  $mask
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SubjectMask $mask)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'required|exists:exams,id',
            'mask_value' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Check for existing mask for the same subject and exam (excluding current)
        $existing = SubjectMask::where('subject_id', $validated['subject_id'])
            ->where('exam_id', $validated['exam_id'])
            ->where('id', '!=', $mask->id)
            ->first();
            
        if ($existing) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'A mask already exists for this subject and exam combination.');
        }
        
        // Add updater information
        $validated['updated_by'] = Auth::id();
        
        $mask->update($validated);
        
        return redirect()->route('masks.index')
            ->with('success', 'Subject mask updated successfully.');
    }

    /**
     * Remove the specified subject mask from storage.
     *
     * @param  \App\Models\SubjectMask  $mask
     * @return \Illuminate\Http\Response
     */
    public function destroy(SubjectMask $mask)
    {
        $mask->delete();
        
        return redirect()->route('masks.index')
            ->with('success', 'Subject mask deleted successfully.');
    }

    /**
     * Get mask for a specific exam-subject combination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getMask(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'exam_id' => 'required|exists:exams,id',
        ]);
        
        $mask = SubjectMask::where('subject_id', $request->subject_id)
            ->where('exam_id', $request->exam_id)
            ->where('is_active', true)
            ->first();
            
        if (!$mask) {
            return response()->json(['mask_exists' => false]);
        }
        
        return response()->json([
            'mask_exists' => true,
            'mask_value' => $mask->mask_value,
            'description' => $mask->description,
        ]);
    }
} 