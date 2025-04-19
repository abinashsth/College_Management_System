@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Salary Record</h1>
        <a href="{{ route('salary.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to List
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <form method="POST" action="{{ route('salary.store') }}" id="salaryForm">
                @csrf

                <div class="mb-4">
                    <label for="employee_id" class="block text-gray-700 text-sm font-bold mb-2">Employee</label>
                    <select id="employee_id" name="employee_id" 
                            class="w-full px-3 py-2 border rounded-md @error('employee_id') border-red-500 @enderror"
                            required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" 
                                    data-basic-salary="{{ $employee->salary }}">
                                {{ $employee->name }} ({{ $employee->employee_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="basic_salary" class="block text-gray-700 text-sm font-bold mb-2">Basic Salary</label>
                    <input type="number" step="0.01" id="basic_salary" name="basic_salary"
                           class="w-full px-3 py-2 border rounded-md @error('basic_salary') border-red-500 @enderror"
                           required readonly>
                    @error('basic_salary')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="allowances" class="block text-gray-700 text-sm font-bold mb-2">Allowances</label>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" name="allowances[]" 
                                   class="flex-1 px-3 py-2 border rounded-md allowance-input"
                                   placeholder="Amount">
                            <input type="text" name="allowance_descriptions[]" 
                                   class="flex-1 px-3 py-2 border rounded-md"
                                   placeholder="Description">
                        </div>
                        <button type="button" onclick="addAllowanceField()"
                                class="text-blue-500 text-sm">+ Add Another Allowance</button>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="deductions" class="block text-gray-700 text-sm font-bold mb-2">Deductions</label>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2">
                            <input type="number" step="0.01" name="deductions[]" 
                                   class="flex-1 px-3 py-2 border rounded-md deduction-input"
                                   placeholder="Amount">
                            <input type="text" name="deduction_descriptions[]" 
                                   class="flex-1 px-3 py-2 border rounded-md"
                                   placeholder="Description">
                        </div>
                        <button type="button" onclick="addDeductionField()"
                                class="text-blue-500 text-sm">+ Add Another Deduction</button>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="payment_date" class="block text-gray-700 text-sm font-bold mb-2">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date"
                           class="w-full px-3 py-2 border rounded-md @error('payment_date') border-red-500 @enderror"
                           required>
                    @error('payment_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4">
                    <button type="button" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                            onclick="window.location.href='{{ route('salary.index') }}'">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
                        Create Salary Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('employee_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const basicSalary = selectedOption.dataset.basicSalary || '';
    document.getElementById('basic_salary').value = basicSalary;
});

function addAllowanceField() {
    const container = document.querySelector('[name="allowances[]"]').parentElement.parentElement;
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-2';
    newRow.innerHTML = `
        <input type="number" step="0.01" name="allowances[]" 
               class="flex-1 px-3 py-2 border rounded-md allowance-input"
               placeholder="Amount">
        <input type="text" name="allowance_descriptions[]" 
               class="flex-1 px-3 py-2 border rounded-md"
               placeholder="Description">
        <button type="button" onclick="this.parentElement.remove()"
                class="text-red-500">Remove</button>
    `;
    container.insertBefore(newRow, container.lastElementChild);
}

function addDeductionField() {
    const container = document.querySelector('[name="deductions[]"]').parentElement.parentElement;
    const newRow = document.createElement('div');
    newRow.className = 'flex items-center gap-2';
    newRow.innerHTML = `
        <input type="number" step="0.01" name="deductions[]" 
               class="flex-1 px-3 py-2 border rounded-md deduction-input"
               placeholder="Amount">
        <input type="text" name="deduction_descriptions[]" 
               class="flex-1 px-3 py-2 border rounded-md"
               placeholder="Description">
        <button type="button" onclick="this.parentElement.remove()"
                class="text-red-500">Remove</button>
    `;
    container.insertBefore(newRow, container.lastElementChild);
}
</script>
@endpush
@endsection