@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Student</h1>
        <a href="{{ route('students.index') }}" class="text-gray-600 hover:text-gray-800">
            Back to Students
        </a>
    </div>

    <div class="bg-white rounded shadow-md max-w-3xl mx-auto p-6">
        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Student Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter student name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter email address">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter password">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Confirm password">
                </div>

                <div class="mb-4">
                    <label for="class_id" class="block text-gray-700 font-medium mb-2">Class</label>
                    <select id="class_id" name="class_id" required
                            class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }} - {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="contact_number" class="block text-gray-700 font-medium mb-2">Contact Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}"
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                           placeholder="Enter contact number">
                    @error('contact_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="dob" class="block text-gray-700 font-medium mb-2">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob') }}"
                           class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    @error('dob')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                    <textarea id="address" name="address" rows="3"
                              class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                              placeholder="Enter address">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Status</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
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

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('students.index') }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Create Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
