<x-app-layout>
    <x-slot name="title">
        Select Exam and Subject
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Select Exam and Subject') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Select Exam and Subject for Mark Entry</h3>
                        <div>
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm">Back to Dashboard</a>
                        </div>
                    </div>

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                        <h4 class="font-medium text-blue-800">Viewing or Managing Marks</h4>
                        <p class="mt-2 text-sm text-blue-700">
                            Select an exam and subject combination to view or manage marks. You can view, edit, or export marks after selection.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('marks.index') }}" class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="exam_id" class="block text-sm font-medium text-gray-700">Select Exam</label>
                                <select id="exam_id" name="exam_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Select Exam --</option>
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}">
                                            {{ $exam->title }} ({{ $exam->exam_type }}) - {{ $exam->academicSession->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">Select Subject</label>
                                <select id="subject_id" name="subject_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Select Subject --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">
                                            {{ $subject->name }} ({{ $subject->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-center mt-6 space-x-4">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('View Marks') }}
                            </button>
                            
                            <a href="{{ route('marks.create') }}" 
                               onclick="event.preventDefault(); document.getElementById('create-marks-form').submit();"
                               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                {{ __('Enter New Marks') }}
                            </a>
                        </div>
                    </form>
                    
                    <!-- Hidden form for create marks navigation -->
                    <form id="create-marks-form" action="{{ route('marks.create') }}" method="GET" class="hidden">
                        <input type="hidden" id="create_exam_id" name="exam_id" value="">
                        <input type="hidden" id="create_subject_id" name="subject_id" value="">
                    </form>

                    <div class="mt-8">
                        <h4 class="text-lg font-medium mb-4">Other Options</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('marks.import') }}" class="block p-4 bg-green-50 border border-green-200 rounded-md hover:bg-green-100">
                                <div class="font-medium text-green-800">Bulk Import Marks</div>
                                <p class="text-sm text-green-600 mt-1">Upload Excel/CSV file to import marks in bulk</p>
                            </a>
                            
                            <a href="{{ route('marks.createBulk') }}?class_id=" 
                               id="bulk-entry-link" 
                               class="block p-4 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100">
                                <div class="font-medium text-blue-800">Bulk Marks Entry</div>
                                <p class="text-sm text-blue-600 mt-1">Enter marks for multiple students at once</p>
                            </a>
                            
                            <a href="{{ route('marks.verifyInterface') }}" class="block p-4 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100">
                                <div class="font-medium text-purple-800">Verification Queue</div>
                                <p class="text-sm text-purple-600 mt-1">View marks pending verification</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const examSelect = document.getElementById('exam_id');
            const subjectSelect = document.getElementById('subject_id');
            const createExamInput = document.getElementById('create_exam_id');
            const createSubjectInput = document.getElementById('create_subject_id');
            const bulkEntryLink = document.getElementById('bulk-entry-link');
            
            // Update hidden form values when selects change
            function updateHiddenInputs() {
                if (examSelect && createExamInput) {
                    createExamInput.value = examSelect.value;
                }
                
                if (subjectSelect && createSubjectInput) {
                    createSubjectInput.value = subjectSelect.value;
                }
                
                // Update bulk entry link if both exam and subject are selected
                if (bulkEntryLink && examSelect && examSelect.value) {
                    // Get the current href and update with selected exam
                    let href = "{{ route('marks.createBulk') }}?exam_id=" + examSelect.value;
                    
                    // Add subject ID if selected
                    if (subjectSelect && subjectSelect.value) {
                        href += "&subject_id=" + subjectSelect.value;
                    }
                    
                    bulkEntryLink.href = href;
                }
            }
            
            // Initialize
            updateHiddenInputs();
            
            // Add change event listeners
            if (examSelect) {
                examSelect.addEventListener('change', updateHiddenInputs);
            }
            
            if (subjectSelect) {
                subjectSelect.addEventListener('change', updateHiddenInputs);
            }
            
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