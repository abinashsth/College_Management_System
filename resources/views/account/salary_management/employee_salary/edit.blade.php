
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-bold mb-6">Edit Employee Salary Record</h2>

        <form action="{{ route('account.salary_management.employee_salary.update', $employeeSalary->id) }}" method="POST">
            @csrf
            @method('PUT')

            @if(session('success'))     
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif  

            @if($errors->any())     
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">There were some problems with your input.</span>
                </div>
            @endif

            {{-- <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employee_id">
                    Employee ID
                </label>
                <input type="text" name="employee_id" id="employee_id" value="{{ $employeeSalary->employee->employee_id }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" disabled>
                @error('employee_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div> --}}

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employee_id">

                    Employee
                </label>
                <select name="employee_id" id="employee_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" disabled>
                    <option value="{{ $employeeSalary->employee_id }}">{{ $employeeSalary->employee->name }}</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="basic_salary">
                    Basic Salary
                </label>
                <input type="number" name="basic_salary" id="basic_salary" value="{{ $employeeSalary->basic_salary }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="calculateTotal()">
                @error('basic_salary')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="allowances">
                    Allowances
                </label>
                <input type="number" name="allowances" id="allowances" value="{{ $employeeSalary->allowances }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="calculateTotal()">
                @error('allowances')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="deductions">
                    Deductions
                </label>
                <input type="number" name="deductions" id="deductions" value="{{ $employeeSalary->deductions }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" onchange="calculateTotal()">
                @error('deductions')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="net_salary">
                    Net Salary
                </label>
                <input type="number" name="net_salary" id="net_salary" value="{{ $employeeSalary->net_salary }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" readonly>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_date">
                    Payment Date
                </label>
                <input type="date" name="payment_date" id="payment_date" value="{{ $employeeSalary->payment_date }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                @error('payment_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_method">
                    Payment Method
                </label>
                <select name="payment_method" id="payment_method" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="bank_transfer" {{ $employeeSalary->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    <option value="cash" {{ $employeeSalary->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="check" {{ $employeeSalary->payment_method == 'check' ? 'selected' : '' }}>Check</option>
                </select>
                @error('payment_method')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                    Status
                </label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="paid" {{ $employeeSalary->status == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ $employeeSalary->status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="pending" {{ $employeeSalary->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="rejected" {{ $employeeSalary->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="approved" {{ $employeeSalary->status == 'approved' ? 'selected' : '' }}>Approved</option>    
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="remarks">
                    Remarks
                </label>
                <textarea name="remarks" id="remarks" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ $employeeSalary->remarks }}</textarea>
                @error('remarks')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Update Salary Record
                </button>
                <a href="{{ route('account.salary_management.employee_salary.index') }}" class="text-blue-500 hover:text-blue-700">
                    Back to List
                </a>
            </div>
        </form>
    </div>

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

<script>
    function calculateTotal() {
        const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
        const allowances = parseFloat(document.getElementById('allowances').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        
        const totalSalary = basicSalary + allowances - deductions;
        document.getElementById('total_salary').value = totalSalary.toFixed(2);
    }
</script>
@endsection
