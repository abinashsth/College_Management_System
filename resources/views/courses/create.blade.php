@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Create New Course</h1>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('courses.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Courses
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded relative" role="alert">
            {{ session('error') }}
            <button type="button" class="absolute top-0 right-0 mt-4 mr-4" onclick="this.parentElement.remove()">
                <span class="text-red-700">&times;</span>
            </button>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <form method="POST" action="{{ route('courses.store') }}">
                @csrf
                
                <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Course Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                            Course Code <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}" required
                               class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('code') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Unique identifier for the course. Example: CS101, MATH201
                        </p>
                    </div>

                    <!-- Course Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Course Name <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Department <span class="text-red-600">*</span>
                        </label>
                        <select name="department_id" id="department_id" required
                                class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('department_id') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }} ({{ $department->code ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Credit Hours -->
                    <div>
                        <label for="credit_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Credit Hours <span class="text-red-600">*</span>
                        </label>
                        <input type="number" name="credit_hours" id="credit_hours" value="{{ old('credit_hours') }}" min="0" required
                               class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('credit_hours') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('credit_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Lecture Hours -->
                    <div>
                        <label for="lecture_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Lecture Hours
                        </label>
                        <input type="number" name="lecture_hours" id="lecture_hours" value="{{ old('lecture_hours') }}" min="0"
                               class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('lecture_hours') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('lecture_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lab Hours -->
                    <div>
                        <label for="lab_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Lab Hours
                        </label>
                        <input type="number" name="lab_hours" id="lab_hours" value="{{ old('lab_hours') }}" min="0"
                               class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('lab_hours') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('lab_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tutorial Hours -->
                    <div>
                        <label for="tutorial_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Tutorial Hours
                        </label>
                        <input type="number" name="tutorial_hours" id="tutorial_hours" value="{{ old('tutorial_hours') }}" min="0"
                               class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('tutorial_hours') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                        @error('tutorial_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Level -->
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">
                            Level
                        </label>
                        <select name="level" id="level"
                                class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('level') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="">Select Level</option>
                            <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                            <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                        </select>
                        @error('level')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                            Type
                        </label>
                        <select name="type" id="type"
                                class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="">Select Type</option>
                            <option value="mandatory" {{ old('type') == 'mandatory' ? 'selected' : '' }}>Mandatory</option>
                            <option value="elective" {{ old('type') == 'elective' ? 'selected' : '' }}>Elective</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                            Status
                        </label>
                        <select name="status" id="status"
                                class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        Description
                    </label>
                    <textarea name="description" id="description" rows="3"
                              class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Learning Outcomes -->
                <div class="mb-6">
                    <label for="learning_outcomes" class="block text-sm font-medium text-gray-700 mb-1">
                        Learning Outcomes
                    </label>
                    <textarea name="learning_outcomes" id="learning_outcomes" rows="3"
                              class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('learning_outcomes') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('learning_outcomes') }}</textarea>
                    @error('learning_outcomes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Evaluation Criteria -->
                <div class="mb-6">
                    <label for="evaluation_criteria" class="block text-sm font-medium text-gray-700 mb-1">
                        Evaluation Criteria
                    </label>
                    <textarea name="evaluation_criteria" id="evaluation_criteria" rows="3"
                              class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('evaluation_criteria') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('evaluation_criteria') }}</textarea>
                    @error('evaluation_criteria')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Syllabus -->
                <div class="mb-6">
                    <label for="syllabus" class="block text-sm font-medium text-gray-700 mb-1">
                        Syllabus
                    </label>
                    <textarea name="syllabus" id="syllabus" rows="4"
                              class="shadow-sm border-gray-300 rounded-md w-full py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('syllabus') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror">{{ old('syllabus') }}</textarea>
                    @error('syllabus')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-1"></i> Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 