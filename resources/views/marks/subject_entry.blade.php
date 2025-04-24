<x-app-layout>
    <x-slot name="title">
        Subject Marks Entry
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subject Marks Entry') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Selection Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">Select Exam and Subject</h3>
                        <div>
                            <a href="{{ route('marks.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-arrow-left mr-1"></i> Back to Marks Dashboard
                            </a>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    <form method="GET" action="{{ route('marks.subjectEntry') }}" class="mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Select Exam</label>
                                <select id="exam_id" name="exam_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Select Exam --</option>
                                    @foreach ($exams as $examOption)
                                        <option value="{{ $examOption->id }}" {{ (request('exam_id') == $examOption->id || (isset($exam) && $exam->id == $examOption->id)) ? 'selected' : '' }}>
                                            {{ $examOption->title }} ({{ ucfirst($examOption->exam_type) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Select Subject</label>
                                <select id="subject_id" name="subject_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Select Subject --</option>
                                    @foreach ($subjects as $subjectOption)
                                        <option value="{{ $subjectOption->id }}" {{ (request('subject_id') == $subjectOption->id || (isset($subject) && $subject->id == $subjectOption->id)) ? 'selected' : '' }}>
                                            {{ $subjectOption->name }} ({{ $subjectOption->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <button type="submit" class="py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Load Students
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Marks Entry Form -->
            @if(isset($exam) && isset($subject) && $students->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Enter Marks: {{ $exam->title }}</h3>
                                <p class="text-sm text-gray-600">Subject: {{ $subject->name }} ({{ $subject->code }})</p>
                                <p class="text-sm text-gray-600">Total Marks: {{ $exam->total_marks }} | Passing Marks: {{ $exam->passing_marks }}</p>
                                <p class="text-sm text-gray-600">Academic Session: {{ $exam->academicSession->name }}</p>
                            </div>
                            <div>
                                @if(count($existingMarks) > 0)
                                    <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                        {{ count($existingMarks) }} marks already entered
                                    </span>
                                @endif
                            </div>
                        </div>

                        <form method="POST" action="{{ route('marks.storeSubject') }}" id="marks-form">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                            <div class="mb-4">
                                <div class="flex justify-between items-center mb-2">
                                    <h4 class="text-md font-medium text-gray-700">Student Marks</h4>
                                    <div class="flex items-center space-x-4">
                                        <button type="button" id="select-all-btn" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                                        <button type="button" id="clear-all-btn" class="text-sm text-red-600 hover:text-red-800">Clear All</button>
                                    </div>
                                </div>
                                
                                <div class="overflow-x-auto bg-gray-50 rounded-lg p-1">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks (max: {{ $exam->total_marks }})</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                                <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($students as $student)
                                                <tr class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-blue-50 transition-colors">
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        {{ $student->roll_number }}
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <input type="number" 
                                                               name="marks[{{ $student->id }}]" 
                                                               value="{{ isset($existingMarks[$student->id]) ? $existingMarks[$student->id]->marks_obtained : '' }}"
                                                               class="marks-input w-24 rounded border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                               min="0" 
                                                               max="{{ $exam->total_marks }}" 
                                                               step="0.01"
                                                               {{ isset($existingMarks[$student->id]) && $existingMarks[$student->id]->is_absent ? 'disabled' : '' }}>
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <input type="checkbox" 
                                                               name="is_absent[{{ $student->id }}]" 
                                                               class="absent-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                               {{ isset($existingMarks[$student->id]) && $existingMarks[$student->id]->is_absent ? 'checked' : '' }}
                                                               onchange="toggleMarkInput(this, {{ $student->id }})">
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        <input type="text" 
                                                               name="remarks[{{ $student->id }}]" 
                                                               value="{{ isset($existingMarks[$student->id]) ? $existingMarks[$student->id]->remarks : '' }}"
                                                               class="w-full rounded border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                               placeholder="Optional remarks">
                                                    </td>
                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                        @if(isset($existingMarks[$student->id]))
                                                            @php
                                                                $status = $existingMarks[$student->id]->status;
                                                                $statusClasses = [
                                                                    'draft' => 'bg-yellow-100 text-yellow-800',
                                                                    'submitted' => 'bg-blue-100 text-blue-800',
                                                                    'verified' => 'bg-green-100 text-green-800',
                                                                    'published' => 'bg-purple-100 text-purple-800',
                                                                    'rejected' => 'bg-red-100 text-red-800',
                                                                ][$status] ?? 'bg-gray-100 text-gray-800';
                                                            @endphp
                                                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClasses }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        @else
                                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                                Not entered
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="flex justify-between mt-6">
                                <div class="text-sm text-gray-500">
                                    <p>* Enter marks for each student or mark as absent</p>
                                    <p>* Marks must be between 0 and {{ $exam->total_marks }}</p>
                                </div>
                                <div class="flex space-x-2">
                                    <button type="submit" name="action" value="save_draft" class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                        Save as Draft
                                    </button>
                                    <button type="submit" name="action" value="submit" class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Submit for Verification
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @elseif(request('exam_id') && request('subject_id'))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Students Found</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                There are no active students for this class and exam.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to toggle marks input based on absent checkbox
            window.toggleMarkInput = function(checkbox, studentId) {
                const marksInput = document.querySelector(`input[name="marks[${studentId}]"]`);
                
                if (checkbox.checked) {
                    marksInput.disabled = true;
                    marksInput.value = '';
                } else {
                    marksInput.disabled = false;
                }
            };
            
            // Select all button functionality
            document.getElementById('select-all-btn').addEventListener('click', function() {
                document.querySelectorAll('.marks-input:not(:disabled)').forEach(input => {
                    input.value = {{ $exam->total_marks ?? 0 }};
                });
            });
            
            // Clear all button functionality
            document.getElementById('clear-all-btn').addEventListener('click', function() {
                document.querySelectorAll('.marks-input:not(:disabled)').forEach(input => {
                    input.value = '';
                });
            });
            
            // Form validation before submit
            document.getElementById('marks-form').addEventListener('submit', function(e) {
                let valid = false;
                
                // Check if at least one student has marks or is marked absent
                document.querySelectorAll('.marks-input, .absent-checkbox').forEach(input => {
                    if ((input.type === 'number' && input.value !== '') || 
                        (input.type === 'checkbox' && input.checked)) {
                        valid = true;
                    }
                });
                
                if (!valid) {
                    e.preventDefault();
                    alert('Please enter marks for at least one student or mark them as absent.');
                }
            });
        });
    </script>
    @endpush
</x-app-layout> 