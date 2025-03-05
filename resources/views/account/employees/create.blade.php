@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Employee</h1>
        <a href="{{ route('account.employee.index') }}" class="text-gray-600 hover:text-gray-800">
            Back to Employees
        </a>
    </div>

    <div class="bg-white rounded shadow-md max-w-3xl mx-auto p-6">
        <form action="{{ route('account.employee.store') }}" method="POST">
            @csrf

            <!-- Employee ID -->
            <div class="mb-4">
                <label for="employee_id" class="block text-gray-700 font-medium mb-2">Employee ID</label>
                <input type="text" id="employee_id" name="employee_id" value="{{ old('employee_id') }}" required
                       class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                       placeholder="Enter employee ID">
                @error('employee_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Name -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Employee Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter employee name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Employee email</label>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter employee email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div class="mb-4">
                    <label for="department" class="block text-gray-700 font-medium mb-2">Department</label>
                    <select id="department" name="department" required
                            class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        <option value="">Select Department</option>
                        <option value="HR" {{ old('department') == 'HR' ? 'selected' : '' }}>Human Resources</option>
                        <option value="IT" {{ old('department') == 'IT' ? 'selected' : '' }}>Information Technology</option>
                        <option value="Finance" {{ old('department') == 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option value="Marketing" {{ old('department') == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="Operations" {{ old('department') == 'Operations' ? 'selected' : '' }}>Operations</option>
                    </select>
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Designation -->
                <div class="mb-4">
                    <label for="designation" class="block text-gray-700 font-medium mb-2">Designation</label>
                    <input type="text" id="designation" name="designation" value="{{ old('designation') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter designation">
                    @error('designation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>


                  <!-- Net Salary -->
                  <div class="mb-4">
                    <label for="net_salary" class="block text-gray-700 font-medium mb-2">Net Salary</label>
                    <input type="text" id="net_salary" name="net_salary" value="{{ old('net_salary') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter Net Salary">
                    @error('net_salary')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact -->
                <div class="mb-4">
                    <label for="contact" class="block text-gray-700 font-medium mb-2">Contact</label>
                    <input type="text" id="contact" name="contact" value="{{ old('contact') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none" 
                           pattern="[0-9]{10}" maxlength="10"
                           placeholder="Enter 10 digit contact number"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)">
                    @error('contact')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-fl
                        ex items-center">
                            <input type="radio" name="status" value="1" 
                                   {{ old('status', '1') === '1' ? 'checked' : '' }}
                                   class="text-teal-600 focus:ring-teal-500">
                            <span class="ml-2">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="0" 
                                   {{ old('status') === '0' ? 'checked' : '' }}
                                   class="text-teal-600 focus:ring-teal-500">
                            <span class="ml-2">Inactive</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('account.employee.index') }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Create Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
