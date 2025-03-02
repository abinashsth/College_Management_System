<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryIncrement;
use App\Models\Employee;
use App\Models\EmployeeSalary;

class SalaryIncrementController extends Controller
{
    public function index()
    {
        $salaryIncrements =SalaryIncrement::whereNull('deleted_at')->paginate(10);
        return view('account.salary_management.salary_increment.index', compact('salaryIncrements'));
    }   

    public function create()
    {
        $employees = Employee::all();
        return view('account.salary_management.salary_increment.create', compact('employees'));
    }       

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'increment_amount' => 'required|numeric|min:0', 
            'effective_date' => 'required|date'
        ]);

        $employee = Employee::find($request->employee_id);
        
        // Get current salary from employee_salaries table
        $currentSalary = EmployeeSalary::where('employee_id', $employee->id)
            ->orderBy('salary_month', 'desc')
            ->first();

        if (!$currentSalary) {
            return redirect()->back()->with('error', 'No salary record found for this employee');
        }

        // Calculate new salary
        $newSalary = $currentSalary->basic_salary + $request->increment_amount;

        // Create salary increment record
        $increment = SalaryIncrement::create([
            'employee_id' => $request->employee_id,
            'current_salary' => $currentSalary->basic_salary,
            'increment_amount' => $request->increment_amount,
            'new_salary' => $newSalary,
            'effective_date' => $request->effective_date,
            'remarks' => $request->remarks ?? null
        ]);

        // Create new salary record
        EmployeeSalary::create([
            'employee_id' => $employee->id,
            'salary_month' => $request->effective_date,
            'basic_salary' => $newSalary,
            'allowances' => $currentSalary->allowances,
            'deductions' => $currentSalary->deductions,
            'payment_date' => now(),
            'status' => 'Pending'
        ]);

        return redirect()->route('salary-increments.index')->with('success', 'Salary increment added successfully.');
    }

    public function edit(SalaryIncrement $salaryIncrement)
    {   
        $employees = Employee::all();
        return view('account.salary_management.salary_increment.edit', compact('salaryIncrement', 'employees'));
    }

    public function update(Request $request, SalaryIncrement $salaryIncrement)
    {   
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'increment_amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
        ]);

        $currentSalary = EmployeeSalary::where('employee_id', $request->employee_id)
            ->where('salary_month', '<', $request->effective_date)
            ->orderBy('salary_month', 'desc')
            ->first();

        $newSalary = $currentSalary->basic_salary + $request->increment_amount;

        $salaryIncrement->update([
            'employee_id' => $request->employee_id,
            'current_salary' => $currentSalary->basic_salary,
            'increment_amount' => $request->increment_amount,
            'new_salary' => $newSalary,
            'effective_date' => $request->effective_date,
            'remarks' => $request->remarks ?? null
        ]);

        // Update or create new salary record
        EmployeeSalary::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'salary_month' => $request->effective_date
            ],
            [
                'basic_salary' => $newSalary,
                'allowances' => $currentSalary->allowances,
                'deductions' => $currentSalary->deductions,
                'payment_date' => now(),
                'status' => 'Pending'
            ]
        );

        return redirect()->route('salary-increments.index')->with('success', 'Salary increment updated successfully');
    }

    public function destroy(SalaryIncrement $salaryIncrement)
    {
        $salaryIncrement->delete();

        return redirect()->route('salary-increments.index')->with('success', 'Salary increment deleted successfully');
    }
}
