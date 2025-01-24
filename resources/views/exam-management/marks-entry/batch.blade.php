@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Batch Marks Entry</h2>
        <a href="{{ route('marks-entry.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Regular Entry
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <form action="{{ route('marks-entry.batch.upload') }}" method="POST" enctype="multipart/form-data" class="mb-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="exam_id">
                        Exam
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="exam_id" name="exam_id" required>
                        <option value="">Select Exam</option>
                        @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="subject_id">
                        Subject
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="marks_file">
                        Marks File (Excel/CSV)
                    </label>
                    <input type="file" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="marks_file" 
                        name="marks_file"
                        accept=".csv,.xlsx,.xls"
                        required>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-[#37a6bc] hover:bg-[#2c849c] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Upload Marks
                </button>

                <a href="{{ route('marks-entry.batch.template') }}" class="ml-4 text-[#37a6bc] hover:text-[#2c849c]">
                    Download Template
                </a>
            </div>
        </form>

        @if(isset($preview))
        <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Preview</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Student ID</th>
                            <th class="py-3 px-6 text-left">Student Name</th>
                            <th class="py-3 px-6 text-center">Theory Marks</th>
                            <th class="py-3 px-6 text-center">Practical Marks</th>
                            <th class="py-3 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($preview as $row)
                        <tr class="border-b border-gray-200 hover:bg-gray-100 {{ $row['status'] === 'error' ? 'bg-red-50' : '' }}">
                            <td class="py-3 px-6 text-left">{{ $row['student_id'] }}</td>
                            <td class="py-3 px-6 text-left">{{ $row['student_name'] }}</td>
                            <td class="py-3 px-6 text-center">{{ $row['theory_marks'] }}</td>
                            <td class="py-3 px-6 text-center">{{ $row['practical_marks'] }}</td>
                            <td class="py-3 px-6 text-center">
                                @if($row['status'] === 'error')
                                <span class="text-red-600">{{ $row['message'] }}</span>
                                @else
                                <span class="text-green-600">Valid</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!collect($preview)->where('status', 'error')->count())
            <div class="mt-6 flex justify-end">
                <form action="{{ route('marks-entry.batch.confirm') }}" method="POST">
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batchId }}">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Confirm Upload
                    </button>
                </form>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic subject loading based on exam selection
    document.getElementById('exam_id').addEventListener('change', function() {
        const examId = this.value;
        const subjectSelect = document.getElementById('subject_id');
        
        // Clear current options
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        
        if (examId) {
            fetch(`/api/exams/${examId}/subjects`)
                .then(response => response.json())
                .then(subjects => {
                    subjects.forEach(subject => {
                        const option = new Option(subject.name, subject.id);
                        subjectSelect.add(option);
                    });
                });
        }
    });
});
</script>
@endpush
@endsection
