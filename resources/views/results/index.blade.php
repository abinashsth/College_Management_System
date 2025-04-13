@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2>Results Management</h2>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('results.analysis') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> Results Analysis
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filter Results</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('results.index') }}" method="GET" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="class_id" class="mr-2">Class:</label>
                    <select name="class_id" id="class_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if($selectedClassId)
                <div class="form-group mr-3 mb-2">
                    <label for="section_id" class="mr-2">Section:</label>
                    <select name="section_id" id="section_id" class="form-control" onchange="this.form.submit()">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ $selectedSectionId == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="form-group mr-3 mb-2">
                    <label for="exam_id" class="mr-2">Exam:</label>
                    <select name="exam_id" id="exam_id" class="form-control" onchange="this.form.submit()">
                        <option value="">Select Exam</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ $selectedExamId == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                
                <a href="{{ route('results.index') }}" class="btn btn-secondary mb-2 ml-2">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </form>
        </div>
    </div>

    @if($selectedClassId && $selectedExamId)
        <div class="card mt-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Results List</h5>
                
                @can('export-results')
                <div>
                    <form action="{{ route('results.export.excel') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                        <input type="hidden" name="exam_id" value="{{ $selectedExamId }}">
                        <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
                        <button type="submit" class="btn btn-sm btn-light">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                    </form>
                </div>
                @endcan
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Student Name</th>
                                <th>Roll Number</th>
                                <th>GPA</th>
                                <th>Result</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($results as $result)
                                <tr>
                                    <td>{{ $result['student']->id }}</td>
                                    <td>{{ $result['student']->name }}</td>
                                    <td>{{ $result['student']->roll_number }}</td>
                                    <td>{{ number_format($result['gpa'], 2) }}</td>
                                    <td>
                                        @if($result['passed'])
                                            <span class="badge badge-success">Pass</span>
                                        @else
                                            <span class="badge badge-danger">Fail</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('results.student', ['studentId' => $result['student']->id, 'examId' => $selectedExamId]) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        
                                        @can('download-results')
                                        <a href="{{ route('results.download.pdf', ['studentId' => $result['student']->id, 'examId' => $selectedExamId]) }}" 
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-file-pdf"></i> PDF
                                        </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No results found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($selectedClassId || $selectedExamId)
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> Please select both Class and Exam to view results.
        </div>
    @else
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> Use the filters above to view student results.
        </div>
    @endif
</div>
@endsection 