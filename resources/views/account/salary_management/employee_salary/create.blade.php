@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-6">Create Employee Salary</h2>

        <form action="{{ route('account.salary_management.employee_salary.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employee">
                    Select Employee
                </label>
                <select name="employee_id" id="employee" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" data-basic-salary="{{ $employee->basic_salary }}">
                            {{ $employee->name }} - {{ $employee->department }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="basic_salary">
                    Basic Salary (&#8377;)
                </label>
                <input type="number" name="basic_salary" id="basic_salary" step="0.01" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('basic_salary')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="allowances">
                    Allowances (&#8377;)
                </label>
                <input type="number" name="allowances" id="allowances" step="0.01" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('allowances')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="deductions">
                    Deductions (&#8377;)
                </label>
                <input type="number" name="deductions" id="deductions" step="0.01" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('deductions')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="total_salary">
                    Net Salary (&#8377;)
                </label>
                <input type="number" name="total_salary" id="total_salary" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_date">
                    Payment Date
                </label>
                <input type="date" name="payment_date" id="payment_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('payment_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_method">
                    Payment Method
                </label>
                <select name="payment_method" id="payment_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Payment Method</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="cash">Cash</option>
                    <option value="check">Check</option>
                </select>
                @error('payment_method')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">  
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                    Status
                </label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>  

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="remarks">
                    Remarks
                </label>
                <textarea name="remarks" id="remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                @error('remarks')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Create Salary Record
                </button>
                <a href="{{ route('account.salary_management.employee_salary.index') }}" class="text-blue-500 hover:text-blue-700">
                    Back to List
                </a>
            </div>

        </form>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
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

<script>
    // Auto-populate basic salary when employee is selected
    document.getElementById('employee').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const basicSalary = selectedOption.dataset.basicSalary || '';
        document.getElementById('basic_salary').value = basicSalary;
        calculateTotal();
    });

    // Calculate total salary
    function calculateTotal() {
        const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
        const allowances = parseFloat(document.getElementById('allowances').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        
        const totalSalary = basicSalary + allowances - deductions;
        
        // Update the total salary field
        document.getElementById('total_salary').value = totalSalary.toFixed(2);
    }

    // Add event listeners to recalculate when values change
    document.getElementById('basic_salary').addEventListener('input', calculateTotal);
    document.getElementById('allowances').addEventListener('input', calculateTotal);
    document.getElementById('deductions').addEventListener('input', calculateTotal);

    // Set default payment date to today
    document.getElementById('payment_date').valueAsDate = new Date();

    // Initial calculation on page load
    calculateTotal();
</script>
@endsection