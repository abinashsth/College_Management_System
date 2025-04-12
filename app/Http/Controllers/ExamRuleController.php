<?php

namespace App\Http\Controllers;

use App\Models\ExamRule;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamRuleController extends Controller
{
    /**
     * Display a listing of exam rules.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = ExamRule::with(['exam', 'creator']);
        
        // Apply filters if provided
        if ($request->filled('exam_id')) {
            $query->where('exam_id', $request->exam_id);
        }
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->filled('is_global')) {
            $query->where('is_global', $request->is_global === 'yes');
        }
        
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'yes');
        }
        
        $rules = $query->orderBy('is_global', 'desc')
            ->orderBy('display_order', 'asc')
            ->paginate(15);
        
        // Get data for filters
        $exams = Exam::where('is_active', true)->get();
        $categories = ExamRule::getCategories();
        
        return view('exam_rules.index', compact('rules', 'exams', 'categories'));
    }

    /**
     * Show the form for creating a new exam rule.
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
        $categories = ExamRule::getCategories();
        
        return view('exam_rules.create', compact('exams', 'categories', 'exam'));
    }

    /**
     * Store a newly created exam rule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_id' => 'required_unless:is_global,1|nullable|exists:exams,id',
            'is_global' => 'boolean',
            'description' => 'required|string',
            'is_mandatory' => 'boolean',
            'display_order' => 'nullable|integer|min:1',
            'category' => 'required|string',
            'penalty_for_violation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Global rules don't need an exam_id
        if ($validated['is_global'] ?? false) {
            $validated['exam_id'] = null;
        }
        
        // Get the max display order if not provided
        if (!isset($validated['display_order'])) {
            $maxOrder = ExamRule::where('exam_id', $validated['exam_id'])
                ->max('display_order') ?? 0;
            $validated['display_order'] = $maxOrder + 1;
        }
        
        // Add creator
        $validated['created_by'] = Auth::id();
        
        $rule = ExamRule::create($validated);
        
        return redirect()
            ->route('exam-rules.show', $rule)
            ->with('success', 'Exam rule created successfully');
    }

    /**
     * Display the specified exam rule.
     *
     * @param  \App\Models\ExamRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function show(ExamRule $rule)
    {
        $rule->load(['exam', 'creator']);
        
        return view('exam_rules.show', compact('rule'));
    }

    /**
     * Show the form for editing the specified exam rule.
     *
     * @param  \App\Models\ExamRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function edit(ExamRule $rule)
    {
        $exams = Exam::where('is_active', true)->get();
        $categories = ExamRule::getCategories();
        
        return view('exam_rules.edit', compact('rule', 'exams', 'categories'));
    }

    /**
     * Update the specified exam rule in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ExamRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ExamRule $rule)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'exam_id' => 'required_unless:is_global,1|nullable|exists:exams,id',
            'is_global' => 'boolean',
            'description' => 'required|string',
            'is_mandatory' => 'boolean',
            'display_order' => 'nullable|integer|min:1',
            'category' => 'required|string',
            'penalty_for_violation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // Global rules don't need an exam_id
        if ($validated['is_global'] ?? false) {
            $validated['exam_id'] = null;
        }
        
        $rule->update($validated);
        
        return redirect()
            ->route('exam-rules.show', $rule)
            ->with('success', 'Exam rule updated successfully');
    }

    /**
     * Remove the specified exam rule from storage.
     *
     * @param  \App\Models\ExamRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExamRule $rule)
    {
        $rule->delete();
        
        return redirect()
            ->route('exam-rules.index')
            ->with('success', 'Exam rule deleted successfully');
    }
    
    /**
     * Display rules for a specific exam.
     *
     * @param  \App\Models\Exam  $exam
     * @return \Illuminate\Http\Response
     */
    public function examRules(Exam $exam)
    {
        // Get both exam-specific rules and global rules
        $rules = ExamRule::where(function($query) use ($exam) {
            $query->where('exam_id', $exam->id)
                ->orWhere('is_global', true);
        })
        ->with('creator')
        ->orderBy('is_global', 'desc')
        ->orderBy('display_order', 'asc')
        ->paginate(15);
        
        $categories = ExamRule::getCategories();
        
        return view('exam_rules.exam_rules', compact('exam', 'rules', 'categories'));
    }
    
    /**
     * Toggle the active status of a rule.
     *
     * @param  \App\Models\ExamRule  $rule
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus(ExamRule $rule)
    {
        $rule->is_active = !$rule->is_active;
        $rule->save();
        
        $status = $rule->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->back()
            ->with('success', "Rule {$status} successfully");
    }
    
    /**
     * Update the display order of rules.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request)
    {
        $validated = $request->validate([
            'rules' => 'required|array',
            'rules.*.id' => 'required|exists:exam_rules,id',
            'rules.*.order' => 'required|integer|min:1',
        ]);
        
        foreach ($validated['rules'] as $ruleData) {
            $rule = ExamRule::find($ruleData['id']);
            $rule->display_order = $ruleData['order'];
            $rule->save();
        }
        
        return response()->json(['success' => true]);
    }
} 