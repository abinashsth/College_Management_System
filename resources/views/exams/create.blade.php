@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Create New Exam</h1>
        <a href="{{ route('exams.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Back to Exams
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
        <form action="{{ route('exams.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h2>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Exam Title <span class="text-red-600">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-1">Exam Type <span class="text-red-600">*</span></label>
                    <select name="exam_type" id="exam_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                        <option value="">-- Select Exam Type --</option>
                        @foreach($examTypes as $key => $value)
                            <option value="{{ $key }}" {{ old('exam_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('exam_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="academic_session_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Session <span class="text-red-600">*</span></label>
                    <select name="academic_session_id" id="academic_session_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                        <option value="">-- Select Academic Session --</option>
                        @foreach($academicSessions as $session)
                            <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_session_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <input type="text" name="semester" id="semester" value="{{ old('semester') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('semester')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-600">*</span></label>
                    <select name="class_id" id="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input type="hidden" name="subject_id" id="subject_id" value="{{ old('subject_id') }}">
                    <label for="subjects" class="block text-sm font-medium text-gray-700 mb-1">Subjects <span class="text-red-600">*</span></label>
                    <select name="subjects[]" id="subjects" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" multiple required>
                        <option disabled>-- Select Subjects --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ is_array(old('subjects')) && in_array($subject->id, old('subjects')) ? 'selected' : '' }}>{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">All subjects for the selected class will be automatically selected. You can manually adjust if needed.</p>
                    @error('subjects')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('subjects.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject-specific Grading Configuration -->
                <div class="md:col-span-2 mt-4">
                    <h3 class="text-md font-medium text-gray-800 border-b pb-1 mb-3">Subject-specific Grading</h3>
                    <p class="text-sm text-gray-600 mb-3">Configure individual grading settings for each selected subject. If left empty, the default exam marks will be used.</p>
                    
                    <div id="subject-grades-container" class="space-y-4">
                        <!-- Subject-specific grade settings will be added here dynamically via JavaScript -->
                        <div class="text-sm text-gray-500 italic" id="no-subjects-message">
                            Select subjects above to configure subject-specific grading
                        </div>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Exam Schedule -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Exam Schedule</h2>
                </div>

                <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">Exam Date <span class="text-red-600">*</span></label>
                    <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', $defaults['exam_date']) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('exam_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) <span class="text-red-600">*</span></label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $defaults['duration_minutes']) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-600">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $defaults['start_time']) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-600">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $defaults['end_time']) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                    <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('room_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Grading Information -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Grading Information</h2>
                </div>

                <div>
                    <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-1">Total Marks <span class="text-red-600">*</span></label>
                    <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $defaults['total_marks']) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('total_marks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="passing_marks" class="block text-sm font-medium text-gray-700 mb-1">Passing Marks <span class="text-red-600">*</span></label>
                    <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $defaults['passing_marks']) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('passing_marks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="weight_percentage" class="block text-sm font-medium text-gray-700 mb-1">Weight Percentage</label>
                    <input type="number" name="weight_percentage" id="weight_percentage" value="{{ old('weight_percentage') }}" min="0" max="100" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('weight_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grading_scale" class="block text-sm font-medium text-gray-700 mb-1">Grading Scale</label>
                    <input type="text" name="grading_scale" id="grading_scale" value="{{ old('grading_scale') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('grading_scale')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Information -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Additional Information</h2>
                </div>

                <div>
                    <label for="registration_deadline" class="block text-sm font-medium text-gray-700 mb-1">Registration Deadline</label>
                    <input type="date" name="registration_deadline" id="registration_deadline" value="{{ old('registration_deadline') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('registration_deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="result_date" class="block text-sm font-medium text-gray-700 mb-1">Result Date</label>
                    <input type="date" name="result_date" id="result_date" value="{{ old('result_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('result_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="reset" class="bg-gray-200 text-gray-700 py-2 px-4 rounded mr-2 hover:bg-gray-300 transition-colors">
                    <i class="fas fa-redo mr-1"></i> Reset
                </button>
                <button type="submit" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
                    <i class="fas fa-save mr-1"></i> Create Exam
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto calculate end time based on start time and duration
        const startTimeInput = document.getElementById('start_time');
        const durationInput = document.getElementById('duration_minutes');
        const endTimeInput = document.getElementById('end_time');
        
        function updateEndTime() {
            if (startTimeInput.value) {
                const startTime = new Date(`2000-01-01T${startTimeInput.value}`);
                const durationMinutes = parseInt(durationInput.value) || 0;
                
                startTime.setMinutes(startTime.getMinutes() + durationMinutes);
                
                const hours = startTime.getHours().toString().padStart(2, '0');
                const minutes = startTime.getMinutes().toString().padStart(2, '0');
                
                endTimeInput.value = `${hours}:${minutes}`;
            }
        }
        
        startTimeInput.addEventListener('change', updateEndTime);
        durationInput.addEventListener('input', updateEndTime);
        
        // Get all necessary DOM elements
        const classSelect = document.getElementById('class_id');
        const subjectsSelect = document.getElementById('subjects');
        const subjectIdInput = document.getElementById('subject_id');
        const examTypeSelect = document.getElementById('exam_type');
        const totalMarksInput = document.getElementById('total_marks');
        const passingMarksInput = document.getElementById('passing_marks');
        const subjectGradesContainer = document.getElementById('subject-grades-container');
        const noSubjectsMessage = document.getElementById('no-subjects-message');
        const form = document.querySelector('form');
        
        // Create a mapping of class IDs to subject IDs
        const classSubjectsMap = {
            @foreach($classes as $class)
                {{ $class->id }}: [
                    @foreach($class->subjects as $subject)
                        {{ $subject->id }},
                    @endforeach
                ],
            @endforeach
        };

        // Create a mapping of subject IDs to names
        const subjectNamesMap = {
            @foreach($subjects as $subject)
                {{ $subject->id }}: "{{ $subject->name }}",
            @endforeach
        };
        
        // Function to update subject selection based on class selection
        function updateSubjects() {
            const classId = classSelect.value;
            
            if (classId) {
                const classSubjects = classSubjectsMap[classId] || [];
                
                // Clear current selections
                for (let i = 0; i < subjectsSelect.options.length; i++) {
                    subjectsSelect.options[i].selected = false;
                    // Reset display for all options first
                    subjectsSelect.options[i].style.display = '';
                }
                
                // Select all subjects for the selected class and hide irrelevant options
                for (let i = 0; i < subjectsSelect.options.length; i++) {
                    const option = subjectsSelect.options[i];
                    
                    if (option.disabled) continue;
                    
                    const subjectId = parseInt(option.value);
                    
                    // Check if this subject belongs to the selected class
                    if (classSubjects.includes(subjectId)) {
                        option.selected = true;
                    } else {
                        option.style.display = 'none';
                    }
                }
                
                // If there's only one subject, set it as the main subject_id
                if (classSubjects.length === 1) {
                    subjectIdInput.value = classSubjects[0];
                } else {
                    subjectIdInput.value = '';
                }

                // Update subject-specific grading fields
                updateSubjectGradingFields();
            }
        }

        // Function to update the subject-specific grading fields
        function updateSubjectGradingFields() {
            // Get the selected subjects
            const selectedSubjects = Array.from(subjectsSelect.selectedOptions).map(option => ({
                id: option.value,
                name: option.text
            }));

            // Clear the container except for the no subjects message
            const children = Array.from(subjectGradesContainer.children);
            for (const child of children) {
                if (child.id !== 'no-subjects-message') {
                    subjectGradesContainer.removeChild(child);
                }
            }

            // Show/hide the no subjects message
            noSubjectsMessage.style.display = selectedSubjects.length > 0 ? 'none' : 'block';

            // Add a new row for each selected subject
            selectedSubjects.forEach(subject => {
                const subjectRow = document.createElement('div');
                subjectRow.className = 'border rounded p-3 bg-gray-50';
                subjectRow.dataset.subjectId = subject.id;
                
                const defaultTotalMarks = totalMarksInput.value || {{ $defaults['total_marks'] }};
                const defaultPassingMarks = passingMarksInput.value || {{ $defaults['passing_marks'] }};

                subjectRow.innerHTML = `
                    <div class="font-medium text-gray-800 mb-2">${subject.name}</div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="subject_total_marks_${subject.id}" class="block text-sm font-medium text-gray-700 mb-1">Total Marks</label>
                            <input type="number" name="subject_total_marks[${subject.id}]" id="subject_total_marks_${subject.id}" 
                                value="${defaultTotalMarks}" min="1" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="subject_passing_marks_${subject.id}" class="block text-sm font-medium text-gray-700 mb-1">Passing Marks</label>
                            <input type="number" name="subject_passing_marks[${subject.id}]" id="subject_passing_marks_${subject.id}" 
                                value="${defaultPassingMarks}" min="1" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                        </div>
                    </div>
                    <div class="mt-2">
                        <label for="subject_notes_${subject.id}" class="block text-sm font-medium text-gray-700 mb-1">Notes for this subject</label>
                        <textarea name="subject_notes[${subject.id}]" id="subject_notes_${subject.id}" rows="2" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50"></textarea>
                    </div>
                `;
                
                subjectGradesContainer.appendChild(subjectRow);
            });
        }
        
        // Update subject fields when global marks change
        totalMarksInput.addEventListener('change', function() {
            // Update all subject total marks with the new default value
            document.querySelectorAll('[id^="subject_total_marks_"]').forEach(input => {
                input.value = totalMarksInput.value;
            });
        });
        
        passingMarksInput.addEventListener('change', function() {
            // Update all subject passing marks with the new default value
            document.querySelectorAll('[id^="subject_passing_marks_"]').forEach(input => {
                input.value = passingMarksInput.value;
            });
        });
        
        // Event listeners for various form elements
        classSelect.addEventListener('change', updateSubjects);
        subjectsSelect.addEventListener('change', updateSubjectGradingFields);
        examTypeSelect.addEventListener('change', updateSubjectGradingFields);
        
        // Form submission validation
        form.addEventListener('submit', function(e) {
            // Validate required fields
            const requiredFields = [
                { id: 'title', name: 'Exam Title' },
                { id: 'exam_type', name: 'Exam Type' },
                { id: 'academic_session_id', name: 'Academic Session' },
                { id: 'class_id', name: 'Class' },
                { id: 'exam_date', name: 'Exam Date' },
                { id: 'start_time', name: 'Start Time' },
                { id: 'end_time', name: 'End Time' },
                { id: 'total_marks', name: 'Total Marks' },
                { id: 'passing_marks', name: 'Passing Marks' }
            ];
            
            let missingFields = [];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element.value) {
                    missingFields.push(field.name);
                }
            });
            
            // Check for subjects
            const selectedSubjects = Array.from(subjectsSelect.selectedOptions);
            if (selectedSubjects.length === 0) {
                missingFields.push('At least one Subject');
            }
            
            // If any required field is missing, prevent form submission
            if (missingFields.length > 0) {
                e.preventDefault();
                alert('Please fill in the following required fields: ' + missingFields.join(', '));
                return false;
            }
            
            // Validate that passing marks are less than or equal to total marks
            const totalMarks = parseInt(totalMarksInput.value);
            const passingMarks = parseInt(passingMarksInput.value);
            
            if (passingMarks > totalMarks) {
                e.preventDefault();
                alert('Passing marks cannot be greater than total marks.');
                return false;
            }
            
            return true;
        });
        
        // Initialize on page load
        if (classSelect.value) {
            updateSubjects();
        } else {
            // Initialize subject-specific grading fields
            updateSubjectGradingFields();
        }
        
        // Initialize end time calculation 
        updateEndTime();
    });
</script>
@endpush 