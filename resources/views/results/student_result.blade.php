@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Student Result</h2>
            <div>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                @can('download-results')
                <a href="{{ route('results.download.pdf', ['studentId' => $student->id, 'examId' => $exam->id]) }}" class="btn btn-danger ml-2">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                @endcan
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Student Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Student Name</th>
                            <td>{{ $student->name }}</td>
                        </tr>
                        <tr>
                            <th>Roll Number</th>
                            <td>{{ $student->roll_number }}</td>
                        </tr>
                        <tr>
                            <th>Admission Number</th>
                            <td>{{ $student->admission_number }}</td>
                        </tr>
                        <tr>
                            <th>Class</th>
                            <td>
                                @if($student->enrollments->first())
                                    {{ $student->enrollments->first()->schoolClass->name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Section</th>
                            <td>
                                @if($student->enrollments->first())
                                    {{ $student->enrollments->first()->section->name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Exam Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Exam Name</th>
                            <td>{{ $exam->name }}</td>
                        </tr>
                        <tr>
                            <th>Term</th>
                            <td>{{ $exam->term ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Marks</th>
                            <td>{{ $totalObtained }} / {{ $totalPossible }}</td>
                        </tr>
                        <tr>
                            <th>Percentage</th>
                            <td>{{ number_format($totalPercentage, 2) }}%</td>
                        </tr>
                        <tr>
                            <th>GPA</th>
                            <td>{{ number_format($gpaResult['gpa'], 2) }}</td>
                        </tr>
                        <tr>
                            <th>Result</th>
                            <td>
                                @if($gpaResult['passed'])
                                    <span class="badge badge-success">PASSED</span>
                                @else
                                    <span class="badge badge-danger">FAILED</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Subject Marks</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th>Subject</th>
                            <th>Marks Obtained</th>
                            <th>Total Marks</th>
                            <th>Percentage</th>
                            <th>Grade</th>
                            <th>GPA</th>
                            <th>Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $result)
                            <tr>
                                <td>{{ $result['subject']->name }}</td>
                                <td>
                                    @if($result['is_absent'])
                                        <span class="badge badge-warning">Absent</span>
                                    @else
                                        {{ $result['marks_obtained'] }}
                                    @endif
                                </td>
                                <td>{{ $result['total_marks'] }}</td>
                                <td>{{ number_format($result['percentage'], 2) }}%</td>
                                <td>{{ $result['grade'] }}</td>
                                <td>{{ number_format($result['gpa'], 2) }}</td>
                                <td>
                                    @if($result['passed'])
                                        <span class="badge badge-success">Pass</span>
                                    @else
                                        <span class="badge badge-danger">Fail</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold bg-light">
                            <td>Total</td>
                            <td>{{ $totalObtained }}</td>
                            <td>{{ $totalPossible }}</td>
                            <td>{{ number_format($totalPercentage, 2) }}%</td>
                            <td colspan="2">GPA: {{ number_format($gpaResult['gpa'], 2) }}</td>
                            <td>
                                @if($gpaResult['passed'])
                                    <span class="badge badge-success">Pass</span>
                                @else
                                    <span class="badge badge-danger">Fail</span>
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if(count($results) > 0)
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Performance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" role="progressbar" 
                             style="width: {{ $totalPercentage }}%;" 
                             aria-valuenow="{{ $totalPercentage }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($totalPercentage, 2) }}%
                        </div>
                    </div>
                    
                    <div class="alert alert-{{ $gpaResult['passed'] ? 'success' : 'danger' }}">
                        <h5>
                            <i class="fas fa-{{ $gpaResult['passed'] ? 'check-circle' : 'times-circle' }}"></i> 
                            Overall Result: {{ $gpaResult['passed'] ? 'PASSED' : 'FAILED' }}
                        </h5>
                        <p class="mb-0">
                            Total GPA: <strong>{{ number_format($gpaResult['gpa'], 2) }}</strong><br>
                            Total Credit Hours: <strong>{{ $gpaResult['total_credits'] }}</strong><br>
                            Total Grade Points: <strong>{{ number_format($gpaResult['grade_points'], 2) }}</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Remarks</h5>
                </div>
                <div class="card-body">
                    @php
                        $performanceMessage = "";
                        if ($totalPercentage >= 90) {
                            $performanceMessage = "Outstanding performance. Keep up the excellent work!";
                        } elseif ($totalPercentage >= 80) {
                            $performanceMessage = "Excellent performance. Well done!";
                        } elseif ($totalPercentage >= 70) {
                            $performanceMessage = "Very good performance. Keep it up!";
                        } elseif ($totalPercentage >= 60) {
                            $performanceMessage = "Good performance. Continue working hard!";
                        } elseif ($totalPercentage >= 50) {
                            $performanceMessage = "Satisfactory performance. There's room for improvement.";
                        } elseif ($totalPercentage >= 40) {
                            $performanceMessage = "Average performance. Need to work harder.";
                        } else {
                            $performanceMessage = "Below average performance. Significant improvement needed.";
                        }
                    @endphp
                    
                    <p>{{ $performanceMessage }}</p>
                    
                    @if(!$gpaResult['passed'])
                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle"></i> Improvement Needed</h6>
                            <p class="mb-0">Student needs to improve in the following subjects:</p>
                            <ul class="mb-0 mt-2">
                                @foreach($results as $result)
                                    @if(!$result['passed'])
                                        <li>{{ $result['subject']->name }} ({{ number_format($result['percentage'], 2) }}%)</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection 