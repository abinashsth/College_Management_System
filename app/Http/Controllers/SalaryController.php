<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Salary;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    /**
     * Display salary history for an employee.
     */
    public function history(Employee $employee)
    {
        $salaries = $employee->salaries()->orderBy('effective_date', 'desc')->get();
        return view('salaries.history', compact('employee', 'salaries'));
    }
    
    /**
     * Show form to add new salary.
     */
    public function create(Employee $employee)
    {
        return view('salaries.create', compact('employee'));
    }
    
    /**
     * Store a new salary record.
     */
    public function store(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'effective_date' => 'required|date',
            'payment_type' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        $employee->salaries()->create($validated);
        
        return redirect()->route('account.employee.show', $employee)
            ->with('success', 'Salary information updated successfully');
    }
}