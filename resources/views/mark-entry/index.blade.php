@extends('layouts.app')

@section('title', 'Mark Entry Dashboard')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Mark Entry Dashboard</h1>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Students
        </div>
        <div class="card-body">
            <form id="filterForm" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label for="academic_year_id" class="form-label">Academic Year</label>
                    <select class="form-select" id="academic_year_id" name="academic_year_id" required>
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->year }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="faculty_id" class="form-label">Faculty</label>
                    <select class="form-select" id="faculty_id" name="faculty_id" required>
                        <option value="">Select Faculty</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ $faculty->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="department_id" class="form-label">Department</label>
                    <select class="form-select" id="department_id" name="department_id" required disabled>
                        <option value="">Select Department</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="class_id" class="form-label">Class</label>
                    <select class="form-select" id="class_id" name="class_id" required disabled>
                        <option value="">Select Class</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select class="form-select" id="subject_id" name="subject_id" required disabled>
                        <option value="">Select Subject</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="exam_id" class="form-label">Exam Term</label>
                    <select class="form-select" id="exam_id" name="exam_id" required disabled>
                        <option value="">Select Exam Term</option>
                    </select>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i> Search Students
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="studentsCard" class="card mb-4 d-none">
        <div class="card-header">
            <i class="fas fa-users me-1"></i>
            Student Marks
        </div>
        <div class="card-body">
            <form id="marksForm">
                @csrf
                <input type="hidden" name="subject_id" id="marks_subject_id">
                <input type="hidden" name="exam_id" id="marks_exam_id">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Roll Number</th>
                                <th>Marks</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Save Marks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Faculty change handler
    $('#faculty_id').change(function() {
        const facultyId = $(this).val();
        if (facultyId) {
            fetch(`/mark-entry/departments/${facultyId}`)
                .then(response => response.json())
                .then(data => {
                    $('#department_id').html('<option value="">Select Department</option>')
                        .append(data.map(dept => 
                            `<option value="${dept.id}">${dept.name}</option>`
                        )).prop('disabled', false);
                });
        } else {
            $('#department_id').html('<option value="">Select Department</option>').prop('disabled', true);
        }
        resetDependentDropdowns('department');
    });

    // Department change handler
    $('#department_id').change(function() {
        const departmentId = $(this).val();
        if (departmentId) {
            fetch(`/mark-entry/classes/${departmentId}`)
                .then(response => response.json())
                .then(data => {
                    $('#class_id').html('<option value="">Select Class</option>')
                        .append(data.map(cls => 
                            `<option value="${cls.id}">${cls.name}</option>`
                        )).prop('disabled', false);
                });
        } else {
            $('#class_id').html('<option value="">Select Class</option>').prop('disabled', true);
        }
        resetDependentDropdowns('class');
    });

    // Class change handler
    $('#class_id').change(function() {
        const classId = $(this).val();
        if (classId) {
            Promise.all([
                fetch(`/mark-entry/subjects/${classId}`).then(res => res.json()),
                fetch(`/mark-entry/exam-terms/${classId}`).then(res => res.json())
            ]).then(([subjects, examTerms]) => {
                $('#subject_id').html('<option value="">Select Subject</option>')
                    .append(subjects.map(subject => 
                        `<option value="${subject.id}">${subject.name}</option>`
                    )).prop('disabled', false);

                $('#exam_id').html('<option value="">Select Exam Term</option>')
                    .append(examTerms.map(exam => 
                        `<option value="${exam.id}">${exam.name}</option>`
                    )).prop('disabled', false);
            });
        } else {
            $('#subject_id, #exam_id').html('<option value="">Select</option>').prop('disabled', true);
        }
        resetDependentDropdowns('subject');
    });

    // Filter form submit handler
    $('#filterForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/mark-entry/students', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const tableBody = $('#studentsTableBody');
            tableBody.empty();
            
            data.students.forEach(student => {
                const mark = student.marks[0]?.marks ?? '';
                tableBody.append(`
                    <tr>
                        <td>${student.name}</td>
                        <td>${student.roll_number}</td>
                        <td>
                            <input type="number" 
                                class="form-control" 
                                name="marks[${student.id}]" 
                                value="${mark}"
                                min="0" 
                                max="${data.maxMarks}" 
                                required>
                        </td>
                    </tr>
                `);
            });

            $('#marks_subject_id').val(formData.get('subject_id'));
            $('#marks_exam_id').val(formData.get('exam_id'));
            $('#studentsCard').removeClass('d-none');
        });
    });

    // Marks form submit handler
    $('#marksForm').submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        fetch('/mark-entry/store', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Marks saved successfully!');
            }
        })
        .catch(error => {
            alert('Error saving marks. Please try again.');
        });
    });

    function resetDependentDropdowns(from) {
        switch(from) {
            case 'department':
                $('#class_id').html('<option value="">Select Class</option>').prop('disabled', true);
            case 'class':
                $('#subject_id, #exam_id').html('<option value="">Select</option>').prop('disabled', true);
                $('#studentsCard').addClass('d-none');
                break;
            case 'subject':
                $('#studentsCard').addClass('d-none');
                break;
        }
    }
});
</script>
@endpush 