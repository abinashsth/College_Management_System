<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\Employee;


use Illuminate\Http\Request;

class EmployeeSalaryController extends Controller
{
    /**
     * Display a listing of employee salaries.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
{
    $employeeSalaries = EmployeeSalary::with('employee')->paginate(10); // Use pagination
    $employees = \App\Models\Employee::all();
    $totalSalary = $employeeSalaries->sum('amount');
    $averageSalary = $employeeSalaries->avg('amount');

    return view('account.salary_management.employee_salary.index', compact('employeeSalaries', 'employees', 'totalSalary', 'averageSalary'));
}


 
    public function create()
    {
        $employees = Employee::all();
        return view('account.salary_management.employee_salary.create', compact('employees'));
    }

  
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        EmployeeSalary::create($validated);

        return redirect()->route('employee_salary.index')
            ->with('success', 'Employee salary created successfully.');
    }

  
    public function show(EmployeeSalary $employeeSalary)
    {
        return view('account.salary_management.employee_salary.show', compact('employeeSalary'));
    }

  
    public function edit(EmployeeSalary $employeeSalary)
    {
        $employees = Employee::all();
        return view('account.salary_management.employee_salary.edit', compact('employeeSalary', 'employees'));
    }

  
    public function update(Request $request, EmployeeSalary $employeeSalary)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $employeeSalary->update($validated);

        return redirect()->route('employee_salary.index')
            ->with('success', 'Employee salary updated successfully.');
    }

   
    public function destroy(EmployeeSalary $employeeSalary)
    {
        $employeeSalary->delete();

        return redirect()->route('employee_salary.index')
            ->with('success', 'Employee salary deleted successfully.');
    }
}
