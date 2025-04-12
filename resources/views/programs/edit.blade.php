@extends('layouts.app')

@section('title', 'Edit Program - ' . $program->name)

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Program: {{ $program->name }}</h1>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('programs.show', $program) }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Program
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded relative" role="alert">
            {{ session('success') }}
            <button type="button" class="absolute top-0 right-0 mt-4 mr-4" onclick="this.parentElement.remove()">
                <span class="text-green-700">&times;</span>
            </button>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Program Information</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('programs.update', $program) }}" method="POST">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Program Code -->
                    <div class="md:col-span-4 mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Program Code <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('code') border-red-500 @enderror" 
                            id="code" name="code" value="{{ old('code', $program->code) }}" required>
                        @error('code')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Unique identifier for the program.</p>
                    </div>

                    <!-- Program Name -->
                    <div class="md:col-span-8 mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Program Name <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" 
                            id="name" name="name" value="{{ old('name', $program->name) }}" required>
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <!-- Department -->
                    <div class="md:col-span-6 mb-4">
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('department_id') border-red-500 @enderror" 
                            id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments ?? [] as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $program->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Program Coordinator -->
                    <div class="md:col-span-6 mb-4">
                        <label for="coordinator_id" class="block text-sm font-medium text-gray-700 mb-1">Program Coordinator</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('coordinator_id') border-red-500 @enderror" 
                            id="coordinator_id" name="coordinator_id">
                            <option value="">Select Coordinator (Optional)</option>
                            @foreach($coordinators ?? [] as $coordinator)
                                <option value="{{ $coordinator->id }}" {{ old('coordinator_id', $program->coordinator_id) == $coordinator->id ? 'selected' : '' }}>
                                    {{ $coordinator->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('coordinator_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- Duration -->
                    <div class="mb-4">
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration <span class="text-red-600">*</span></label>
                        <input type="number" step="1" min="1" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('duration') border-red-500 @enderror" 
                            id="duration" name="duration" value="{{ old('duration', $program->duration) }}" required>
                        @error('duration')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration Unit -->
                    <div class="mb-4">
                        <label for="duration_unit" class="block text-sm font-medium text-gray-700 mb-1">Duration Unit <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('duration_unit') border-red-500 @enderror" 
                            id="duration_unit" name="duration_unit" required>
                            <option value="years" {{ old('duration_unit', strtolower($program->duration_unit)) == 'years' ? 'selected' : '' }}>Years</option>
                            <option value="semesters" {{ old('duration_unit', strtolower($program->duration_unit)) == 'semesters' ? 'selected' : '' }}>Semesters</option>
                        </select>
                        @error('duration_unit')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Credit Hours -->
                    <div class="mb-4">
                        <label for="credit_hours" class="block text-sm font-medium text-gray-700 mb-1">Total Credit Hours <span class="text-red-600">*</span></label>
                        <input type="number" step="1" min="0" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('credit_hours') border-red-500 @enderror" 
                            id="credit_hours" name="credit_hours" value="{{ old('credit_hours', $program->credit_hours) }}" required>
                        @error('credit_hours')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Degree Level -->
                    <div class="mb-4">
                        <label for="degree_level" class="block text-sm font-medium text-gray-700 mb-1">Degree Level <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('degree_level') border-red-500 @enderror" 
                            id="degree_level" name="degree_level" required>
                            <option value="">Select Degree Level</option>
                            <option value="School" {{ old('degree_level', $program->degree_level) == 'School' ? 'selected' : '' }}>School</option>
                            <option value="College (+2)" {{ old('degree_level', $program->degree_level) == 'College (+2)' ? 'selected' : '' }}>College (+2)</option>
                            <option value="Bachelor" {{ old('degree_level', $program->degree_level) == 'Bachelor' ? 'selected' : '' }}>Bachelor</option>
                            <option value="Master" {{ old('degree_level', $program->degree_level) == 'Master' ? 'selected' : '' }}>Master</option>
                        </select>
                        @error('degree_level')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Status -->
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('status') border-red-500 @enderror" 
                            id="status" name="status" required>
                            <option value="1" {{ old('status', $program->status) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $program->status) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tuition Fee -->
                    <div class="mb-4">
                        <label for="tuition_fee" class="block text-sm font-medium text-gray-700 mb-1">Tuition Fee</label>
                        <input type="number" step="0.01" min="0" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('tuition_fee') border-red-500 @enderror" 
                            id="tuition_fee" name="tuition_fee" value="{{ old('tuition_fee', $program->tuition_fee) }}">
                        @error('tuition_fee')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Max Students -->
                    <div class="mb-4">
                        <label for="max_students" class="block text-sm font-medium text-gray-700 mb-1">Max Students</label>
                        <input type="number" step="1" min="0" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('max_students') border-red-500 @enderror" 
                            id="max_students" name="max_students" value="{{ old('max_students', $program->max_students) }}">
                        @error('max_students')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Start Date -->
                    <div class="mb-4">
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('start_date') border-red-500 @enderror" 
                            id="start_date" name="start_date" value="{{ old('start_date', $program->start_date ? $program->start_date->format('Y-m-d') : '') }}">
                        @error('start_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div class="mb-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('end_date') border-red-500 @enderror" 
                            id="end_date" name="end_date" value="{{ old('end_date', $program->end_date ? $program->end_date->format('Y-m-d') : '') }}">
                        @error('end_date')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror" 
                        id="description" name="description" rows="3">{{ old('description', $program->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Admission Requirements -->
                <div class="mb-4">
                    <label for="admission_requirements" class="block text-sm font-medium text-gray-700 mb-1">Admission Requirements</label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('admission_requirements') border-red-500 @enderror" 
                        id="admission_requirements" name="admission_requirements" rows="3">{{ old('admission_requirements', $program->admission_requirements) }}</textarea>
                    @error('admission_requirements')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Curriculum -->
                <div class="mb-4">
                    <label for="curriculum" class="block text-sm font-medium text-gray-700 mb-1">Curriculum</label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('curriculum') border-red-500 @enderror" 
                        id="curriculum" name="curriculum" rows="3">{{ old('curriculum', $program->curriculum) }}</textarea>
                    @error('curriculum')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 mt-6 pt-6 flex justify-end">
                    <a href="{{ route('programs.show', $program) }}" class="px-6 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition mr-2">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition">
                        <i class="fas fa-save mr-1"></i> Update Program
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
