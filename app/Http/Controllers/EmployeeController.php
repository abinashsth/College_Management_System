<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query()->with('department');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Salary range filter
        if ($request->filled('salary_range')) {
            [$min, $max] = explode('-', $request->salary_range);
            $query->where('basic_salary', '>=', $min);
            if ($max !== '+') {
                $query->where('basic_salary', '<=', $max);
            }
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $employees = $query->paginate(10)->withQueryString();
        $departments = Department::all();

        return view('account.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('account.employees.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0'
         
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Generate unique employee ID
        $validated['employee_id'] = 'EMP' . str_pad(Employee::max('id') + 1, 5, '0', STR_PAD_LEFT);
        
        Employee::create($validated);

        return redirect()->route('account.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function show(Employee $employee)
    {
        return view('account.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $departments = Department::all();
        return view('account.employees.edit', compact('employee', 'departments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string|max:100',
            'joining_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'allowances' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0'
          
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $employee->update($validated);

        return redirect()->route('account.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if ($employee->avatar) {
            Storage::disk('public')->delete($employee->avatar);
        }
        
        $employee->delete();

        return redirect()->route('account.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:employees,id'
        ]);

        $employees = Employee::whereIn('id', $validated['ids'])->get();

        foreach ($employees as $employee) {
            if ($employee->avatar) {
                Storage::disk('public')->delete($employee->avatar);
            }
        }

        Employee::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('account.employees.index')
            ->with('success', 'Selected employees deleted successfully.');
    }

 /**
     * Export employees to Excel/CSV file
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    
    public function export()
    {
        // Fetch employee data from the database
        $employees = Employee::all();

        // Create a CSV file or any other format you need
        $csvFileName = 'employees_export_' . date('Y-m-d') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        // Open output stream
        $handle = fopen('php://output', 'w');

        // Add CSV header
        fputcsv($handle, ['ID', 'Name', 'Email', 'Position']); // Adjust according to your Employee model

        // Add employee data to CSV
        foreach ($employees as $employee) {
            fputcsv($handle, [$employee->id, $employee->name, $employee->email, $employee->position]); // Adjust according to your Employee model
        }

        fclose($handle);

    }

}