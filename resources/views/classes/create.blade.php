@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Class</h1>
        <a href="{{ route('classes.index') }}" class="text-gray-600 hover:text-gray-800">
            Back to Classes
        </a>
    </div>

    <div class="bg-white rounded shadow-md max-w-3xl mx-auto p-6">
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="class_name" class="block text-gray-700 font-medium mb-2">Class Name <span class="text-red-500">*</span></label>
                <input type="text" id="class_name" name="class_name" value="{{ old('class_name') }}" required
                       class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                       placeholder="Enter class name">
                @error('class_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Academic Year Selection -->
            <div class="mb-4">
                <label for="academic_year_id" class="block text-gray-700 font-medium mb-2">Academic Year <span class="text-red-500">*</span></label>
                <select id="academic_year_id" name="academic_year_id" required
                        class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="">Select Academic Year</option>
                    @foreach($academicYears as $academicYear)
                        <option value="{{ $academicYear->id }}" {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                            {{ $academicYear->name }}
                        </option>
                    @endforeach
                </select>
                @error('academic_year_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Department Selection -->
            <div class="mb-4">
                <label for="department_id" class="block text-gray-700 font-medium mb-2">Department <span class="text-red-500">*</span></label>
                <select id="department_id" name="department_id" required
                        class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="">Select Department</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                            {{ $department->name }}
                        </option>
                    @endforeach
                </select>
                @error('department_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Program Selection -->
            <div class="mb-4">
                <label for="program_id" class="block text-gray-700 font-medium mb-2">Program <span class="text-red-500">*</span></label>
                <select id="program_id" name="program_id" required
                        class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="">Select Program</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Capacity -->
            <div class="mb-4">
                <label for="capacity" class="block text-gray-700 font-medium mb-2">Capacity <span class="text-red-500">*</span></label>
                <input type="number" id="capacity" name="capacity" value="{{ old('capacity', 30) }}" required min="1"
                       class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                       placeholder="Enter maximum students">
                @error('capacity')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label for="status" class="block text-gray-700 font-medium mb-2">Status <span class="text-red-500">*</span></label>
                <select id="status" name="status" required
                        class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-medium mb-2">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                          placeholder="Enter class description">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('classes.index') }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Create Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection