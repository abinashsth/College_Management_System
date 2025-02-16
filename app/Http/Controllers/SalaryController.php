<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\SalaryComponent;
use App\Models\SalaryIncrement;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    public function index()
    {
        $employees = Employee::with(['salaries', 'salaryIncrements'])->get();
        return view('account.salary_management.salary.index', compact('employees'));
    }

    public function create()
    {
        $employees = Employee::all();
        $salaryComponents = SalaryComponent::active()->get();
        return view('account.salary_management.salary.create', compact('employees', 'salaryComponents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'components' => 'required|array',
            'components.*.component_id' => 'required|exists:salary_components,id',
            'components.*.amount' => 'required|numeric|min:0'
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        foreach ($request->components as $component) {
            $employee->salaries()->create([
                'salary_component_id' => $component['component_id'],
                'amount' => $component['amount']
            ]);
        }

        return redirect()->route('account.salary_management.salary.index')
            ->with('success', 'Salary details added successfully.');
    }

    public function show($id)
    {
        $employee = Employee::with(['salaries.component', 'salaryIncrements'])->findOrFail($id);
        return view('account.salary_management.salary.show', compact('employee'));
    }

    public function edit($id)
    {
        $employee = Employee::with('salaries.component')->findOrFail($id);
        $salaryComponents = SalaryComponent::active()->get();
        return view('account.salary_management.salary.edit', compact('employee', 'salaryComponents'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'components' => 'required|array',
            'components.*.component_id' => 'required|exists:salary_components,id',
            'components.*.amount' => 'required|numeric|min:0'
        ]);

        $employee = Employee::findOrFail($id);
        
        // Delete existing salary components
        $employee->salaries()->delete();
        
        // Add new salary components
        foreach ($request->components as $component) {
            $employee->salaries()->create([
                'salary_component_id' => $component['component_id'],
                'amount' => $component['amount']
            ]);
        }

        return redirect()->route('account.salary_management.salary.index')
            ->with('success', 'Salary details updated successfully.');
    }

    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->salaries()->delete();
        
        return redirect()->route('account.salary_management.salary.index')
            ->with('success', 'Salary details deleted successfully.');
    }

    public function generate()
    {
        $employees = Employee::all();
        $salaryComponents = SalaryComponent::active()->get();
        return view('account.salary_management.generate_salary.index', compact('employees', 'salaryComponents'));
    }
}
