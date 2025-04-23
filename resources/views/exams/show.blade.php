@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">{{ $exam->title }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('exams.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
            @if(auth()->user()->checkPermission('edit exams'))
            <a href="{{ route('exams.edit', $exam) }}" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            @endif
        </div>
    </div>

    <!-- Status Bar -->
    <div class="bg-gray-100 p-4 rounded-lg mb-6 flex justify-between items-center">
        <div class="flex items-center space-x-4">
            <span class="px-3 py-1 rounded-full {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $exam->is_active ? 'Active' : 'Inactive' }}
            </span>
            <span class="px-3 py-1 rounded-full {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                {{ $exam->is_published ? 'Published' : 'Draft' }}
            </span>
        </div>
        <div class="text-sm text-gray-600">
            <span><i class="fas fa-calendar mr-1"></i> {{ $exam->exam_date->format('M d, Y') }}</span>
            <span class="ml-4"><i class="fas fa-clock mr-1"></i> {{ $exam->start_time->format('h:i A') }} - {{ $exam->end_time->format('h:i A') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Exam Information -->
        <div class="md:col-span-2 space-y-6">
            <!-- Basic Information Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Basic Information</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Class</h3>
                            <p class="mt-1">{{ $exam->class->class_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Academic Session</h3>
                            <p class="mt-1">{{ $exam->academicSession->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Exam Type</h3>
                            <p class="mt-1">{{ ucfirst($exam->exam_type) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Semester</h3>
                            <p class="mt-1">{{ $exam->semester ?? 'N/A' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Subjects</h3>
                            <div class="mt-1 flex flex-wrap gap-1">
                                @if($exam->subjects->count() > 0)
                                    @foreach($exam->subjects as $subject)
                                        <span class="inline-flex items-center rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10">
                                            {{ $subject->name }}
                                        </span>
                                    @endforeach
                                @else
                                    {{ $exam->subject->name ?? 'N/A' }}
                                @endif
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="mt-1">{{ $exam->description ?? 'No description provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Information Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Schedule Information</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Exam Date</h3>
                            <p class="mt-1">{{ $exam->exam_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                            <p class="mt-1">{{ $exam->duration_minutes }} minutes</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Start Time</h3>
                            <p class="mt-1">{{ $exam->start_time->format('h:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">End Time</h3>
                            <p class="mt-1">{{ $exam->end_time->format('h:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Location</h3>
                            <p class="mt-1">{{ $exam->location ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Room Number</h3>
                            <p class="mt-1">{{ $exam->room_number ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grading Information Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Grading Information</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Total Marks</h3>
                            <p class="mt-1">{{ $exam->total_marks }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Passing Marks</h3>
                            <p class="mt-1">{{ $exam->passing_marks }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Weight Percentage</h3>
                            <p class="mt-1">{{ $exam->weight_percentage ?? 'Not specified' }}%</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Grading Scale</h3>
                            <p class="mt-1">{{ $exam->grading_scale ?? 'Default' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Additional Information</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Registration Deadline</h3>
                            <p class="mt-1">{{ $exam->registration_deadline ? $exam->registration_deadline->format('F d, Y') : 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Result Date</h3>
                            <p class="mt-1">{{ $exam->result_date ? $exam->result_date->format('F d, Y') : 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created By</h3>
                            <p class="mt-1">{{ $exam->creator->name ?? 'Unknown' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated By</h3>
                            <p class="mt-1">{{ $exam->updater->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Exam Details</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Class</h3>
                            <p class="mt-1">{{ $exam->class->class_name ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Academic Session</h3>
                            <p class="mt-1">{{ $exam->academicSession->name ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Semester</h3>
                            <p class="mt-1">{{ $exam->semester ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Exam Type</h3>
                            <p class="mt-1">{{ ucfirst($exam->exam_type) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects & Grading Details -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Subjects & Grading</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 gap-4 mb-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Default Total Marks</h3>
                            <p class="mt-1">{{ $exam->total_marks }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Default Passing Marks</h3>
                            <p class="mt-1">{{ $exam->passing_marks }}</p>
                        </div>
                        @if($exam->weight_percentage)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Weight Percentage</h3>
                            <p class="mt-1">{{ $exam->weight_percentage }}%</p>
                        </div>
                        @endif
                        @if($exam->grading_scale)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Grading Scale</h3>
                            <p class="mt-1">{{ $exam->grading_scale }}</p>
                        </div>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <h3 class="text-sm font-medium text-gray-700 border-b pb-2 mb-3">Subject-specific Grading</h3>
                        @if($exam->subjects->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Marks</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Passing Marks</th>
                                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($exam->subjects as $subject)
                                            <tr>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $subject->pivot->total_marks }}</td>
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $subject->pivot->passing_marks }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $subject->pivot->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No subjects have been assigned to this exam.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Exam Schedule Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Exam Schedule</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Exam Date</h3>
                            <p class="mt-1">{{ $exam->exam_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                            <p class="mt-1">{{ $exam->duration_minutes }} minutes</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Start Time</h3>
                            <p class="mt-1">{{ $exam->start_time->format('h:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">End Time</h3>
                            <p class="mt-1">{{ $exam->end_time->format('h:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Location</h3>
                            <p class="mt-1">{{ $exam->location ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Room Number</h3>
                            <p class="mt-1">{{ $exam->room_number ?? 'Not specified' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Quick Links Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Quick Links</h2>
                </div>
                <div class="p-4">
                    <nav class="space-y-1">
                        @if(auth()->user()->checkPermission('manage exam schedules'))
                        <a href="{{ route('exam.schedules', $exam) }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-calendar-alt mr-3 text-gray-400"></i>
                            <span>Manage Schedules</span>
                            <span class="ml-auto bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $exam->schedules->count() }}</span>
                        </a>
                        @endif

                        @if(auth()->user()->checkPermission('manage exam rules'))
                        <a href="{{ route('exam.rules', $exam) }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-gavel mr-3 text-gray-400"></i>
                            <span>Manage Rules</span>
                            <span class="ml-auto bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $exam->rules->count() }}</span>
                        </a>
                        @endif

                        @if(auth()->user()->checkPermission('manage exam materials'))
                        <a href="{{ route('exam.materials', $exam) }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-file-pdf mr-3 text-gray-400"></i>
                            <span>Manage Materials</span>
                            <span class="ml-auto bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $exam->materials->count() }}</span>
                        </a>
                        @endif

                        @if(auth()->user()->checkPermission('edit exams'))
                        <form action="{{ route('exams.toggle-status', $exam) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                                <i class="fas {{ $exam->is_active ? 'fa-toggle-on text-green-500' : 'fa-toggle-off text-red-500' }} mr-3"></i>
                                <span>{{ $exam->is_active ? 'Deactivate Exam' : 'Activate Exam' }}</span>
                            </button>
                        </form>
                        
                        <form action="{{ route('exams.toggle-published', $exam) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                                <i class="fas {{ $exam->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-3 text-gray-400"></i>
                                <span>{{ $exam->is_published ? 'Unpublish Exam' : 'Publish Exam' }}</span>
                            </button>
                        </form>
                        @endif

                        <a href="#students-section" class="flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-user-graduate mr-3 text-gray-400"></i>
                            <span>Student Management</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Enrolled Students Card -->
            <div id="students-section" class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900">Enrolled Students</h2>
                    <span class="bg-blue-100 text-blue-800 py-0.5 px-2 rounded-full text-xs">{{ $enrolledStudents->count() }}</span>
                </div>
                <div class="p-4">
                    @if($enrolledStudents->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($enrolledStudents as $student)
                                <li class="py-2 flex items-center">
                                    <img src="{{ $student->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $student->name }}" class="w-8 h-8 rounded-full object-cover mr-3">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->student_id }}</div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4 text-gray-500">
                            <p>No students enrolled for this exam yet.</p>
                        </div>
                    @endif

                    @if(auth()->user()->checkPermission('edit exams'))
                        <div class="mt-4">
                            <button type="button" data-toggle="modal" data-target="#enrollStudentsModal" class="w-full flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#37a2bc] hover:bg-[#2c8ca3]">
                                <i class="fas fa-user-plus mr-2"></i> Enroll Students
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Enroll Students Modal -->
@if(auth()->user()->checkPermission('edit exams'))
<div class="modal fade" id="enrollStudentsModal" tabindex="-1" role="dialog" aria-labelledby="enrollStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollStudentsModalLabel">Enroll Students for {{ $exam->title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('exams.enroll-students', $exam) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Select students to enroll in this exam. Only students from {{ $exam->class->name }} are shown.</p>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all-students" class="form-checkbox h-4 w-4 text-[#37a2bc] transition duration-150 ease-in-out">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Student ID
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Name
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($students as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox form-checkbox h-4 w-4 text-[#37a2bc] transition duration-150 ease-in-out" {{ $enrolledStudents->contains($student) ? 'checked' : '' }}>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $student->student_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <img class="h-8 w-8 rounded-full" src="{{ $student->avatar ?? asset('images/default-avatar.png') }}" alt="{{ $student->name }}">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $enrolledStudents->contains($student) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $enrolledStudents->contains($student) ? 'Enrolled' : 'Not Enrolled' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary bg-[#37a2bc]">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All Checkbox Functionality
        const selectAllCheckbox = document.getElementById('select-all-students');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const studentCheckboxes = document.querySelectorAll('.student-checkbox');
                studentCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    });
</script>
@endpush 