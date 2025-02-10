@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Generate Marksheet</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('marksheet.generate') }}" method="GET" class="mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="exam_id" class="form-label">Select Exam</label>
                        <select name="exam_id" id="exam_id" class="form-select" required>
                            <option value="">Choose...</option>
                            @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="class_id" class="form-label">Select Class</label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">Choose...</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="student_id" class="form-label">Select Student</label>
                        <select name="student_id" id="student_id" class="form-select" required>
                            <option value="">Choose Class First</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i> Generate Marksheet
                    </button>
                </div>
            </form>

            @if(isset($marksheet))
            <div class="marksheet mt-4">
                <div class="text-center mb-4">
                    <h4>{{ config('app.name') }}</h4>
                    <h5>{{ $exam->name }} - Marksheet</h5>
                    <p class="mb-0">Academic Year: {{ $exam->academic_year }}</p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th>Student Name:</th>
                                <td>{{ $student->name }}</td>
                            </tr>
                            <tr>
                                <th>Roll No:</th>
                                <td>{{ $student->roll_no }}</td>
                            </tr>
                            <tr>
                                <th>Class:</th>
                                <td>{{ $class->name }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Theory</th>
                                <th>Practical</th>
                                <th>Total</th>
                                <th>Grade</th>
                                <th>Grade Point</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($marksheet->subjects as $subject)
                            <tr>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->theory_marks }}</td>
                                <td>{{ $subject->practical_marks }}</td>
                                <td>{{ $subject->total_marks }}</td>
                                <td>{{ $subject->grade }}</td>
                                <td>{{ $subject->grade_point }}</td>
                                <td>{{ $subject->remarks }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3">Total Marks</th>
                                <td>{{ $marksheet->total_marks }}</td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <th colspan="3">Percentage</th>
                                <td>{{ $marksheet->percentage }}%</td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <th colspan="3">Grade Point Average (GPA)</th>
                                <td>{{ $marksheet->gpa }}</td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <th colspan="3">Final Grade</th>
                                <td>{{ $marksheet->final_grade }}</td>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <th colspan="3">Result</th>
                                <td colspan="4">
                                    <span class="badge bg-{{ $marksheet->result === 'PASS' ? 'success' : 'danger' }}">
                                        {{ $marksheet->result }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4">
                    <button onclick="window.print()" class="btn btn-secondary">
                        <i class="fas fa-print"></i> Print Marksheet
                    </button>
                    <a href="{{ route('marksheet.download', ['exam' => $exam->id, 'student' => $student->id]) }}" 
                       class="btn btn-primary">
                        <i class="fas fa-download"></i> Download PDF
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Dynamic student loading based on class selection
    $('#class_id').change(function() {
        const classId = $(this).val();
        if (classId) {
            $.get(`/api/classes/${classId}/students`, function(students) {
                let options = '<option value="">Select Student</option>';
                students.forEach(student => {
                    options += `<option value="${student.id}">${student.name} (${student.roll_no})</option>`;
                });
                $('#student_id').html(options);
            });
        } else {
            $('#student_id').html('<option value="">Choose Class First</option>');
        }
    });
</script>
@endpush
@endsection
