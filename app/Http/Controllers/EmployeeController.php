<?php

namespace App\Http\Controllers;


use App\Models\ClassModel; 
use App\Models\Employee; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class EmployeeController extends Controller
{
    public function index()
    {
        // Fetch accounts data
        $employees = Employee::paginate(10);

        // Pass the data to the view
        return view('account.employee.index', compact('employees'));
    }

    public function create()
    {
          // Fetch the list of classes from the database
          $classes = ClassModel::all(); // Replace 'ClassModel' with your actual class model name

          // Pass the classes to the view
          return view('account.employee.create', compact('classes'));

    }


    public function store(Request $request)
    {
        // Validate and process the incoming request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'position' => 'required|string',
            'salary' => 'required|numeric',
        ]);

        // Save employee to the database
        \App\Models\Employee::create($validated);

        return redirect()->route('account.employee.index')->with('success', 'Employee added successfully.');
    }


}
