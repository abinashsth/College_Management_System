<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class FeeCategoryController extends Controller
{
    /**
     * Display a listing of the fee categories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = FeeCategory::orderBy('name')->get();
        return view('finance.fee-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new fee category.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('finance.fee-categories.create');
    }

    /**
     * Store a newly created fee category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fee_categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        
        $category = FeeCategory::create($validated);

        return redirect()->route('fee-categories.index')
            ->with('success', 'Fee category created successfully.');
    }

    /**
     * Display the specified fee category.
     *
     * @param  \App\Models\FeeCategory  $feeCategory
     * @return \Illuminate\Http\Response
     */
    public function show(FeeCategory $feeCategory)
    {
        $feeCategory->load('feeTypes');
        return view('finance.fee-categories.show', compact('feeCategory'));
    }

    /**
     * Show the form for editing the specified fee category.
     *
     * @param  \App\Models\FeeCategory  $feeCategory
     * @return \Illuminate\Http\Response
     */
    public function edit(FeeCategory $feeCategory)
    {
        return view('finance.fee-categories.edit', compact('feeCategory'));
    }

    /**
     * Update the specified fee category in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeeCategory  $feeCategory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeeCategory $feeCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fee_categories,code,' . $feeCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        
        $feeCategory->update($validated);

        return redirect()->route('fee-categories.index')
            ->with('success', 'Fee category updated successfully.');
    }

    /**
     * Remove the specified fee category from storage.
     *
     * @param  \App\Models\FeeCategory  $feeCategory
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeeCategory $feeCategory)
    {
        // Check if the category has related fee types
        if ($feeCategory->feeTypes()->count() > 0) {
            return redirect()->route('fee-categories.index')
                ->with('error', 'Cannot delete a fee category that has associated fee types.');
        }

        $feeCategory->delete();

        return redirect()->route('fee-categories.index')
            ->with('success', 'Fee category deleted successfully.');
    }
} 