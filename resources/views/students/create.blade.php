@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add New Student</h1>
    </div>

    <!-- Form Section -->
    <div class="bg-white shadow-md rounded px-6 py-6">
        <form method="POST" action="{{ route('students.store') }}">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('name') border-red-500 bg-red-50 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Class -->
            <div class="mb-4">
                <label for="class_id" class="block text-gray-700 font-medium mb-2">Class</label>
                <select id="class_id" name="class_id" required 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('class_id') border-red-500 bg-red-50 @enderror">
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->class_name }} {{ $class->section }}
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-medium mb-2">Address</label>
                <textarea id="address" name="address" required
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('address') border-red-500 bg-red-50 @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Number -->
            <div class="mb-4">
                <label for="contact_number" class="block text-gray-700 font-medium mb-2">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('contact_number') border-red-500 bg-red-50 @enderror">
                @error('contact_number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div class="mb-4">
                <label for="dob" class="block text-gray-700 font-medium mb-2">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="{{ old('dob') }}" required 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('dob') border-red-500 bg-red-50 @enderror">
                @error('dob')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email (Optional)</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('email') border-red-500 bg-red-50 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('password') border-red-500 bg-red-50 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-medium mb-2">Status</label>
                <select id="status" name="status" required 
                    class="w-full bg-gray-100 border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 @error('status') border-red-500 bg-red-50 @enderror">
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enabled</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disabled</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
                    Create Student
                </button>
                <a href="{{ route('students.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
