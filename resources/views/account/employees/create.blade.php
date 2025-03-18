@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add New Employee</h1>
        <a href="{{ route('account.employees.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Back to List
        </a>
    </div>
   

    <div class="py-12">
        {{-- <div class="max-w-7xl mx-auto sm:px-6 lg:px-8"> --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('account.employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
                                
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Employment Details -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900">Employment Details</h3>
                                
                                <div>
                                    <label for="department_id" class="block text-sm font-medium text-gray-700">Department</label>
                                    <select 
                                        name="department_id" 
                                        id="department_id" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="" disabled selected>Select Department</option>
                                        <option value="1">Human Resources</option>
                                        <option value="2">Finance</option>
                                        <option value="3">Information Technology</option>
                                        <option value="4">Marketing</option>
                                        <option value="5">Operations</option>
                                        <option value="6">Research & Development</option>
                                        <option value="7">Customer Service</option>
                                        <option value="8">Sales</option>
                                        <option value="9">Legal</option>
                                        <option value="10">Executive</option>
                                    </select>
                                </div>
                                

                                <div>
                                    <label for="designation" class="block text-sm font-medium text-gray-700">Designation</label>
                                    <input type="text" name="designation" id="designation" value="{{ old('designation') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('designation')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="joining_date" class="block text-sm font-medium text-gray-700">Joining Date</label>
                                    <input type="date" name="joining_date" id="joining_date" value="{{ old('joining_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('joining_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Salary Information -->
                            <div class="space-y-6">
                                <h3 class="text-lg font-medium text-gray-900">Salary Information</h3>
                                
                                <div>
                                    <label for="basic_salary" class="block text-sm font-medium text-gray-700">Basic Salary</label>
                                    <input type="number" step="0.01" name="basic_salary" id="basic_salary" value="{{ old('basic_salary') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('basic_salary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="allowances" class="block text-sm font-medium text-gray-700">Allowances</label>
                                    <input type="number" step="0.01" name="allowances" id="allowances" value="{{ old('allowances', 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('allowances')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="deductions" class="block text-sm font-medium text-gray-700">Deductions</label>
                                    <input type="number" step="0.01" name="deductions" id="deductions" value="{{ old('deductions', 0) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('deductions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                       </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('account.employees.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Create Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection