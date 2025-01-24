<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Classes;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view subjects', ['only' => ['index', 'show']]);
        $this->middleware('permission:create subjects', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit subjects', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete subjects', ['only' => ['destroy']]);
    }

    public function index()
    {
        $subjects = Subject::with('classes')
            ->orderBy('name')
            ->paginate(10);
            
        return view('exam-management.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $classes = Classes::where('is_active', true)->get();
        $academicSessions = AcademicSession::where('is_active', true)->get();
        return view('exam-management.subjects.create', compact('classes', 'academicSessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects',
            'description' => 'nullable|string',
            'total_theory_marks' => 'required|numeric|min:0',
            'total_practical_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'classes' => 'nullable|array',
            'classes.*' => 'exists:classes,id'
        ]);

        DB::beginTransaction();
        try {
            $subject = Subject::create([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'total_theory_marks' => $validated['total_theory_marks'],
                'total_practical_marks' => $validated['total_practical_marks'],
                'passing_marks' => $validated['passing_marks'],
                'status' => true
            ]);

            if (!empty($validated['classes'])) {
                foreach ($validated['classes'] as $classId) {
                    $subject->classes()->attach($classId, [
                        'academic_session_id' => $validated['academic_session_id']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create subject. ' . $e->getMessage());
        }
    }

    public function show(Subject $subject)
    {
        $subject->load('classes', 'exams', 'examinerAssignments.user');
        return view('exam-management.subjects.show', compact('subject'));
    }

    public function edit(Subject $subject)
    {
        $classes = Classes::where('is_active', true)->get();
        $academicSessions = AcademicSession::where('is_active', true)->get();
        $assignedClasses = $subject->classes->pluck('id')->toArray();
        return view('exam-management.subjects.edit', compact('subject', 'classes', 'academicSessions', 'assignedClasses'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'description' => 'nullable|string',
            'total_theory_marks' => 'required|numeric|min:0',
            'total_practical_marks' => 'required|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
            'academic_session_id' => 'required|exists:academic_sessions,id,is_active,true',
            'classes' => 'array'
        ]);

        DB::beginTransaction();
        try {
            $subject->update([
                'name' => $validated['name'],
                'code' => $validated['code'],
                'description' => $validated['description'] ?? null,
                'total_theory_marks' => $validated['total_theory_marks'],
                'total_practical_marks' => $validated['total_practical_marks'],
                'passing_marks' => $validated['passing_marks'],
                'status' => true
            ]);

            if (isset($validated['classes'])) {
                foreach ($validated['classes'] as $classId) {
                    $subject->classes()->syncWithoutDetaching($classId, [
                        'academic_session_id' => $validated['academic_session_id']
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update subject. ' . $e->getMessage());
        }
    }

    public function destroy(Subject $subject)
    {
        if ($subject->exams()->exists() || $subject->examinerAssignments()->exists()) {
            return back()->with('error', 'Cannot delete subject. It has associated exams or examiner assignments.');
        }

        DB::beginTransaction();
        try {
            $subject->classes()->detach();
            $subject->delete();
            DB::commit();
            return redirect()->route('subjects.index')->with('success', 'Subject deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete subject. ' . $e->getMessage());
        }
    }

    public function assignClass(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id'
        ]);

        if (!$subject->classes()->where('class_id', $validated['class_id'])->exists()) {
            $subject->classes()->attach($validated['class_id']);
            return back()->with('success', 'Class assigned successfully.');
        }

        return back()->with('info', 'Class is already assigned to this subject.');
    }

    public function removeClass(Subject $subject, Classes $class)
    {
        if ($subject->exams()->where('class_id', $class->id)->exists()) {
            return back()->with('error', 'Cannot remove class. There are exams associated with this subject-class combination.');
        }

        $subject->classes()->detach($class->id);
        return back()->with('success', 'Class removed successfully.');
    }
}
