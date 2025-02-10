@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Tabulation Sheet</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('tabulation.generate') }}" method="GET" class="mb-4">
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
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-table"></i> Generate Sheet
                        </button>
                    </div>
                </div>
            </form>

            @if(isset($results))
            <div class="table-responsive mt-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Roll No</th>
                            <th>Student Name</th>
                            @foreach($subjects as $subject)
                            <th>{{ $subject->name }}</th>
                            @endforeach
                            <th>Total</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ $result->student->roll_no }}</td>
                            <td>{{ $result->student->name }}</td>
                            @foreach($result->marks as $mark)
                            <td>{{ $mark }}</td>
                            @endforeach
                            <td>{{ $result->total }}</td>
                            <td>{{ $result->average }}</td>
                            <td>{{ $result->grade }}</td>
                            <td>{{ $result->position }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
