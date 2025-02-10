<?php

namespace App\Http\Controllers;

use App\Models\SalaryComponent;
use Illuminate\Http\Request;

class SalaryComponentController extends Controller
{
    public function index()
    {
        $salaryComponents = SalaryComponent::paginate(10);
        return view('account.salary_management.salary_component.index', compact('salaryComponents'));
    }




    public function create()
    {
        return view('account.salary_management.salary_component.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Allowance,Deduction',
            'status' => 'required|boolean',
            'description' => 'nullable|string'
        ]);

        SalaryComponent::create($validated);

        return redirect()->route('account.salary_management.salary_component.index')
            ->with('success', 'Salary component created successfully.');
    }

        public function edit(SalaryComponent $salaryComponent)
    {
        return view('account.salary_management.salary_component.edit', compact('salaryComponent'));
        


    }

    public function update(Request $request, SalaryComponent $salaryComponent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:Allowance,Deduction',
            'status' => 'required|boolean',
            'description' => 'nullable|string'
        ]);

        $salaryComponent->update($validated);

        return redirect()->route('account.salary_management.salary_component.index')
            ->with('success', 'Salary component updated successfully.');
    }

    public function destroy(SalaryComponent $salaryComponent)
    {
        $salaryComponent->delete();

        return redirect()->route('account.salary_management.salary_component.index')
            ->with('success', 'Salary component deleted successfully.');
    }
}
