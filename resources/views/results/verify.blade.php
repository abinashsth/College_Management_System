@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Result Verification</h4>
                    <div>
                        @can('verify result')
                        <a href="{{ route('results.verify.all', $exam->id) }}" 
                           class="btn btn-success"
                           onclick="return confirm('Are you sure you want to verify all results? This action cannot be undone.');">
                            <i class="fas fa-check-double"></i> Verify All Results
                        </a>
                        @endcan
                    </div>
                </div>

                <div class="card-body">
                    <div class="mb-4">
                        <div class="alert alert-info">
                            <h5>Exam: {{ $exam->name }}</h5>
                            <p>Start Date: {{ $exam->start_date->format('d M Y') }} | End Date: {{ $exam->end_date->format('d M Y') }}</p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Total Subjects</th>
                                    <th>Total Marks</th>
                                    <th>Obtained Marks</th>
                                    <th>Percentage</th>
                                    <th>GPA</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $result)
                                <tr>
                                    <td>{{ $result->id }}</td>
                                    <td>
                                        {{ $result->student->name }}
                                        <br>
                                        <small class="text-muted">{{ $result->student->admission_number }}</small>
                                    </td>
                                    <td>
                                        {{ $result->student->studentRecord->section->class->name }} - 
                                        {{ $result->student->studentRecord->section->name }}
                                    </td>
                                    <td>{{ $result->total_subjects }}</td>
                                    <td>{{ $result->total_marks }}</td>
                                    <td>{{ $result->obtained_marks }}</td>
                                    <td>{{ number_format($result->percentage, 2) }}%</td>
                                    <td>{{ number_format($result->gpa, 2) }}</td>
                                    <td>{{ $result->grade }}</td>
                                    <td>
                                        @if($result->is_passed)
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                        <br>
                                        @if($result->is_verified)
                                            <span class="badge bg-info">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending Verification</span>
                                        @endif
                                    </td>
                                    <td class="d-flex">
                                        <a href="{{ route('results.show', $result->id) }}" class="btn btn-sm btn-info me-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @can('verify result')
                                        @if(!$result->is_verified)
                                        <form action="{{ route('results.verify', $result->id) }}" method="POST" class="me-1">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" 
                                                    onclick="return confirm('Are you sure you want to verify this result?');">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @endcan
                                        
                                        @can('delete result')
                                        <form action="{{ route('results.destroy', $result->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete this result?');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">No results to verify</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $results->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 