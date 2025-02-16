<?php

namespace App\Http\Controllers;

use App\Models\EmployeeSalary;
use App\Models\Employee;
use App\Models\Department;
use App\Models\SalaryComponent;

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

    $departments = Department::all(); // Fetch all departments

    return view('account.salary_management.employee_salary.index', compact('employeeSalaries', 'employees', 'totalSalary', 'averageSalary', 'departments'));
}


 
    public function create()
    {
        $employees = Employee::all();
        $departments = Department::all();
        $salaryComponents = SalaryComponent::all();
        return view('account.salary_management.employee_salary.create', compact('employees', 'departments', 'salaryComponents'));   
    }

  
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employee,id',
            'basic_salary' => 'required|numeric',
            'allowances' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'status' => 'required|string|in:paid,unpaid,pending,rejected,approved',
            'remarks' => 'nullable|string',
            'net_salary' => 'required|numeric',
        ]);
        

        EmployeeSalary::create([
            'employee_id' => $request->employee_id,
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances,
            'deductions' => $request->deductions,
            'status' => $request->status,
            'remarks' => $request->remarks,
            'net_salary' => $request->net_salary,
            'payment_date' => $request->payment_date ?? now(),
            'payment_method' => $request->payment_method ?? 'cash', // Default to 'cash'
        ]);
    }
        

  
    public function show(EmployeeSalary $employeeSalary)
    {
        $employeeSalary = EmployeeSalary::findOrFail($employeeSalary->id);
    return view('account.salary_management.employee_salary.show', compact('employeeSalary'));

    }
  
    public function edit(EmployeeSalary $employeeSalary)
    {   
        $employees = Employee::all();
        return view('account.salary_management.employee_salary.edit', compact('employeeSalary', 'employees'));
    }

  
    public function update(Request $request, EmployeeSalary $employeeSalary)
    {
        $request->validate([
            'basic_salary' => 'required|numeric',
            'allowances' => 'nullable|numeric',
            'deductions' => 'nullable|numeric',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string', // Ensure this field is required
            'status' => 'required|string|in:paid,unpaid,pending,rejected,approved', 
            'remarks' => 'nullable|string',
            'net_salary' => 'required|numeric',
        ]);
    
        $employeeSalary = EmployeeSalary::findOrFail($employeeSalary->id);
    
        $employeeSalary->update([
            'basic_salary' => $request->basic_salary,
            'allowances' => $request->allowances ?? 0,
            'deductions' => $request->deductions ?? 0,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method ?? 'cash', // ✅ Default to 'cash'
            'status' => $request->status,
            'remarks' => $request->remarks,
            'net_salary' => $request->net_salary,
        ]);
    
        return redirect()->route('account.salary_management.employee_salary.index')
            ->with('success', 'Salary record updated successfully.');
    }

   
    public function destroy(EmployeeSalary $employeeSalary)
    {
        $employeeSalary->delete();

        return redirect()->route('account.salary_management.employee_salary.index')
            ->with('success', 'Employee salary deleted successfully.');
    }


    public function showEmployeeSalary()
    {
        $departments = Department::all();  // Fetch all departments
    
        return view('account.salary_management.employee_salary.index', compact('departments'));
    }

}
