<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exam;
use App\Models\ExamRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExamRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ExamRule::with(['exam', 'creator']);
        
        // Filter by global
        if ($request->has('is_global')) {
            $query->global();
        }
        
        // Filter by exam
        if ($request->has('exam_id') && $request->exam_id) {
            $query->where('exam_id', $request->exam_id);
        }
        
        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->category($request->category);
        }
        
        // Filter by active status
        if ($request->has('is_active')) {
            $query->active();
        }
        
        // Filter by mandatory
        if ($request->has('is_mandatory')) {
            $query->mandatory();
        }
        
        $rules = $query->ordered()->paginate(15);
        $exams = Exam::where('is_active', true)->get();
        $categories = ExamRule::getCategories();
        
        return view('exam.rules.index', compact('rules', 'exams', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $exams = Exam::where('is_active', true)->get();
        $categories = ExamRule::getCategories();
        
        return view('exam.rules.create', compact('exams', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'exam_id' => 'nullable|exists:exams,id',
            'is_global' => 'boolean',
            'description' => 'required|string',
            'is_mandatory' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'category' => 'required|in:general,conduct,materials,timing,grading,other',
            'penalty_for_violation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // If is_global is true, set exam_id to null
        if ($request->has('is_global') && $request->is_global) {
            $validatedData['exam_id'] = null;
        }
        
        DB::beginTransaction();
        
        try {
            // Set default values
            $validatedData['created_by'] = Auth::id();
            $validatedData['is_global'] = $request->has('is_global');
            $validatedData['is_mandatory'] = $request->has('is_mandatory');
            $validatedData['is_active'] = $request->has('is_active');
            
            $rule = ExamRule::create($validatedData);
            
            DB::commit();
            
            return redirect()->route('exam.rules.show', $rule)
                ->with('success', 'Exam rule created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create exam rule. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ExamRule $rule)
    {
        $rule->load(['exam', 'creator']);
        
        // Get related rules (same category, same exam or global)
        $relatedRules = ExamRule::where('id', '!=', $rule->id)
            ->where(function ($query) use ($rule) {
                $query->where('category', $rule->category)
                    ->orWhere(function ($q) use ($rule) {
                        if ($rule->exam_id) {
                            $q->where('exam_id', $rule->exam_id);
                        } else {
                            $q->whereNull('exam_id');
                        }
                    });
            })
            ->active()
            ->ordered()
            ->limit(5)
            ->get();
        
        return view('exam.rules.show', compact('rule', 'relatedRules'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ExamRule $rule)
    {
        $exams = Exam::where('is_active', true)->get();
        $categories = ExamRule::getCategories();
        
        return view('exam.rules.edit', compact('rule', 'exams', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExamRule $rule)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'exam_id' => 'nullable|exists:exams,id',
            'is_global' => 'boolean',
            'description' => 'required|string',
            'is_mandatory' => 'boolean',
            'display_order' => 'nullable|integer|min:0',
            'category' => 'required|in:general,conduct,materials,timing,grading,other',
            'penalty_for_violation' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        
        // If is_global is true, set exam_id to null
        if ($request->has('is_global') && $request->is_global) {
            $validatedData['exam_id'] = null;
        }
        
        DB::beginTransaction();
        
        try {
            // Set boolean values
            $validatedData['is_global'] = $request->has('is_global');
            $validatedData['is_mandatory'] = $request->has('is_mandatory');
            $validatedData['is_active'] = $request->has('is_active');
            
            $rule->update($validatedData);
            
            DB::commit();
            
            return redirect()->route('exam.rules.show', $rule)
                ->with('success', 'Exam rule updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update exam rule. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExamRule $rule)
    {
        try {
            $rule->delete();
            
            return redirect()->route('exam.rules.index')
                ->with('success', 'Exam rule deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete exam rule. ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle the active status of a rule.
     */
    public function toggleActive(ExamRule $rule)
    {
        try {
            $rule->update([
                'is_active' => !$rule->is_active,
            ]);
            
            $status = $rule->is_active ? 'activated' : 'deactivated';
            
            return redirect()->route('exam.rules.show', $rule)
                ->with('success', "Rule {$status} successfully.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to change rule status. ' . $e->getMessage());
        }
    }
    
    /**
     * List rules for a specific exam.
     */
    public function examRules(Exam $exam)
    {
        // Get all applicable rules for this exam (exam-specific + global)
        $rules = ExamRule::getApplicableRules($exam->id);
        
        return view('exam.rules.exam_rules', compact('exam', 'rules'));
    }
    
    /**
     * Bulk update rule display order.
     */
    public function updateOrder(Request $request)
    {
        $validatedData = $request->validate([
            'rules' => 'required|array',
            'rules.*.id' => 'required|exists:exam_rules,id',
            'rules.*.display_order' => 'required|integer|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($validatedData['rules'] as $ruleData) {
                ExamRule::where('id', $ruleData['id'])->update([
                    'display_order' => $ruleData['display_order'],
                ]);
            }
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Rule order updated successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json(['success' => false, 'message' => 'Failed to update rule order: ' . $e->getMessage()], 500);
        }
    }
}
