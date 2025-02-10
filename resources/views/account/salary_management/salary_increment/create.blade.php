@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-6">Create Salary Increment</h2>

        <form action="{{ route('account.salary_management.salary_increment.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employee">
                    Select Employee
                </label>
                <select name="employee_id" id="employee" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" data-current-salary="{{ $employee->salary }}">
                            {{ $employee->name }} - {{ $employee->department }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="current_salary">
                    Current Salary (&#8377;)
                </label>
                <input type="number" name="current_salary" id="current_salary" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline" readonly>
                @error('current_salary')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="increment_amount">
                    Increment Amount (&#8377;)
                </label>
                <input type="number" name="increment_amount" id="increment_amount" step="0.01" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('increment_amount')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="new_salary">
                    New Salary (&#8377;)
                </label>
                <input type="number" name="new_salary" id="new_salary" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 bg-gray-100 leading-tight focus:outline-none focus:shadow-outline" readonly>
                @error('new_salary')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="effective_date">
                    Effective Date
                </label>
                <input type="date" name="effective_date" id="effective_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                @error('effective_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="status">
                    Status
                </label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="">Select Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                    <option value="Rejected">Rejected</option>
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
                    Create Increment Record
                </button>
                <a href="{{ route('account.salary_management.salary_increment.index') }}" class="text-blue-500 hover:text-blue-700">
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

@push('scripts')
<script>
    document.getElementById('employee').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const currentSalary = selectedOption.dataset.currentSalary || 0;
        document.getElementById('current_salary').value = currentSalary;
        calculateNewSalary();
    });

    document.getElementById('increment_amount').addEventListener('input', function() {
        calculateNewSalary();
    });

    function calculateNewSalary() {
        const currentSalary = parseFloat(document.getElementById('current_salary').value) || 0;
        const incrementAmount = parseFloat(document.getElementById('increment_amount').value) || 0;
        const newSalary = currentSalary + incrementAmount;
        document.getElementById('new_salary').value = newSalary.toFixed(2);
    }
</script>
@endpush

@endsection
