<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Bulk Mark Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Enter Marks for {{ $exam->title }}</h3>
                            <p class="text-sm text-gray-600">Subject: {{ $subject->name }} ({{ $subject->code }})</p>
                            <p class="text-sm text-gray-600">Total Marks: {{ $exam->total_marks }} | Passing Marks: {{ $exam->passing_marks }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('marks.downloadTemplate', ['exam_id' => $exam->id, 'subject_id' => $subject->id, 'format' => 'xlsx']) }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                                Download Excel Template
                            </a>
                            <a href="{{ route('marks.index', ['exam_id' => $exam->id, 'subject_id' => $subject->id]) }}" 
                               class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">
                                View Existing Marks
                            </a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('marks.storeBulk') }}">
                        @csrf
                        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                        <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Marks (Max: {{ $exam->total_marks }})
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Absent</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($students as $student)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $student->roll_number }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $student->user->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <input 
                                                    type="number" 
                                                    name="marks[{{ $student->id }}]" 
                                                    id="marks_{{ $student->id }}"
                                                    min="0" 
                                                    max="{{ $exam->total_marks }}" 
                                                    step="0.01"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    value="{{ $existingMarks[$student->id]->marks_obtained ?? '' }}"
                                                    {{ isset($existingMarks[$student->id]) && $existingMarks[$student->id]->is_absent ? 'disabled' : '' }}
                                                >
                                                @error("marks.{$student->id}")
                                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                                @enderror
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <input 
                                                    type="checkbox" 
                                                    name="is_absent[{{ $student->id }}]" 
                                                    id="is_absent_{{ $student->id }}"
                                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                    {{ isset($existingMarks[$student->id]) && $existingMarks[$student->id]->is_absent ? 'checked' : '' }}
                                                    onchange="toggleMarksInput(this, '{{ $student->id }}')"
                                                >
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                <input 
                                                    type="text" 
                                                    name="remarks[{{ $student->id }}]" 
                                                    id="remarks_{{ $student->id }}"
                                                    class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                    value="{{ $existingMarks[$student->id]->remarks ?? '' }}"
                                                >
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                                No students found for this class.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="flex justify-between mt-6">
                            <div class="text-sm text-gray-500">
                                <p>* Enter marks for each student or mark as absent</p>
                                <p>* Marks must be between 0 and {{ $exam->total_marks }}</p>
                                <p>* Passing marks: {{ $exam->passing_marks }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button type="submit" name="action" value="save_draft" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    Save as Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Submit for Verification
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleMarksInput(checkbox, studentId) {
            const marksInput = document.getElementById('marks_' + studentId);
            
            if (checkbox.checked) {
                marksInput.disabled = true;
                marksInput.value = '';
            } else {
                marksInput.disabled = false;
            }
        }
    </script>
</x-app-layout> 