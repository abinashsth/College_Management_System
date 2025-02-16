<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    public function index()
    {
        $feeCategories = FeeCategory::paginate(10);

        return view('account.fee_management.fee_category.index', compact('feeCategories'));
    }   

    public function create()
    {
        return view('account.fee_management.fee_category.create');
    }   

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]); 

        FeeCategory::create($validated);

        return redirect()->route('account.fee_management.fee_category.index')->with('success', 'Fee category created successfully');
    }      

    public function edit($id)
    {
        $feeCategory = FeeCategory::findOrFail($id);
        return view('account.fee_management.fee_category.edit', compact('feeCategory'));
    }      

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);  

        $feeCategory = FeeCategory::findOrFail($id);
        $feeCategory->update($validated);

        return redirect()->route('account.fee_management.fee_category.index')->with('success', 'Fee category updated successfully');
    }             

    public function destroy($id)
    {
        $feeCategory = FeeCategory::findOrFail($id);
        $feeCategory->delete();

        return redirect()->route('account.fee_management.fee_category.index')->with('success', 'Fee category deleted successfully');
    }   
    
    
    
}
