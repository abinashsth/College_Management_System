@extends('layouts.app')

@section('title', 'Edit Subject')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Subject: {{ $subject->code }} - {{ $subject->name }}</h1>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('subjects.show', $subject) }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <i class="fas fa-eye mr-1"></i> View Subject
            </a>
            <a href="{{ route('subjects.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Subjects
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
            <h6 class="font-bold text-blue-600">Subject Information</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('subjects.update', $subject) }}" method="POST">
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
                    <!-- Subject Code -->
                    <div class="md:col-span-4 mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Subject Code <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('code') border-red-500 @enderror" 
                            id="code" name="code" value="{{ old('code', $subject->code) }}" required placeholder="e.g., CS101">
                        @error('code')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Unique identifier for the subject.</p>
                    </div>

                    <!-- Subject Name -->
                    <div class="md:col-span-8 mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Subject Name <span class="text-red-600">*</span></label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('name') border-red-500 @enderror" 
                            id="name" name="name" value="{{ old('name', $subject->name) }}" required placeholder="e.g., Introduction to Programming">
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
                            @foreach($academicStructureDepartments as $department)
                                <option value="{{ $department->id }}" {{ (old('department_id', $subject->department_id) == $department->id) ? 'selected' : '' }}>
                                    {{ $department->name }}
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
                            id="credit_hours" name="credit_hours" value="{{ old('credit_hours', $subject->credit_hours) }}" required>
                        @error('credit_hours')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-3 mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('status') border-red-500 @enderror" 
                            id="status" name="status" required>
                            <option value="active" {{ old('status', $subject->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $subject->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <option value="school" {{ old('level', $subject->level) == 'school' ? 'selected' : '' }}>School</option>
                            <option value="college" {{ old('level', $subject->level) == 'college' ? 'selected' : '' }}>College (+2)</option>
                            <option value="bachelor" {{ old('level', $subject->level) == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                            <option value="master" {{ old('level', $subject->level) == 'master' ? 'selected' : '' }}>Master</option>
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
                            <option value="0" {{ old('elective', $subject->elective) == '0' ? 'selected' : '' }}>Core (Required)</option>
                            <option value="1" {{ old('elective', $subject->elective) == '1' ? 'selected' : '' }}>Elective (Optional)</option>
                        </select>
                        @error('elective')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Semester -->
                    <div class="mb-4" id="semester-container" {{ old('duration_type') == 'year' ? 'style=display:none' : '' }}>
                        <label for="semester_offered" class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-600">*</span></label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('semester_offered') border-red-500 @enderror" 
                            id="semester_offered" name="semester_offered">
                            <option value="">Select Semester</option>
                            <option value="first" {{ old('semester_offered', $subject->semester_offered) == 'first' ? 'selected' : '' }}>First Semester</option>
                            <option value="second" {{ old('semester_offered', $subject->semester_offered) == 'second' ? 'selected' : '' }}>Second Semester</option>
                            <option value="third" {{ old('semester_offered', $subject->semester_offered) == 'third' ? 'selected' : '' }}>Third Semester</option>
                            <option value="fourth" {{ old('semester_offered', $subject->semester_offered) == 'fourth' ? 'selected' : '' }}>Fourth Semester</option>
                            <option value="fifth" {{ old('semester_offered', $subject->semester_offered) == 'fifth' ? 'selected' : '' }}>Fifth Semester</option>
                            <option value="sixth" {{ old('semester_offered', $subject->semester_offered) == 'sixth' ? 'selected' : '' }}>Sixth Semester</option>
                            <option value="seventh" {{ old('semester_offered', $subject->semester_offered) == 'seventh' ? 'selected' : '' }}>Seventh Semester</option>
                            <option value="eighth" {{ old('semester_offered', $subject->semester_offered) == 'eighth' ? 'selected' : '' }}>Eighth Semester</option>
                            <option value="all" {{ old('semester_offered', $subject->semester_offered) == 'all' ? 'selected' : '' }}>All Semesters</option>
                        </select>
                        @error('semester_offered')
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
                                id="lecture_hours" name="lecture_hours" value="{{ old('lecture_hours', $subject->lecture_hours) }}">
                            @error('lecture_hours')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Practical Hours -->
                        <div class="mb-4">
                            <label for="practical_hours" class="block text-sm font-medium text-gray-700 mb-1">Practical/Lab Hours</label>
                            <input type="number" step="0.5" min="0" max="10" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('practical_hours') border-red-500 @enderror" 
                                id="practical_hours" name="practical_hours" value="{{ old('practical_hours', $subject->practical_hours) }}">
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
                        id="description" name="description" rows="3">{{ old('description', $subject->description) }}</textarea>
                    @error('description')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Learning Outcomes -->
                <div class="mb-4">
                    <label for="learning_objectives" class="block text-sm font-medium text-gray-700 mb-1">Learning Outcomes</label>
                    <textarea class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('learning_objectives') border-red-500 @enderror" 
                        id="learning_objectives" name="learning_objectives" rows="4">{{ old('learning_objectives', $subject->learning_objectives) }}</textarea>
                    @error('learning_objectives')
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
                                <option value="{{ $program->id }}" {{ in_array($program->id, old('program_ids', $subjectPrograms)) ? 'selected' : '' }}>
                                    {{ $program->name }} ({{ optional($program->department)->name ?? 'N/A' }})
                                </option>
                            @endforeach
                        </select>
                        @error('program_ids')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-gray-500 text-sm mt-1">Programs that will offer this subject.</p>
                    </div>
                </div>

                <!-- Prerequisites -->
                <div class="mb-4">
                    <label for="prerequisite_ids" class="block text-sm font-medium text-gray-700 mb-1">Prerequisites</label>
                    <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 @error('prerequisite_ids') border-red-500 @enderror" 
                        id="prerequisite_ids" name="prerequisite_ids[]" multiple>
                        @foreach($subjects as $prerequisiteSubject)
                            @if($prerequisiteSubject->id != $subject->id)
                                <option value="{{ $prerequisiteSubject->id }}" {{ in_array($prerequisiteSubject->id, old('prerequisite_ids', $prerequisites)) ? 'selected' : '' }}>
                                    {{ $prerequisiteSubject->code }} - {{ $prerequisiteSubject->name }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('prerequisite_ids')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-gray-500 text-sm mt-1">Select subjects that must be completed before taking this one.</p>
                </div>

                <div class="flex justify-end mt-6">
                    <a href="{{ route('subjects.index') }}" class="px-6 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-save mr-1"></i> Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const durationType = document.getElementById('duration_type');
    const semesterContainer = document.getElementById('semester-container');
    const yearContainer = document.getElementById('year-container');
    
    durationType.addEventListener('change', function() {
        if (this.value === 'year') {
            semesterContainer.style.display = 'none';
            yearContainer.style.display = 'block';
            document.getElementById('semester_offered').value = '';
        } else {
            semesterContainer.style.display = 'block';
            yearContainer.style.display = 'none';
            document.getElementById('year').value = '';
        }
    });
    
    // For multi-selects, consider adding a library like Select2
    // Or using vanilla JS to improve UX
});
</script>
@endsection 