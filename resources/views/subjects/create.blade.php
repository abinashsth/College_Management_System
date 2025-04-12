@extends('layouts.app')

@section('title', 'Create Subject')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Create New Subject</h1>
        <a href="{{ route('subjects.index') }}" class="mt-4 md:mt-0 px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
            <i class="fas fa-arrow-left mr-1"></i> Back to Subjects
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Subject Information</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf

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
                    <!-- Subject Code -->
                    <div class="md:col-span-4 mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Subject Code <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('code') border-red-500 @enderror" 
                            id="code" name="code" value="{{ old('code') }}" required placeholder="e.g., CS101">
                        @error('code')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Unique identifier for the subject.</p>
                    </div>

                    <!-- Subject Name -->
                    <div class="md:col-span-8 mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Subject Name <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" 
                            id="name" name="name" value="{{ old('name') }}" required placeholder="e.g., Introduction to Programming">
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
                            @foreach($academicStructureDepartments as $dept)
                                <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Credit Hours -->
                    <div class="md:col-span-3 mb-4">
                        <label for="credit_hours" class="block text-sm font-medium text-gray-700 mb-1">Credit Hours <span class="text-red-600">*</span></label>
                        <input type="number" step="0.5" min="0.5" max="10" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('credit_hours') border-red-500 @enderror" 
                            id="credit_hours" name="credit_hours" value="{{ old('credit_hours', 3) }}" required>
                        @error('credit_hours')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-3 mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('status') border-red-500 @enderror" 
                            id="status" name="status" required>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Level -->
                    <div class="mb-4">
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('level') border-red-500 @enderror" 
                            id="level" name="level" required>
                            <option value="">Select Level</option>
                            <option value="school" {{ old('level') == 'school' ? 'selected' : '' }}>School</option>
                            <option value="college" {{ old('level') == 'college' ? 'selected' : '' }}>College (+2)</option>
                            <option value="bachelor" {{ old('level') == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                            <option value="master" {{ old('level') == 'master' ? 'selected' : '' }}>Master</option>
                        </select>
                        @error('level')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration Type -->
                    <div class="mb-4">
                        <label for="duration_type" class="block text-sm font-medium text-gray-700 mb-1">Duration Type <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('duration_type') border-red-500 @enderror" 
                            id="duration_type" name="duration_type" required>
                            <option value="semester" {{ old('duration_type', 'semester') == 'semester' ? 'selected' : '' }}>Semester Based</option>
                            <option value="year" {{ old('duration_type') == 'year' ? 'selected' : '' }}>Year Based</option>
                        </select>
                        @error('duration_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Elective -->
                    <div class="mb-4">
                        <label for="elective" class="block text-sm font-medium text-gray-700 mb-1">Subject Type <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('elective') border-red-500 @enderror" 
                            id="elective" name="elective" required>
                            <option value="0" {{ old('elective') == '0' ? 'selected' : '' }}>Core (Required)</option>
                            <option value="1" {{ old('elective') == '1' ? 'selected' : '' }}>Elective (Optional)</option>
                        </select>
                        @error('elective')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Semester -->
                    <div class="mb-4" id="semester-container" {{ old('duration_type') == 'year' ? 'style=display:none' : '' }}>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('semester') border-red-500 @enderror" 
                            id="semester" name="semester">
                            <option value="">Select Semester</option>
                            <option value="first" {{ old('semester') == 'first' ? 'selected' : '' }}>First Semester</option>
                            <option value="second" {{ old('semester') == 'second' ? 'selected' : '' }}>Second Semester</option>
                            <option value="third" {{ old('semester') == 'third' ? 'selected' : '' }}>Third Semester</option>
                            <option value="fourth" {{ old('semester') == 'fourth' ? 'selected' : '' }}>Fourth Semester</option>
                            <option value="fifth" {{ old('semester') == 'fifth' ? 'selected' : '' }}>Fifth Semester</option>
                            <option value="sixth" {{ old('semester') == 'sixth' ? 'selected' : '' }}>Sixth Semester</option>
                            <option value="seventh" {{ old('semester') == 'seventh' ? 'selected' : '' }}>Seventh Semester</option>
                            <option value="eighth" {{ old('semester') == 'eighth' ? 'selected' : '' }}>Eighth Semester</option>
                            <option value="all" {{ old('semester') == 'all' ? 'selected' : '' }}>All Semesters</option>
                        </select>
                        @error('semester')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Year -->
                    <div class="mb-4" id="year-container" {{ old('duration_type') != 'year' ? 'style=display:none' : '' }}>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('year') border-red-500 @enderror" 
                            id="year" name="year">
                            <option value="">Select Year</option>
                            <option value="1" {{ old('year') == '1' ? 'selected' : '' }}>Year 1</option>
                            <option value="2" {{ old('year') == '2' ? 'selected' : '' }}>Year 2</option>
                            <option value="3" {{ old('year') == '3' ? 'selected' : '' }}>Year 3</option>
                            <option value="4" {{ old('year') == '4' ? 'selected' : '' }}>Year 4</option>
                        </select>
                        @error('year')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <!-- Teaching Hours -->
                    <h5 class="font-medium text-gray-700 mb-4">Teaching Hours</h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Lecture Hours -->
                        <div class="mb-4">
                            <label for="lecture_hours" class="block text-sm font-medium text-gray-700 mb-1">Lecture Hours</label>
                            <input type="number" step="0.5" min="0" max="10" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('lecture_hours') border-red-500 @enderror" 
                                id="lecture_hours" name="lecture_hours" value="{{ old('lecture_hours', 2) }}">
                            @error('lecture_hours')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Practical Hours -->
                        <div class="mb-4">
                            <label for="practical_hours" class="block text-sm font-medium text-gray-700 mb-1">Practical/Lab Hours</label>
                            <input type="number" step="0.5" min="0" max="10" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('practical_hours') border-red-500 @enderror" 
                                id="practical_hours" name="practical_hours" value="{{ old('practical_hours', 1) }}">
                            @error('practical_hours')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4 mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('description') border-red-500 @enderror" 
                        id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Learning Outcomes -->
                <div class="mb-4">
                    <label for="learning_outcomes" class="block text-sm font-medium text-gray-700 mb-1">Learning Outcomes</label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('learning_outcomes') border-red-500 @enderror" 
                        id="learning_outcomes" name="learning_outcomes" rows="4">{{ old('learning_outcomes') }}</textarea>
                    @error('learning_outcomes')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">What students will learn from this subject.</p>
                </div>

                <div class="border-t border-gray-200 mt-6 pt-6">
                    <!-- Programs -->
                    <div class="mb-4">
                        <label for="program_ids" class="block text-sm font-medium text-gray-700 mb-1">Programs</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('program_ids') border-red-500 @enderror" 
                            id="program_ids" name="program_ids[]" multiple>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ in_array($program->id, old('program_ids', [])) ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_ids')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Programs that will offer this subject.</p>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-1"></i> Save Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle between semester and year inputs
        const durationType = document.getElementById('duration_type');
        const semesterContainer = document.getElementById('semester-container');
        const yearContainer = document.getElementById('year-container');
        const semesterField = document.getElementById('semester');
        const yearField = document.getElementById('year');

        if (durationType) {
            durationType.addEventListener('change', function() {
                if (this.value === 'year') {
                    semesterContainer.style.display = 'none';
                    yearContainer.style.display = 'block';
                    semesterField.removeAttribute('required');
                    yearField.setAttribute('required', 'required');
                } else {
                    semesterContainer.style.display = 'block';
                    yearContainer.style.display = 'none';
                    yearField.removeAttribute('required');
                    semesterField.setAttribute('required', 'required');
                }
            });
            
            // Trigger the change event on page load to set the initial state
            const event = new Event('change');
            durationType.dispatchEvent(event);
        }
    });
</script>
@endpush 