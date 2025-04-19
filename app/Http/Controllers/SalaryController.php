<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use App\Models\SalaryDetail;
use Illuminate\Http\Request;
use DB;

class SalaryController extends Controller
{
    public function index()
    {
        $salaries = Salary::with('employee')->latest()->paginate(10);
        return view('salary.index', compact('salaries'));
    }

    public function create()
    {
        $employees = Employee::all();

        return view('salary.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'basic_salary' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'allowances' => 'nullable|array',
            'allowance_descriptions' => 'nullable|array',
            'deductions' => 'nullable|array',
            'deduction_descriptions' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $validated) {
            $totalAllowances = array_sum($request->allowances ?? []);
            $totalDeductions = array_sum($request->deductions ?? []);
            $netSalary = $validated['basic_salary'] + $totalAllowances - $totalDeductions;

            $salary = Salary::create([
                'employee_id' => $validated['employee_id'],
                'basic_salary' => $validated['basic_salary'],
                'allowances' => $totalAllowances,
                'deductions' => $totalDeductions,
                'net_salary' => $netSalary,
                'payment_date' => $validated['payment_date'],
               
            ]);

            // Store allowances
            if (!empty($request->allowances)) {
                foreach ($request->allowances as $key => $amount) {
                    if ($amount > 0) {
                        SalaryDetail::create([
                            'salary_id' => $salary->id,
                            'type' => 'allowance',
                            'amount' => $amount,
                            'description' => $request->allowance_descriptions[$key] ?? null,
                        ]);
                    }
                }
            }

            // Store deductions
            if (!empty($request->deductions)) {
                foreach ($request->deductions as $key => $amount) {
                    if ($amount > 0) {
                        SalaryDetail::create([
                            'salary_id' => $salary->id,
                            'type' => 'deduction',
                            'amount' => $amount,
                            'description' => $request->deduction_descriptions[$key] ?? null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('salary.index')
            ->with('success', 'Salary record created successfully');
    }
}