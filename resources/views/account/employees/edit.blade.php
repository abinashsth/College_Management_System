@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Update Employee</h1>
            <p class="text-gray-600 text-sm mt-1">Edit employee information and details</p>
        </div>
        <a href="{{ route('account.employees.index') }}" class="flex items-center text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Employees
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg max-w-4xl mx-auto p-8">
        <form action="{{ route('account.employees.update', $employee->employee_id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Employee Name -->
                <div>
                    <label for="name" class="block text-gray-700 font-semibold mb-2">Employee Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="Enter employee name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="Enter work email">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department -->
                <div>
                    <label for="department" class="block text-gray-700 font-semibold mb-2">Department</label>
                    <select id="department" name="department" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="">Select Department</option>
                        <option value="HR" {{ old('department', $employee->department) == 'HR' ? 'selected' : '' }}>Human Resources</option>
                        <option value="IT" {{ old('department', $employee->department) == 'IT' ? 'selected' : '' }}>Information Technology</option>
                        <option value="Finance" {{ old('department', $employee->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                        <option value="Marketing" {{ old('department', $employee->department) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                        <option value="Operations" {{ old('department', $employee->department) == 'Operations' ? 'selected' : '' }}>Operations</option>
                    </select>
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Designation -->
                <div>
                    <label for="designation" class="block text-gray-700 font-semibold mb-2">Designation</label>
                    <input type="text" id="designation" name="designation" value="{{ old('designation', $employee->designation) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           placeholder="Enter job title">
                    @error('designation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact -->
                <div>
                    <label for="contact" class="block text-gray-700 font-semibold mb-2">Contact Number</label>
                    <input type="number" id="contact" name="contact" value="{{ old('contact', $employee->contact) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                           min="0" max="9999999999"
                           placeholder="Enter 10 digit contact number"
                           oninput="javascript: if (this.value.length > 10) this.value = this.value.slice(0, 10);">
                    @error('contact')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Employment Status</label>
                    <div class="flex items-center space-x-6 bg-gray-50 p-4 rounded-lg">
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="1" 
                                   {{ old('status', $employee->status) == '1' ? 'checked' : '' }}
                                   class="form-radio text-teal-600 focus:ring-teal-500">
                            <span class="ml-2 text-gray-700">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="status" value="0" 
                                   {{ old('status', $employee->status) == '0' ? 'checked' : '' }}
                                   class="form-radio text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-gray-700">Inactive</span>
                        </label>
                    </div>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('account.employees.index') }}" 
                   class="px-6 py-2.5 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition duration-200">
                    Update Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
