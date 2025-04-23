@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Edit Exam: {{ $exam->title }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('exams.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to Exams
            </a>
            <a href="{{ route('exams.show', $exam) }}" class="bg-blue-100 text-blue-700 py-2 px-4 rounded hover:bg-blue-200 transition-colors">
                <i class="fas fa-eye mr-1"></i> View Exam
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden p-6">
        <form action="{{ route('exams.update', $exam) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="md:col-span-2">
                    <h2 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h2>
                </div>

                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Exam Title <span class="text-red-600">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $exam->title) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-1">Exam Type <span class="text-red-600">*</span></label>
                    <select name="exam_type" id="exam_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                        <option value="">-- Select Exam Type --</option>
                        @foreach($examTypes as $key => $value)
                            <option value="{{ $key }}" {{ old('exam_type', $exam->exam_type) == $key ? 'selected' : '' }}>{{ $value }}</option>
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
                            <option value="{{ $session->id }}" {{ old('academic_session_id', $exam->academic_session_id) == $session->id ? 'selected' : '' }}>{{ $session->name }}</option>
                        @endforeach
                    </select>
                    @error('academic_session_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                    <input type="text" name="semester" id="semester" value="{{ old('semester', $exam->semester) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('semester')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-600">*</span></label>
                    <select name="class_id" id="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input type="hidden" name="subject_id" id="subject_id" value="{{ old('subject_id', $exam->subject_id) }}">
                    <label for="subjects" class="block text-sm font-medium text-gray-700 mb-1">Subjects <span class="text-red-600">*</span></label>
                    <select name="subjects[]" id="subjects" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" multiple required>
                        <option disabled>-- Select Subjects --</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" 
                                {{ is_array(old('subjects')) 
                                    ? (in_array($subject->id, old('subjects')) ? 'selected' : '') 
                                    : ($exam->subjects->contains($subject->id) ? 'selected' : '') 
                                }}>
                                {{ $subject->name }}
                            </option>
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
                    <textarea name="description" id="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">{{ old('description', $exam->description) }}</textarea>
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
                    <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('exam_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) <span class="text-red-600">*</span></label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-600">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('H:i') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('start_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-600">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('H:i') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('end_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" id="location" value="{{ old('location', $exam->location) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                    <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $exam->room_number) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
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
                    <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('total_marks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="passing_marks" class="block text-sm font-medium text-gray-700 mb-1">Passing Marks <span class="text-red-600">*</span></label>
                    <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->passing_marks) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('passing_marks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="weight_percentage" class="block text-sm font-medium text-gray-700 mb-1">Weight Percentage</label>
                    <input type="number" name="weight_percentage" id="weight_percentage" value="{{ old('weight_percentage', $exam->weight_percentage) }}" min="0" max="100" step="0.01" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('weight_percentage')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="grading_scale" class="block text-sm font-medium text-gray-700 mb-1">Grading Scale</label>
                    <input type="text" name="grading_scale" id="grading_scale" value="{{ old('grading_scale', $exam->grading_scale) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
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
                    <input type="date" name="registration_deadline" id="registration_deadline" value="{{ old('registration_deadline', $exam->registration_deadline ? $exam->registration_deadline->format('Y-m-d') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('registration_deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="result_date" class="block text-sm font-medium text-gray-700 mb-1">Result Date</label>
                    <input type="date" name="result_date" id="result_date" value="{{ old('result_date', $exam->result_date ? $exam->result_date->format('Y-m-d') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('result_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-[#37a2bc] shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" {{ old('is_active', $exam->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-700">Active</label>
                    </div>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center mt-4">
                        <input type="checkbox" name="is_published" id="is_published" value="1" class="rounded border-gray-300 text-[#37a2bc] shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" {{ old('is_published', $exam->is_published) ? 'checked' : '' }}>
                        <label for="is_published" class="ml-2 block text-sm text-gray-700">Published</label>
                    </div>
                    @error('is_published')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('exams.show', $exam) }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded mr-2 hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times mr-1"></i> Cancel
                </a>
                <button type="submit" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
                    <i class="fas fa-save mr-1"></i> Update Exam
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
        
        // Auto-select all subjects for a class
        const classSelect = document.getElementById('class_id');
        const subjectsSelect = document.getElementById('subjects');
        const examTypeSelect = document.getElementById('exam_type');
        const totalMarksInput = document.getElementById('total_marks');
        const passingMarksInput = document.getElementById('passing_marks');
        const subjectGradesContainer = document.getElementById('subject-grades-container');
        const noSubjectsMessage = document.getElementById('no-subjects-message');
        
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

        // Create a mapping of exam subject data (for existing values)
        const examSubjectData = {
            @foreach($exam->subjects as $subject)
                {{ $subject->id }}: {
                    totalMarks: {{ $subject->pivot->total_marks }},
                    passingMarks: {{ $subject->pivot->passing_marks }},
                    notes: "{{ addslashes($subject->pivot->notes ?? '') }}"
                },
            @endforeach
        };
        
        function updateSubjects() {
            const classId = classSelect.value;
            
            if (classId) {
                const classSubjects = classSubjectsMap[classId] || [];
                
                // Always select all subjects for the selected class, regardless of exam type
                for (let i = 0; i < subjectsSelect.options.length; i++) {
                    const option = subjectsSelect.options[i];
                    
                    if (option.disabled) continue;
                    
                    // Check if this subject belongs to the selected class
                    option.selected = classSubjects.includes(parseInt(option.value));
                }
                
                // Filter subjects to show only those available for the selected class
                for (let i = 0; i < subjectsSelect.options.length; i++) {
                    const option = subjectsSelect.options[i];
                    
                    if (option.disabled) continue;
                    
                    // Show/hide based on whether subject is in class or not
                    option.style.display = classSubjects.includes(parseInt(option.value)) ? '' : 'none';
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
                
                // Get values from existing data or use defaults
                const existingData = examSubjectData[subject.id] || {};
                const defaultTotalMarks = existingData.totalMarks || totalMarksInput.value || 100;
                const defaultPassingMarks = existingData.passingMarks || passingMarksInput.value || 40;
                const existingNotes = existingData.notes || '';

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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">${existingNotes}</textarea>
                    </div>
                `;
                
                subjectGradesContainer.appendChild(subjectRow);
            });
        }
        
        // Event listeners for changes to subjects and defaults
        subjectsSelect.addEventListener('change', updateSubjectGradingFields);
        totalMarksInput.addEventListener('change', function() {
            // Update all subject total marks with the new default value
            document.querySelectorAll('[id^="subject_total_marks_"]').forEach(input => {
                const subjectId = input.id.split('_').pop();
                // Only update if user hasn't set a custom value
                if (!examSubjectData[subjectId]) {
                    input.value = totalMarksInput.value;
                }
            });
        });
        
        passingMarksInput.addEventListener('change', function() {
            // Update all subject passing marks with the new default value
            document.querySelectorAll('[id^="subject_passing_marks_"]').forEach(input => {
                const subjectId = input.id.split('_').pop();
                // Only update if user hasn't set a custom value
                if (!examSubjectData[subjectId]) {
                    input.value = passingMarksInput.value;
                }
            });
        });
        
        classSelect.addEventListener('change', updateSubjects);
        examTypeSelect.addEventListener('change', updateSubjectGradingFields);
        
        // Initialize on page load
        if (classSelect.value) {
            updateSubjects();
        } else {
            updateSubjectGradingFields(); // Load existing subject data even if no class selected
        }
    });
</script>
@endpush 