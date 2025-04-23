<x-app-layout>
    <x-slot name="title">
        Marks Overview
    </x-slot>
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Marks Overview') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif
                    
                    @if(isset($exam) && isset($subject))
                        <!-- Exam and Subject are provided, show marks -->
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold">Marks for {{ $exam->title }}</h3>
                                <p class="text-sm text-gray-600">Subject: {{ $subject->name }} ({{ $subject->code }})</p>
                                <p class="text-sm text-gray-600">Total Marks: {{ $exam->total_marks }} | Passing Marks: {{ $exam->passing_marks }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('marks.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">
                                    Back to Overview
                                </a>
                                <a href="{{ route('marks.create', ['exam_id' => $exam->id, 'subject_id' => $subject->id]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">
                                    Edit Marks
                                </a>
                                <a href="{{ route('marks.export', ['exam_id' => $exam->id, 'subject_id' => $subject->id, 'format' => 'xlsx']) }}" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                                    Export to Excel
                                </a>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Marks (Max: {{ $exam->total_marks }})
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($students as $student)
                                        <tr class="{{ isset($marks[$student->id]) && $marks[$student->id]->isPassing() ? 'bg-green-50' : (isset($marks[$student->id]) ? 'bg-red-50' : '') }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $student->roll_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $student->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if(isset($marks[$student->id]))
                                                    @if($marks[$student->id]->is_absent)
                                                        <span class="text-gray-500">Absent</span>
                                                    @else
                                                        {{ $marks[$student->id]->marks_obtained }} / {{ $exam->total_marks }}
                                                        ({{ number_format(($marks[$student->id]->marks_obtained / $exam->total_marks) * 100, 1) }}%)
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">Not entered</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ isset($marks[$student->id]) ? ($marks[$student->id]->grade ?? 'N/A') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if(isset($marks[$student->id]))
                                                    @if($marks[$student->id]->is_absent)
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Absent</span>
                                                    @elseif($marks[$student->id]->isPassing())
                                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Passed</span>
                                                    @else
                                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Failed</span>
                                                    @endif
                                                @else
                                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ isset($marks[$student->id]) ? ($marks[$student->id]->remarks ?? '-') : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                No students found for this class.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <!-- No exam and subject provided, show overview -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold mb-4">Marks Overview</h3>
                            
                            <!-- Statistics Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                <!-- Total Marks Card -->
                                <div class="bg-white border rounded-lg shadow-sm p-4 flex items-center">
                                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm">Total Marks</p>
                                        <p class="text-xl font-semibold">{{ number_format($stats['total']) }}</p>
                                    </div>
                                </div>

                                <!-- Pending Verification Card -->
                                <div class="bg-white border rounded-lg shadow-sm p-4 flex items-center">
                                    <div class="rounded-full bg-yellow-100 p-3 mr-4">
                                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm">Pending Verification</p>
                                        <p class="text-xl font-semibold">{{ number_format($stats['pending_verification']) }}</p>
                                    </div>
                                </div>

                                <!-- Pending Publication Card -->
                                <div class="bg-white border rounded-lg shadow-sm p-4 flex items-center">
                                    <div class="rounded-full bg-green-100 p-3 mr-4">
                                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm">Pending Publication</p>
                                        <p class="text-xl font-semibold">{{ number_format($stats['pending_publication']) }}</p>
                                    </div>
                                </div>

                                <!-- Published Card -->
                                <div class="bg-white border rounded-lg shadow-sm p-4 flex items-center">
                                    <div class="rounded-full bg-indigo-100 p-3 mr-4">
                                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-gray-500 text-sm">Published</p>
                                        <p class="text-xl font-semibold">{{ number_format($stats['published']) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
                                <h4 class="text-blue-800 font-medium mb-2">Select an Exam and Subject</h4>
                                <p class="text-blue-600 text-sm mb-3">Select from the list below or use the form to view marks for a specific exam and subject.</p>
                                <form action="{{ route('marks.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                                    <div>
                                        <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                                        <select id="exam_id" name="exam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            <option value="">Select Exam</option>
                                            @foreach($exams as $exam)
                                                <option value="{{ $exam->id }}">{{ $exam->title }} ({{ $exam->academicSession->name }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                                        <select id="subject_id" name="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="flex items-end">
                                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                            View Marks
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Recent Exams -->
                        <div>
                            <h3 class="text-lg font-semibold mb-4">Recent Exams</h3>
                            
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Academic Session</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($exams as $exam)
                                            <tr class="bg-white">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {{ $exam->title }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $exam->exam_type }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $exam->academicSession->name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 py-1 {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs">
                                                        {{ $exam->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('marks.index', ['exam_id' => $exam->id, 'subject_id' => $subjects->first()->id]) }}" class="text-blue-600 hover:text-blue-900">
                                                            View Marks
                                                        </a>
                                                        @can('create marks')
                                                        <a href="{{ route('marks.create', ['exam_id' => $exam->id, 'subject_id' => $subjects->first()->id]) }}" class="text-green-600 hover:text-green-900">
                                                            Enter Marks
                                                        </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                                    No exams found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="mt-4">
                                {{ $exams->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight active dropdown in sidebar
            const marksDropdown = document.getElementById('marksManagement');
            if (marksDropdown) {
                marksDropdown.classList.remove('hidden');
                const chevron = document.querySelector('[onclick="toggleDropdown(\'marksManagement\')"] .fa-chevron-down');
                if (chevron) {
                    chevron.classList.add('rotate-180');
                }
            }
        });
    </script>
    @endpush
</x-app-layout> 