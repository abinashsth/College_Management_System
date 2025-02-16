@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-6">Generate Salary Sheet</h2>

        <form action="{{ route('account.salary_management.generate_salary.generate') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="month">
                    Month
                </label>
                <input type="month" name="month" id="month" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('month')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>  

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="department">
                    Department (Optional)
                </label>
                <select name="department" id="department" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">All Departments</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employee">
                    Employee (Optional)
                </label>
                <select name="employee" id="employee" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }} - {{ $employee->employee_id }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Generate Salary Sheet
                </button>
            </div>
        </form>
    </div>

    @if(isset($salarySheet))
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h3 class="text-xl font-bold mb-4">Generated Salary Sheet</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Employee ID</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Name</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Basic Salary</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Allowances</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Deductions</th>
                        <th class="px-6 py-3 border-b-2 border-gray-300 text-left text-sm leading-4 tracking-wider">Net Salary</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($salarySheet as $salary)
                    <tr>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $salary->employee->employee_id }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ $salary->employee->name }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ number_format($salary->basic_salary, 2) }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ number_format($salary->total_allowances, 2) }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ number_format($salary->total_deductions, 2) }}</td>
                        <td class="px-6 py-4 whitespace-no-wrap border-b border-gray-500">{{ number_format($salary->net_salary, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <button onclick="window.print()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Print Salary Sheet
            </button>
        </div>
    </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
            <strong class="font-bold">Whoops!</strong>
            <span class="block sm:inline">There were some problems with your input.</span>
            <ul class="mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
@endsection
