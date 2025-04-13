<?php

namespace App\Http\Controllers;

use App\Models\ExamMaterial;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExamMaterialController extends Controller
{
    /**
     * Display a listing of exam materials.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ExamMaterial::with(['exam', 'creator', 'approver']);
        
        // Apply filters if provided
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->filled('for')) {
            if ($request->for === 'students') {
                $query->where('is_for_students', true);
            } elseif ($request->for === 'teachers') {
                $query->where('is_for_teachers', true);
            }
        }
        
        if ($request->filled('is_confidential')) {
            $query->where('is_confidential', $request->is_confidential === 'yes');
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'yes');
        }
        
        $materials = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get data for filters
        $exams = Exam::where('is_active', true)->get();
        $types = ExamMaterial::getTypes();
        
        return view('exam_materials.index', compact('materials', 'exams', 'types'));
    }

    /**
     * Show the form for creating a new exam material.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $examId = $request->exam_id;
        $exam = null;
        
        if ($examId) {
            $exam = Exam::findOrFail($examId);
        }
        
        $exams = Exam::where('is_active', true)->get();
        $types = ExamMaterial::getTypes();
        
        return view('exam_materials.create', compact('exams', 'types', 'exam'));
    }

    /**
     * Store a newly created exam material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'is_for_students' => 'boolean',
            'is_for_teachers' => 'boolean',
            'is_confidential' => 'boolean',
            'release_date' => 'nullable|date',
            'is_active' => 'boolean',
            'file' => 'required|file|max:10240', // Max file size 10MB
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('exam_materials', $filename, 'public');
            
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
        }
        
        // Set version to 1 for new material
        $validated['version'] = 1;
        
        // Add creator
        $validated['created_by'] = Auth::id();
        
        $material = ExamMaterial::create($validated);
        
        return redirect()
            ->route('exam-materials.show', $material)
            ->with('success', 'Exam material created successfully');
    }

    /**
     * Display the specified exam material.
     *
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function show(ExamMaterial $material)
    {
        $material->load(['exam', 'creator', 'approver']);
        
        // Check if the current user has permission to view this material
        if (!$material->canBeViewedBy(Auth::user())) {
            return redirect()
                ->route('exam-materials.index')
                ->with('error', 'You do not have permission to view this material.');
        }
        
        return view('exam_materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified exam material.
     *
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamMaterial $material)
    {
        $exams = Exam::where('is_active', true)->get();
        $types = ExamMaterial::getTypes();
        
        return view('exam_materials.edit', compact('material', 'exams', 'types'));
    }

    /**
     * Update the specified exam material in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamMaterial $material)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
            'is_for_students' => 'boolean',
            'is_for_teachers' => 'boolean',
            'is_confidential' => 'boolean',
            'release_date' => 'nullable|date',
            'is_active' => 'boolean',
            'file' => 'nullable|file|max:10240', // Max file size 10MB
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('exam_materials', $filename, 'public');
            
            $validated['file_path'] = $path;
            $validated['file_type'] = $file->getClientMimeType();
            $validated['file_size'] = $file->getSize();
            
            // Increment version
            $validated['version'] = $material->version + 1;
        }
        
        $material->update($validated);
        
        return redirect()
            ->route('exam-materials.show', $material)
            ->with('success', 'Exam material updated successfully');
    }

    /**
     * Remove the specified exam material from storage.
     *
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamMaterial $material)
    {
        // Delete the file
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        // Delete the record
        $material->delete();
        
        return redirect()
            ->route('exam-materials.index')
            ->with('success', 'Exam material deleted successfully');
    }
    
    /**
     * Download the exam material file.
     *
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function download(ExamMaterial $material)
    {
        // Check if the current user has permission to download this material
        if (!$material->canBeViewedBy(Auth::user())) {
            return redirect()
                ->route('exam-materials.index')
                ->with('error', 'You do not have permission to download this material.');
        }
        
        // Check if file exists
        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            return redirect()
                ->back()
                ->with('error', 'File not found or has been deleted.');
        }
        
        $filename = pathinfo($material->file_path, PATHINFO_BASENAME);
        
        return Storage::disk('public')->download($material->file_path, $filename);
    }
    
    /**
     * Display materials for a specific exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function examMaterials(Exam $exam)
    {
        // Get materials for this exam with proper access control
        $query = ExamMaterial::where('exam_id', $exam->id);
        
        // Apply access control based on user role
        $user = Auth::user();
        
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            if ($user->hasRole('Teacher')) {
                $query->where('is_for_teachers', true);
            } elseif ($user->hasRole('Student')) {
                $query->where('is_for_students', true)
                    ->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('release_date')
                            ->orWhere('release_date', '<=', now());
                    })
                    ->where('is_confidential', false);
            }
        }
        
        $materials = $query->with(['creator', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $types = ExamMaterial::getTypes();
        
        return view('exam_materials.exam_materials', compact('exam', 'materials', 'types'));
    }
    
    /**
     * Approve an exam material.
     *
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function approve(ExamMaterial $material)
    {
        // Only admins can approve materials
        if (!Auth::user()->hasRole(['Super Admin', 'Admin'])) {
            return redirect()
                ->back()
                ->with('error', 'You do not have permission to approve materials.');
        }
        
        $material->approve(Auth::id());
        
        return redirect()
            ->back()
            ->with('success', 'Material approved successfully');
    }
    
    /**
     * Toggle the active status of a material.
     *
     * @param  \App\Models\ExamMaterial  $material
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(ExamMaterial $material)
    {
        $material->is_active = !$material->is_active;
        $material->save();
        
        $status = $material->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Material {$status} successfully");
    }
} 