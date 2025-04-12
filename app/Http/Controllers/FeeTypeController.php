<?php

namespace App\Http\Controllers;

use App\Models\FeeType;
use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of the fee types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $feeTypes = FeeType::with('feeCategory')->orderBy('name')->get();
        return view('finance.fee-types.index', compact('feeTypes'));
    }

    /**
     * Show the form for creating a new fee type.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = FeeCategory::where('is_active', true)->orderBy('name')->get();
        $frequencies = [
            'one_time' => 'One Time',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi Annually',
            'annually' => 'Annually'
        ];
        
        return view('finance.fee-types.create', compact('categories', 'frequencies'));
    }

    /**
     * Store a newly created fee type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fee_types',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:one_time,monthly,quarterly,semi_annually,annually',
            'is_optional' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_optional'] = $request->has('is_optional');
        $validated['is_active'] = $request->has('is_active');
        
        $feeType = FeeType::create($validated);

        return redirect()->route('fee-types.index')
            ->with('success', 'Fee type created successfully.');
    }

    /**
     * Display the specified fee type.
     *
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function show(FeeType $feeType)
    {
        $feeType->load('feeCategory', 'feeAllocations.academicYear');
        return view('finance.fee-types.show', compact('feeType'));
    }

    /**
     * Show the form for editing the specified fee type.
     *
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function edit(FeeType $feeType)
    {
        $categories = FeeCategory::where('is_active', true)->orderBy('name')->get();
        $frequencies = [
            'one_time' => 'One Time',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'semi_annually' => 'Semi Annually',
            'annually' => 'Annually'
        ];
        
        return view('finance.fee-types.edit', compact('feeType', 'categories', 'frequencies'));
    }

    /**
     * Update the specified fee type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FeeType $feeType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fee_types,code,' . $feeType->id,
            'fee_category_id' => 'required|exists:fee_categories,id',
            'amount' => 'required|numeric|min:0',
            'frequency' => 'required|in:one_time,monthly,quarterly,semi_annually,annually',
            'is_optional' => 'boolean',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $validated['is_optional'] = $request->has('is_optional');
        $validated['is_active'] = $request->has('is_active');
        
        $feeType->update($validated);

        return redirect()->route('fee-types.index')
            ->with('success', 'Fee type updated successfully.');
    }

    /**
     * Remove the specified fee type from storage.
     *
     * @param  \App\Models\FeeType  $feeType
     * @return \Illuminate\Http\Response
     */
    public function destroy(FeeType $feeType)
    {
        // Check if the fee type has related allocations or invoice items
        if ($feeType->feeAllocations()->count() > 0 || $feeType->invoiceItems()->count() > 0) {
            return redirect()->route('fee-types.index')
                ->with('error', 'Cannot delete a fee type that has associated allocations or invoice items.');
        }

        $feeType->delete();

        return redirect()->route('fee-types.index')
            ->with('success', 'Fee type deleted successfully.');
    }
} 