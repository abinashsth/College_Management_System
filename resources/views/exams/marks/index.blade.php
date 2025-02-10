@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Marks Entry</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('marks.search') }}" method="GET" class="mb-4">
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
                        <label for="subject_id" class="form-label">Select Subject</label>
                        <select name="subject_id" id="subject_id" class="form-select" required>
                            <option value="">Choose Class First</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>

            @if(isset($students))
            <form action="{{ route('marks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="exam_id" value="{{ request('exam_id') }}">
                <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                <input type="hidden" name="subject_id" value="{{ request('subject_id') }}">

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Roll No</th>
                                <th>Student Name</th>
                                <th>Theory ({{ $subject->theory_marks }})</th>
                                <th>Practical ({{ $subject->practical_marks }})</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>{{ $student->roll_no }}</td>
                                <td>{{ $student->name }}</td>
                                <td>
                                    <input type="number" 
                                           name="marks[{{ $student->id }}][theory]" 
                                           class="form-control"
                                           min="0"
                                           max="{{ $subject->theory_marks }}"
                                           value="{{ old('marks.' . $student->id . '.theory', $student->mark->theory_marks ?? '') }}"
                                           required>
                                </td>
                                <td>
                                    <input type="number" 
                                           name="marks[{{ $student->id }}][practical]" 
                                           class="form-control"
                                           min="0"
                                           max="{{ $subject->practical_marks }}"
                                           value="{{ old('marks.' . $student->id . '.practical', $student->mark->practical_marks ?? '') }}"
                                           required>
                                </td>
                                <td class="total-marks">0</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Marks
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Dynamic subject loading based on class selection
    $('#class_id').change(function() {
        const classId = $(this).val();
        if (classId) {
            $.get(`/api/classes/${classId}/subjects`, function(subjects) {
                let options = '<option value="">Select Subject</option>';
                subjects.forEach(subject => {
                    options += `<option value="${subject.id}">${subject.name}</option>`;
                });
                $('#subject_id').html(options);
            });
        } else {
            $('#subject_id').html('<option value="">Choose Class First</option>');
        }
    });

    // Calculate total marks
    $('input[type="number"]').on('input', function() {
        const row = $(this).closest('tr');
        const theory = parseFloat(row.find('input[name*="[theory]"]').val()) || 0;
        const practical = parseFloat(row.find('input[name*="[practical]"]').val()) || 0;
        row.find('.total-marks').text(theory + practical);
    });
</script>
@endpush
@endsection
