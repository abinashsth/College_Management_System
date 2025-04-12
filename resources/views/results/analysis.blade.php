@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-8">
            <h2>Results Analysis</h2>
        </div>
        <div class="col-md-4 text-right">
            <a href="{{ route('results.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Results
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Select Class and Exam</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('results.analysis') }}" method="GET" class="form-inline">
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
                
                <a href="{{ route('results.analysis') }}" class="btn btn-secondary mb-2 ml-2">
                    <i class="fas fa-sync"></i> Reset
                </a>
            </form>
        </div>
    </div>

    @if($selectedClassId && $selectedExamId && isset($analysisData) && !empty($analysisData))
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Overview</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>Total Students</th>
                                    <td>{{ $analysisData['total_students'] }}</td>
                                </tr>
                                <tr>
                                    <th>Students Passed</th>
                                    <td>{{ $analysisData['passed_students'] }}</td>
                                </tr>
                                <tr>
                                    <th>Students Failed</th>
                                    <td>{{ $analysisData['failed_students'] }}</td>
                                </tr>
                                <tr>
                                    <th>Absent Students</th>
                                    <td>{{ $analysisData['absent_students'] }}</td>
                                </tr>
                                <tr>
                                    <th>Pass Percentage</th>
                                    <td>{{ number_format($analysisData['pass_percentage'], 2) }}%</td>
                                </tr>
                            </table>
                        </div>

                        <div class="progress mt-3">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $analysisData['pass_percentage'] }}%;" 
                                 aria-valuenow="{{ $analysisData['pass_percentage'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ number_format($analysisData['pass_percentage'], 2) }}%
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Performance Distribution</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Subject-wise Performance</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Pass Count</th>
                                        <th>Fail Count</th>
                                        <th>Average Marks</th>
                                        <th>Pass Percentage</th>
                                        <th>Performance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($analysisData['subject_performance'] as $subject)
                                        <tr>
                                            <td>{{ $subject['subject']->name }}</td>
                                            <td>{{ $subject['pass_count'] }}</td>
                                            <td>{{ $subject['fail_count'] }}</td>
                                            <td>{{ number_format($subject['avg_marks'], 2) }}</td>
                                            <td>{{ number_format($subject['pass_percentage'], 2) }}%</td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar bg-{{ $subject['pass_percentage'] >= 70 ? 'success' : ($subject['pass_percentage'] >= 40 ? 'warning' : 'danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $subject['pass_percentage'] }}%;" 
                                                         aria-valuenow="{{ $subject['pass_percentage'] }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($subject['pass_percentage'], 2) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Performance Insights</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Pass/Fail Distribution</h6>
                                <canvas id="passFailChart" width="300" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6>Subject-wise Average Marks</h6>
                                <canvas id="subjectAvgMarksChart" width="300" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif($selectedClassId || $selectedExamId)
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> Please select both Class and Exam to view analysis.
        </div>
    @else
        <div class="alert alert-info mt-4">
            <i class="fas fa-info-circle"></i> Use the filters above to view result analysis.
        </div>
    @endif
</div>

@if($selectedClassId && $selectedExamId && isset($analysisData) && !empty($analysisData))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Performance Distribution Chart
        var performanceCtx = document.getElementById('performanceChart').getContext('2d');
        var performanceData = {
            labels: ['90% and above', '80% - 89%', '70% - 79%', '60% - 69%', '50% - 59%', '40% - 49%', 'Below 40%'],
            datasets: [{
                label: 'Number of Students',
                data: [
                    {{ $analysisData['passing_thresholds']['above_90'] }},
                    {{ $analysisData['passing_thresholds']['above_80'] }},
                    {{ $analysisData['passing_thresholds']['above_70'] }},
                    {{ $analysisData['passing_thresholds']['above_60'] }},
                    {{ $analysisData['passing_thresholds']['above_50'] }},
                    {{ $analysisData['passing_thresholds']['above_40'] }},
                    {{ $analysisData['passing_thresholds']['below_40'] }}
                ],
                backgroundColor: [
                    'rgba(0, 123, 255, 0.5)',
                    'rgba(23, 162, 184, 0.5)',
                    'rgba(40, 167, 69, 0.5)',
                    'rgba(255, 193, 7, 0.5)',
                    'rgba(253, 126, 20, 0.5)',
                    'rgba(220, 53, 69, 0.5)',
                    'rgba(108, 117, 125, 0.5)'
                ],
                borderColor: [
                    'rgba(0, 123, 255, 1)',
                    'rgba(23, 162, 184, 1)',
                    'rgba(40, 167, 69, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(253, 126, 20, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        };

        var performanceChart = new Chart(performanceCtx, {
            type: 'bar',
            data: performanceData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Student Performance Distribution'
                    }
                }
            }
        });

        // Pass/Fail Chart
        var passFailCtx = document.getElementById('passFailChart').getContext('2d');
        var passFailData = {
            labels: ['Passed', 'Failed', 'Absent'],
            datasets: [{
                data: [
                    {{ $analysisData['passed_students'] }},
                    {{ $analysisData['failed_students'] }},
                    {{ $analysisData['absent_students'] }}
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.5)',
                    'rgba(220, 53, 69, 0.5)',
                    'rgba(108, 117, 125, 0.5)'
                ],
                borderColor: [
                    'rgba(40, 167, 69, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(108, 117, 125, 1)'
                ],
                borderWidth: 1
            }]
        };

        var passFailChart = new Chart(passFailCtx, {
            type: 'pie',
            data: passFailData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Pass/Fail Distribution'
                    }
                }
            }
        });

        // Subject Average Marks Chart
        var subjectLabels = [];
        var subjectData = [];

        @foreach($analysisData['subject_performance'] as $subject)
            subjectLabels.push('{{ $subject['subject']->name }}');
            subjectData.push({{ $subject['avg_marks'] }});
        @endforeach

        var subjectAvgCtx = document.getElementById('subjectAvgMarksChart').getContext('2d');
        var subjectAvgData = {
            labels: subjectLabels,
            datasets: [{
                label: 'Average Marks',
                data: subjectData,
                backgroundColor: 'rgba(23, 162, 184, 0.5)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 1
            }]
        };

        var subjectAvgChart = new Chart(subjectAvgCtx, {
            type: 'bar',
            data: subjectAvgData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Subject-wise Average Marks'
                    }
                }
            }
        });
    });
</script>
@endpush
@endif
@endsection 