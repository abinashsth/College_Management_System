@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Student Grades Management</h1>
        <a href="{{ route('dashboard') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h2 class="text-lg font-medium text-gray-900">Students List</h2>
        </div>
        <div class="p-4">
            <div class="mb-4">
                <input type="text" id="student-search" placeholder="Search by name or ID..." class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#37a2bc] focus:border-transparent">
            </div>
            
            @if($students->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Student ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Class
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($students as $student)
                                <tr class="student-row">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $student->student_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                @if($student->avatar)
                                                    <img src="{{ asset($student->avatar) }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-full">
                                                @else
                                                    <i class="fas fa-user text-gray-500"></i>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $student->class->class_name ?? 'Not Assigned' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $student->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $student->enrollment_status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($student->enrollment_status ?? 'Unknown') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('students.show', $student) }}" class="text-[#37a2bc] hover:text-[#2c8ca3] mr-3">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <button type="button" class="view-grades-btn text-indigo-600 hover:text-indigo-900" data-student-id="{{ $student->id }}" data-student-name="{{ $student->name }}">
                                            <i class="fas fa-chart-bar"></i> Grades
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center">
                    <p class="text-gray-500">No students found.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Student Grades Modal -->
    <div id="grades-modal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900" id="student-name-title">Student Grades</h3>
                <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-4 overflow-y-auto max-h-[calc(90vh-60px)]" id="grades-content">
                <div class="text-center py-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#37a2bc] mx-auto"></div>
                    <p class="mt-4 text-gray-600">Loading student grades...</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Student search functionality
        const searchInput = document.getElementById('student-search');
        const studentRows = document.querySelectorAll('.student-row');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            studentRows.forEach(row => {
                const studentId = row.querySelector('td:first-child').textContent.toLowerCase();
                const studentName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (studentId.includes(searchTerm) || studentName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Modal handling for student grades
        const modal = document.getElementById('grades-modal');
        const closeModal = document.getElementById('close-modal');
        const gradesContent = document.getElementById('grades-content');
        const studentNameTitle = document.getElementById('student-name-title');
        const viewGradesBtns = document.querySelectorAll('.view-grades-btn');
        
        function openModal(studentId, studentName) {
            modal.classList.remove('hidden');
            studentNameTitle.textContent = `${studentName}'s Grades`;
            
            // Fetch student grades via AJAX
            fetch(`/api/students/${studentId}/grades`)
                .then(response => response.json())
                .then(data => {
                    if (data.exams && data.exams.length > 0) {
                        let tableHtml = `
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">`;
                                
                        data.exams.forEach(exam => {
                            const isPassed = exam.grade >= exam.passing_marks;
                            tableHtml += `
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">${exam.title}</div>
                                        <div class="text-xs text-gray-500">${exam.exam_type}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${exam.subject_name || 'Multiple Subjects'}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">${exam.grade}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${exam.total_marks}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${isPassed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                            ${isPassed ? 'Passed' : 'Failed'}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${exam.remarks || 'No remarks'}</div>
                                    </td>
                                </tr>`;
                        });
                        
                        tableHtml += `</tbody></table>`;
                        gradesContent.innerHTML = tableHtml;
                    } else {
                        gradesContent.innerHTML = `<div class="text-center py-6"><p class="text-gray-500">No grades found for this student.</p></div>`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching grades:', error);
                    gradesContent.innerHTML = `<div class="text-center py-6"><p class="text-red-500">Error loading grades. Please try again later.</p></div>`;
                });
        }
        
        function closeModalHandler() {
            modal.classList.add('hidden');
            gradesContent.innerHTML = `
                <div class="text-center py-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-[#37a2bc] mx-auto"></div>
                    <p class="mt-4 text-gray-600">Loading student grades...</p>
                </div>`;
        }
        
        viewGradesBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.getAttribute('data-student-id');
                const studentName = this.getAttribute('data-student-name');
                openModal(studentId, studentName);
            });
        });
        
        closeModal.addEventListener('click', closeModalHandler);
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModalHandler();
            }
        });
        
        // Close modal with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModalHandler();
            }
        });
    });
</script>
@endpush
@endsection 