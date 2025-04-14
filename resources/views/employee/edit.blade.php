@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Employee</h1>
        <a href="{{ route('employee.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
            Back to List
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6">
            <form method="POST" action="{{ route('employee.update', $employee->id) }}" enctype="multipart/form-data" id="employeeForm">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Name</label>
                    <input id="name" type="text" 
                           class="w-full px-3 py-2 border rounded-md @error('name') border-red-500 @enderror"
                           name="name" value="{{ old('name', $employee->name) }}" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input id="email" type="email" 
                           class="w-full px-3 py-2 border rounded-md @error('email') border-red-500 @enderror"
                           name="email" value="{{ old('email', $employee->email) }}" required>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

              

                <div class="mb-4">
                    <label for="department" class="block text-gray-700 text-sm font-bold mb-2">Department</label>
                    <select id="department" 
                            class="w-full px-3 py-2 border rounded-md @error('department') border-red-500 @enderror"
                            name="department" 
                            required
                            onchange="this.classList.remove('border-red-500')">
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" 
                                    {{ old('department', $employee->department_id) == $department->id ? 'selected' : '' }}
                                    data-department-code="{{ $department->code ?? '' }}">
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-600 text-xs mt-1" id="departmentInfo"></p>
                </div>

                <div class="mb-4">
                    <label for="position" class="block text-gray-700 text-sm font-bold mb-2">Position</label>
                    <input id="position" type="text" 
                           class="w-full px-3 py-2 border rounded-md @error('position') border-red-500 @enderror"
                           name="position" value="{{ old('position', $employee->position) }}" required>
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="salary" class="block text-gray-700 text-sm font-bold mb-2">Salary</label>
                    <input id="salary" type="number" step="0.01"
                           class="w-full px-3 py-2 border rounded-md @error('salary') border-red-500 @enderror"
                           name="salary" value="{{ old('salary', $employee->salary) }}" required>
                    @error('salary')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="join_date" class="block text-gray-700 text-sm font-bold mb-2">Join Date</label>
                    <input id="join_date" type="date"
                           class="w-full px-3 py-2 border rounded-md @error('join_date') border-red-500 @enderror"
                           name="join_date" value="{{ old('join_date', $employee->join_date) }}" required>
                    @error('join_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select id="status"
                            class="w-full px-3 py-2 border rounded-md @error('status') border-red-500 @enderror"
                            name="status" required>
                        <option value="1" {{ old('status', $employee->status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $employee->status) == 0 ? 'selected' : '' }}>Inactive</option>
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
                            class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function validateForm() {
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

    const emailField = document.getElementById('email');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailField.value)) {
        emailField.classList.add('border-red-500');
        isValid = false;
    }

    return isValid;
}

document.getElementById('employeeForm').addEventListener('submit', function(event) {
    if (!validateForm()) {
        event.preventDefault();
        alert('Please fill in all required fields correctly.');
    }
});
</script>
@endpush

@endsection
