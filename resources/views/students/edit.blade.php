@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Student</h1>
        <a href="{{ route('students.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
            Cancel
        </a>
    </div>

    <!-- Form Section -->
    <div class="bg-white rounded shadow-md p-6">
        <form method="POST" action="{{ route('students.update', $student) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" 
                       class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 bg-red-50 @enderror" 
                       required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Class -->
            <div class="mb-4">
                <label for="class_id" class="block text-sm font-medium text-gray-700">Class</label>
                <select id="class_id" name="class_id" 
                        class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('class_id') border-red-500 bg-red-50 @enderror" 
                        required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
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
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea id="address" name="address" 
                          class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 bg-red-50 @enderror" 
                          required>{{ old('address', $student->address) }}</textarea>
                @error('address')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Number -->
            <div class="mb-4">
                <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $student->contact_number) }}" 
                       class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('contact_number') border-red-500 bg-red-50 @enderror" 
                       required>
                @error('contact_number')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div class="mb-4">
                <label for="dob" class="block text-sm font-medium text-gray-700">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="{{ old('dob', $student->dob->format('Y-m-d')) }}" 
                       class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('dob') border-red-500 bg-red-50 @enderror" 
                       required>
                @error('dob')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email (Optional)</label>
                <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" 
                       class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 bg-red-50 @enderror">
                @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password (Leave blank to keep current)</label>
                <input type="password" id="password" name="password" 
                       class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 bg-red-50 @enderror">
                @error('password')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" 
                        class="block w-full mt-1 rounded-md shadow-sm border-blue-300 bg-blue-50 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 bg-red-50 @enderror" 
                        required>
                    <option value="1" {{ old('status', $student->status) ? 'selected' : '' }}>Enabled</option>
                    <option value="0" {{ old('status', $student->status) ? '' : 'selected' }}>Disabled</option>
                </select>
                @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
                    Update Student
                </button>
                <a href="{{ route('students.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
