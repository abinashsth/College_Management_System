@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Class-wise Result Analysis</h4>
                    <div>
                        <a href="{{ route('results.export.class-analysis', $exam->id) }}" class="btn btn-primary">
                            <i class="fas fa-file-export"></i> Export Analysis
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <h5>Exam: {{ $exam->name }}</h5>
                            <p>Start Date: {{ $exam->start_date->format('d M Y') }} | End Date: {{ $exam->end_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Class</th>
                                            <th>Section</th>
                                            <th>Total Students</th>
                                            <th>Passed</th>
                                            <th>Failed</th>
                                            <th>Pass %</th>
                                            <th>Avg. Marks</th>
                                            <th>Avg. GPA</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($classAnalysis as $analysis)
                                        <tr>
                                            <td>{{ $analysis->class_name }}</td>
                                            <td>{{ $analysis->section_name }}</td>
                                            <td>{{ $analysis->total_students }}</td>
                                            <td>{{ $analysis->passed_students }}</td>
                                            <td>{{ $analysis->failed_students }}</td>
                                            <td>{{ number_format($analysis->pass_percentage, 2) }}%</td>
                                            <td>{{ number_format($analysis->average_marks, 2) }}</td>
                                            <td>{{ number_format($analysis->average_gpa, 2) }}</td>
                                            <td>
                                                <a href="{{ route('results.section', ['exam' => $exam->id, 'section' => $analysis->section_id]) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="9" class="text-center">No class analysis available</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Overall Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6>Pass Rate</h6>
                                        <div class="progress">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                 style="width: {{ $overallAnalysis->pass_percentage }}%"
                                                 aria-valuenow="{{ $overallAnalysis->pass_percentage }}" 
                                                 aria-valuemin="0" aria-valuemax="100">
                                                {{ number_format($overallAnalysis->pass_percentage, 2) }}%
                                            </div>
                                        </div>
                                    </div>
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Total Students
                                            <span class="badge bg-primary rounded-pill">{{ $overallAnalysis->total_students }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Passed Students
                                            <span class="badge bg-success rounded-pill">{{ $overallAnalysis->passed_students }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Failed Students
                                            <span class="badge bg-danger rounded-pill">{{ $overallAnalysis->failed_students }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Average Marks
                                            <span class="badge bg-info rounded-pill">{{ number_format($overallAnalysis->average_marks, 2) }}</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Average GPA
                                            <span class="badge bg-info rounded-pill">{{ number_format($overallAnalysis->average_gpa, 2) }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 