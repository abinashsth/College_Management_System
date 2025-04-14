@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Employee</h1>
        <a href="{{ route('employee.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to List
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <form method="POST" action="{{ route('employee.store') }}" enctype="multipart/form-data" id="employeeForm">
                @csrf

                <div class="mb-4">
                    <label for="employee_code" class="block text-gray-700 text-sm font-bold mb-2">Employee Code</label>
                    <input id="employee_code" type="number"
                           class="w-full px-3 py-2 border rounded-md @error('employee_code') border-red-500 @enderror"
                           name="employee_code" value="{{ old('employee_code') }}" required>
                    @error('employee_code')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Other form fields remain the same -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input id="name" type="text" 
                           class="w-full px-3 py-2 border rounded-md @error('name') border-red-500 @enderror"
                           name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input id="email" type="email" 
                           class="w-full px-3 py-2 border rounded-md @error('email') border-red-500 @enderror"
                           name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="department" class="block text-gray-700 text-sm font-bold mb-2">Department</label>
                    <select id="department" 
                            class="w-full px-3 py-2 border rounded-md @error('department') border-red-500 @enderror"
                            name="department" required>
                        <option value="">Select Department</option>
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Finance</option>
                        <option value="Marketing">Marketing</option>
                    </select>
                    @error('department')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="position" class="block text-gray-700 text-sm font-bold mb-2">Position</label>
                    <input id="position" type="text" 
                           class="w-full px-3 py-2 border rounded-md @error('position') border-red-500 @enderror"
                           name="position" value="{{ old('position') }}" required>
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="salary" class="block text-gray-700 text-sm font-bold mb-2">Salary</label>
                    <input id="salary" type="number" 
                           class="w-full px-3 py-2 border rounded-md @error('salary') border-red-500 @enderror"
                           name="salary" value="{{ old('salary') }}" required>  
                    @error('salary')    
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                    @enderror   
                </div>

                <div class="mb-4">
                    <label for="join_date" class="block text-gray-700 text-sm font-bold mb-2">Join Date</label>
                    <input id="join_date" type="date"
                           class="w-full px-3 py-2 border rounded-md @error('join_date') border-red-500 @enderror"
                           name="join_date" value="{{ old('join_date') }}" required>
                    @error('join_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select id="status"
                            class="w-full px-3 py-2 border rounded-md @error('status') border-red-500 @enderror"
                            name="status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-4">
                    <button type="button" 
                            class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600"
                            onclick="window.location.href='{{ route('employee.index') }}'">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700"
                            onclick="submitForm(event)">
                        Create Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function submitForm(event) {
    event.preventDefault();
    
    // Validate all required fields
    const form = document.getElementById('employeeForm');
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            isValid = false;
        } else {
            field.classList.remove('border-red-500');
        }
    });

    // Validate email format
    const emailField = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailField.value)) {
        emailField.classList.add('border-red-500');
        isValid = false;
    }

    // Validate salary (positive number)
    const salaryField = document.getElementById('salary');
    if (parseInt(salaryField.value) <= 0) {
        salaryField.classList.add('border-red-500');
        isValid = false;
    }

    if (isValid) {
        form.submit();
    } else {
        alert('Please fill in all required fields correctly.');
    }
}
</script>
@endpush

@endsection
