<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Import Marks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-2">Import Marks from Excel/CSV</h3>
                        <p class="text-sm text-gray-600">Upload a file with student marks. The file should match the template format.</p>
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

                    @if ($errors->any())
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p class="font-semibold">Validation errors:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('marks.processImport') }}" enctype="multipart/form-data" class="mt-4">
                        @csrf

                        @if (!isset($exam) || !isset($subject))
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="exam_id">Select Exam</label>
                                    <select id="exam_id" name="exam_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Select Exam --</option>
                                        @foreach ($exams as $exam)
                                            <option value="{{ $exam->id }}">
                                                {{ $exam->title }} ({{ $exam->exam_type }}) - {{ $exam->academicSession->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('exam_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="subject_id">Select Subject</label>
                                    <select id="subject_id" name="subject_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Select Subject --</option>
                                        @foreach ($subjects as $subject)
                                            <option value="{{ $subject->id }}">
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-50 p-4 rounded-md mb-6">
                                <h4 class="font-medium">Selected Exam & Subject</h4>
                                <p class="text-sm text-gray-600 mt-2">Exam: {{ $exam->title }} ({{ $exam->exam_type }})</p>
                                <p class="text-sm text-gray-600">Subject: {{ $subject->name }} ({{ $subject->code }})</p>
                                <p class="text-sm text-gray-600">Total Marks: {{ $exam->total_marks }} | Passing Marks: {{ $exam->passing_marks }}</p>
                                
                                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                                <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            </div>
                        @endif

                        <div class="mt-4">
                            <label for="import_file">Upload Marks File</label>
                            <input 
                                type="file" 
                                name="import_file" 
                                id="import_file" 
                                required
                                accept=".xlsx,.xls,.csv"
                                class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            >
                            <p class="text-sm text-gray-500 mt-1">Accepted formats: .xlsx, .xls, .csv</p>
                            @error('import_file')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-between mt-6">
                            <div>
                                @if (isset($exam) && isset($subject))
                                    <a href="{{ route('marks.downloadTemplate', ['exam_id' => $exam->id, 'subject_id' => $subject->id, 'format' => 'xlsx']) }}" 
                                       class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                                        Download Excel Template
                                    </a>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('marks.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                                    Cancel
                                </a>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Import Marks
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="mt-8 bg-yellow-50 p-4 rounded-md border border-yellow-200">
                        <h4 class="font-medium text-yellow-800">Import Instructions</h4>
                        <ul class="list-disc list-inside mt-2 text-sm text-yellow-700 space-y-1">
                            <li>Download the template file to ensure correct format</li>
                            <li>Fill in marks for each student or use "AB" to mark a student as absent</li>
                            <li>Do not modify the Roll No. or Admission No. columns</li>
                            <li>Marks must be within the allowed range (0 to maximum marks)</li>
                            <li>Save the file as Excel (.xlsx/.xls) or CSV (.csv)</li>
                            <li>Upload the file and confirm the import</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 