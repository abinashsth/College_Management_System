<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\SalarySheet;  

class SalaryGenerationController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('account.salary_management.generate_salary.index', compact('employees'));
    }

    public function create()
    {
        $employees = Employee::all();
        return view('account.salary_management.generate_salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'payment_date' => 'required|date',
            'employee' => 'nullable|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $salarySheet = new SalarySheet();
        $salarySheet->month = $request->month;
        $salarySheet->payment_date = $request->payment_date;
        $salarySheet->employee_id = $request->employee;
        $salarySheet->basic_salary = $request->basic_salary;
        $salarySheet->allowances = $request->allowances ?? 0;
        $salarySheet->deductions = $request->deductions ?? 0;
        $salarySheet->note = $request->note;
        $salarySheet->save();

        return redirect()->route('account.salary_management.generate_salary.index')
            ->with('success', 'Salary sheet created successfully.');
    }

    public function edit(SalarySheet $salaryGeneration)
    {
        $employees = Employee::all();
        return view('account.salary_management.generate_salary.edit', compact('salaryGeneration', 'employees'));
    }

    public function update(Request $request, SalarySheet $salaryGeneration)
    {
        $request->validate([
            'month' => 'required',
            'payment_date' => 'required|date',
            'employee' => 'nullable|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'note' => 'nullable|string'
        ]);

        $salaryGeneration->update([
            'month' => $request->month,
            'payment_date' => $request->payment_date,
            'employee_id' => $request->employee,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances ?? 0,
            'deductions' => $request->deductions ?? 0,
            'note' => $request->note
        ]);

        return redirect()->route('account.salary_management.generate_salary.index')
            ->with('success', 'Salary sheet updated successfully.');
    }

    public function destroy(SalarySheet $salaryGeneration)
    {
        $salaryGeneration->delete();
        return redirect()->route('account.salary_management.generate_salary.index')
            ->with('success', 'Salary sheet deleted successfully.');
    }
}
