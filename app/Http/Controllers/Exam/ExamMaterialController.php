<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamMaterial;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExamMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExamMaterial::with(['exam', 'creator']);
        
        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }
        
        // Filter by for_students
        if ($request->has('for_students')) {
            $query->forStudents();
        }
        
        // Filter by for_teachers
        if ($request->has('for_teachers')) {
            $query->forTeachers();
        }
        
        // Filter by status
        if ($request->has('is_active')) {
            $query->active();
        }
        
        $materials = $query->orderBy('created_at', 'desc')->paginate(10);
        $exams = Exam::where('is_active', true)->get();
        $materialTypes = ExamMaterial::getTypes();
        
        return view('exam.materials.index', compact('materials', 'exams', 'materialTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exams = Exam::where('is_active', true)->get();
        $materialTypes = ExamMaterial::getTypes();
        
        return view('exam.materials.create', compact('exams', 'materialTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:question_paper,answer_sheet,supplementary,instruction,resource,marking_scheme,other',
            'description' => 'nullable|string',
            'is_for_students' => 'boolean',
            'is_for_teachers' => 'boolean',
            'is_confidential' => 'boolean',
            'release_date' => 'nullable|date',
            'material_file' => 'required|file|max:10240', // Max 10MB
        ]);
        
        DB::beginTransaction();
        
        try {
            $file = $request->file('material_file');
            $extension = $file->getClientOriginalExtension();
            $fileSize = $file->getSize() / 1024; // Convert to KB
            $fileName = Str::slug($request->title) . '-' . time() . '.' . $extension;
            
            // Create storage path based on exam ID
            $path = 'exam_materials/' . $request->exam_id;
            $filePath = $file->storeAs($path, $fileName, 'public');
            
            $material = ExamMaterial::create([
                'exam_id' => $validatedData['exam_id'],
                'title' => $validatedData['title'],
                'type' => $validatedData['type'],
                'file_path' => $filePath,
                'file_type' => $extension,
                'file_size' => $fileSize,
                'description' => $validatedData['description'] ?? null,
                'is_for_students' => $request->has('is_for_students'),
                'is_for_teachers' => $request->has('is_for_teachers'),
                'is_confidential' => $request->has('is_confidential'),
                'release_date' => $validatedData['release_date'] ?? null,
                'is_active' => true,
                'version' => 1,
                'created_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('exam.materials.show', $material)
                ->with('success', 'Exam material uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to upload exam material. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamMaterial $material)
    {
        $material->load(['exam', 'creator', 'approver']);
        
        return view('exam.materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamMaterial $material)
    {
        $exams = Exam::where('is_active', true)->get();
        $materialTypes = ExamMaterial::getTypes();
        
        return view('exam.materials.edit', compact('material', 'exams', 'materialTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamMaterial $material)
    {
        $validatedData = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:question_paper,answer_sheet,supplementary,instruction,resource,marking_scheme,other',
            'description' => 'nullable|string',
            'is_for_students' => 'boolean',
            'is_for_teachers' => 'boolean',
            'is_confidential' => 'boolean',
            'release_date' => 'nullable|date',
            'material_file' => 'nullable|file|max:10240', // Max 10MB
        ]);
        
        DB::beginTransaction();
        
        try {
            $updateData = [
                'exam_id' => $validatedData['exam_id'],
                'title' => $validatedData['title'],
                'type' => $validatedData['type'],
                'description' => $validatedData['description'] ?? null,
                'is_for_students' => $request->has('is_for_students'),
                'is_for_teachers' => $request->has('is_for_teachers'),
                'is_confidential' => $request->has('is_confidential'),
                'release_date' => $validatedData['release_date'] ?? null,
                'version' => $material->version + 1, // Increment version
            ];
            
            // If a new file is uploaded
            if ($request->hasFile('material_file')) {
                // Delete old file if it exists
                if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                    Storage::disk('public')->delete($material->file_path);
                }
                
                $file = $request->file('material_file');
                $extension = $file->getClientOriginalExtension();
                $fileSize = $file->getSize() / 1024; // Convert to KB
                $fileName = Str::slug($request->title) . '-' . time() . '.' . $extension;
                
                // Create storage path based on exam ID
                $path = 'exam_materials/' . $request->exam_id;
                $filePath = $file->storeAs($path, $fileName, 'public');
                
                $updateData['file_path'] = $filePath;
                $updateData['file_type'] = $extension;
                $updateData['file_size'] = $fileSize;
            }
            
            $material->update($updateData);
            
            DB::commit();
            
            return redirect()->route('exam.materials.show', $material)
                ->with('success', 'Exam material updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update exam material. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamMaterial $material)
    {
        try {
            // Delete file if it exists
            if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
                Storage::disk('public')->delete($material->file_path);
            }
            
            $material->delete();
            
            return redirect()->route('exam.materials.index')
                ->with('success', 'Exam material deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete exam material. ' . $e->getMessage());
        }
    }
    
    /**
     * Download the material file.
     */
    public function download(ExamMaterial $material)
    {
        // Check if the user can view this material
        if (!$material->canBeViewedBy(Auth::user())) {
            return redirect()->back()
                ->with('error', 'You do not have permission to download this material.');
        }
        
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            return Storage::disk('public')->download($material->file_path, $material->title . '.' . $material->file_type);
        }
        
        return redirect()->back()
            ->with('error', 'File not found.');
    }
    
    /**
     * Approve the exam material.
     */
    public function approve(Request $request, ExamMaterial $material)
    {
        if ($material->approved_at) {
            return redirect()->back()
                ->with('error', 'This material is already approved.');
        }
        
        try {
            $material->approve(Auth::id());
            
            return redirect()->route('exam.materials.show', $material)
                ->with('success', 'Exam material approved successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to approve exam material. ' . $e->getMessage());
        }
    }
    
    /**
     * Change the active status of the material.
     */
    public function changeStatus(Request $request, ExamMaterial $material)
    {
        try {
            $material->update([
                'is_active' => !$material->is_active,
            ]);
            
            $status = $material->is_active ? 'activated' : 'deactivated';
            
            return redirect()->route('exam.materials.show', $material)
                ->with('success', "Exam material {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to change exam material status. ' . $e->getMessage());
        }
    }
}
