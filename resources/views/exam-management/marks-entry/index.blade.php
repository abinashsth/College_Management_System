@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Marks Entry</h2>
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
        <form action="{{ route('marks-entry.search') }}" method="GET" class="mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="exam_id">
                        Exam
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="exam_id" name="exam_id" required>
                        <option value="">Select Exam</option>
                        @foreach($exams as $exam)
                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                            {{ $exam->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="class_id">
                        Class
                    </label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="class_id" name="class_id" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
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
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="bg-[#37a6bc] hover:bg-[#2c849c] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        Search Students
                    </button>
                </div>
            </div>
        </form>

        @if(isset($students))
        <form action="{{ route('marks-entry.store') }}" method="POST" id="marksForm">
            @csrf
            <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
            <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Student</th>
                            <th class="py-3 px-6 text-center">Theory Marks</th>
                            <th class="py-3 px-6 text-center">Practical Marks</th>
                            <th class="py-3 px-6 text-center">Total Marks</th>
                            <th class="py-3 px-6 text-center">Grade</th>
                            <th class="py-3 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @forelse($students as $student)
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-left">
                                <div class="flex items-center">
                                    <div class="mr-2">
                                        <img class="w-6 h-6 rounded-full" src="{{ $student->profile_photo_url }}" alt="">
                                    </div>
                                    <span>{{ $student->name }}</span>
                                    <input type="hidden" name="students[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                </div>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <input type="number" step="0.01" min="0" max="100"
                                    class="shadow appearance-none border rounded w-24 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-center marks-input theory-marks"
                                    name="students[{{ $student->id }}][theory_marks]"
                                    value="{{ old("students.{$student->id}.theory_marks", optional($student->examResult)->theory_marks) }}"
                                    data-student-id="{{ $student->id }}"
                                    required>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <input type="number" step="0.01" min="0" max="100"
                                    class="shadow appearance-none border rounded w-24 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-center marks-input practical-marks"
                                    name="students[{{ $student->id }}][practical_marks]"
                                    value="{{ old("students.{$student->id}.practical_marks", optional($student->examResult)->practical_marks) }}"
                                    data-student-id="{{ $student->id }}"
                                    required>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="total-marks" id="total-{{ $student->id }}">0.00</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="grade" id="grade-{{ $student->id }}">-</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <span class="status" id="status-{{ $student->id }}">-</span>
                            </td>
                        </tr>
                        @empty
                        <tr class="border-b border-gray-200">
                            <td colspan="6" class="py-3 px-6 text-center text-gray-500">
                                No students found for the selected criteria
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($students) > 0)
            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Save Marks
                </button>
            </div>
            @endif
        </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to calculate total marks and grade
    function calculateMarks(studentId) {
        const theoryInput = document.querySelector(`input[name="students[${studentId}][theory_marks]"]`);
        const practicalInput = document.querySelector(`input[name="students[${studentId}][practical_marks]"]`);
        const totalSpan = document.getElementById(`total-${studentId}`);
        const gradeSpan = document.getElementById(`grade-${studentId}`);
        const statusSpan = document.getElementById(`status-${studentId}`);

        const theory = parseFloat(theoryInput.value) || 0;
        const practical = parseFloat(practicalInput.value) || 0;
        const total = theory + practical;

        totalSpan.textContent = total.toFixed(2);

        // Calculate grade based on total percentage
        const grade = calculateGrade(total);
        gradeSpan.textContent = grade;

        // Set pass/fail status
        const status = total >= 40 ? 'Pass' : 'Fail';
        statusSpan.textContent = status;
        statusSpan.className = `status ${status === 'Pass' ? 'text-green-600' : 'text-red-600'}`;
    }

    function calculateGrade(total) {
        if (total >= 90) return 'A+';
        if (total >= 80) return 'A';
        if (total >= 70) return 'B+';
        if (total >= 60) return 'B';
        if (total >= 50) return 'C+';
        if (total >= 40) return 'C';
        return 'F';
    }

    // Add event listeners to all marks inputs
    document.querySelectorAll('.marks-input').forEach(input => {
        input.addEventListener('input', function() {
            const studentId = this.dataset.studentId;
            calculateMarks(studentId);
        });

        // Calculate initial values
        const studentId = input.dataset.studentId;
        calculateMarks(studentId);
    });

    // Dynamic subject loading based on class selection
    document.getElementById('class_id').addEventListener('change', function() {
        const classId = this.value;
        const subjectSelect = document.getElementById('subject_id');
        
        // Clear current options
        subjectSelect.innerHTML = '<option value="">Select Subject</option>';
        
        if (classId) {
            fetch(`/api/classes/${classId}/subjects`)
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
