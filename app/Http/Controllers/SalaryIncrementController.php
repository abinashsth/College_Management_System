<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SalaryIncrement;
use App\Models\Employee;    

class SalaryIncrementController extends Controller
{
    public function index()
    {
        $salaryIncrements = SalaryIncrement::paginate(10); 
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
            'increment_date' => 'required|date'
        ]);

        $increment = SalaryIncrement::create($request->all());

        // Update employee's salary
        $employee = Employee::find($request->employee_id);
        $employee->salary += $request->increment_amount;
        $employee->save();

        return redirect()->route('employees.index')->with('success', 'Salary increment added successfully.');
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
            'increment_date' => 'required|date',
        ]);

        $salaryIncrement->employee_id = $request->employee_id;
        $salaryIncrement->increment_amount = $request->increment_amount;
        $salaryIncrement->increment_date = $request->increment_date;
        $salaryIncrement->save();

        return redirect()->route('account.salary_management.salary_increment.index')->with('success', 'Salary increment updated successfully');
    }

    public function destroy(SalaryIncrement $salaryIncrement)
    {
        $salaryIncrement->delete();

        return redirect()->route('account.salary_management.salary_increment.index')->with('success', 'Salary increment deleted successfully');
    }
}
