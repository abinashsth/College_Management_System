@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Edit Course</h3>
        <a href="{{ route('courses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Courses
        </a>
    </div>

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('courses.update', $course) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="course_code" class="block text-gray-700 text-sm font-bold mb-2">Course Code</label>
                        <input type="text" name="course_code" id="course_code" class="form-input w-full @error('course_code') border-red-500 @enderror" value="{{ old('course_code', $course->course_code) }}" required>
                        @error('course_code')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="course_name" class="block text-gray-700 text-sm font-bold mb-2">Course Name</label>
                        <input type="text" name="course_name" id="course_name" class="form-input w-full @error('course_name') border-red-500 @enderror" value="{{ old('course_name', $course->course_name) }}" required>
                        @error('course_name')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="faculty_id" class="block text-gray-700 text-sm font-bold mb-2">Faculty</label>
                        <select name="faculty_id" id="faculty_id" class="form-select w-full @error('faculty_id') border-red-500 @enderror" required>
                            <option value="">Select Faculty</option>
                            @foreach($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ old('faculty_id', $course->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('faculty_id')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="duration" class="block text-gray-700 text-sm font-bold mb-2">Duration (Years)</label>
                        <input type="number" name="duration" id="duration" class="form-input w-full @error('duration') border-red-500 @enderror" value="{{ old('duration', $course->duration) }}" required min="0.5" max="6" step="0.5">
                        @error('duration')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                        <textarea name="description" id="description" class="form-textarea w-full @error('description') border-red-500 @enderror" rows="3">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                        <select name="status" id="status" class="form-select w-full @error('status') border-red-500 @enderror" required>
                            <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <button type="submit" class="bg-[#37a2bc] hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                            Update Course
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection