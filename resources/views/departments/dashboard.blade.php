@extends('layouts.app')

@section('title', $department->name . ' Dashboard')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $department->name }} Dashboard</h1>
        <div class="flex space-x-3">
            <a href="{{ route('departments.show', $department) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-eye mr-2"></i> View Department
            </a>
            <a href="{{ route('departments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Departments
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Total Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['students_count'] ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <p class="text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ $stats['students_growth'] ?? '0' }}%
                    </p>
                    <p class="text-gray-500">vs last semester</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Faculty Members</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $department->teachers->count() }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <p class="text-gray-500">
                        <i class="fas fa-minus mr-1"></i>
                        {{ $stats['teachers_growth'] ?? '0' }}%
                    </p>
                    <p class="text-gray-500">vs last semester</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Active Courses</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['courses_count'] ?? 0 }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <p class="text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>
                        {{ $stats['courses_growth'] ?? '0' }}%
                    </p>
                    <p class="text-gray-500">vs last semester</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-500 mr-4">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Programs</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $department->programs->count() }}</p>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex justify-between text-sm">
                    <p class="text-gray-500">
                        <i class="fas fa-equals mr-1"></i>
                        {{ $stats['programs_growth'] ?? '0' }}%
                    </p>
                    <p class="text-gray-500">vs last year</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Demographics -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Student Demographics</h3>
            </div>
            <div class="p-6">
                <div class="h-80 flex items-center justify-center">
                    <canvas id="studentDemographicsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Faculty Distribution -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Faculty Composition</h3>
            </div>
            <div class="p-6">
                <div class="h-80 flex items-center justify-center">
                    <canvas id="facultyCompositionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
        <!-- Course Performance -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Course Performance</h3>
            </div>
            <div class="p-6">
                <div class="h-80 flex items-center justify-center">
                    <canvas id="coursePerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-4">
                    @forelse($activities as $activity)
                        <li class="border-b border-gray-100 pb-3">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-1">
                                    <i class="fas {{ $activity->icon ?? 'fa-bell' }} text-blue-500"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium">{{ $activity->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="text-center text-gray-500 py-4">
                            No recent activities found
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Upcoming Events -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Upcoming Events</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    View All
                </a>
            </div>
            <div class="p-6">
                <ul class="divide-y divide-gray-200">
                    @forelse($events as $event)
                        <li class="py-3">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-indigo-100 rounded flex flex-col items-center justify-center mr-4">
                                    <span class="text-xs font-bold text-indigo-600">{{ \Carbon\Carbon::parse($event->date)->format('M') }}</span>
                                    <span class="text-lg font-bold text-indigo-800">{{ \Carbon\Carbon::parse($event->date)->format('d') }}</span>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $event->title }}</p>
                                    <p class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($event->date)->format('D, M d, Y') }}</p>
                                    <p class="text-sm text-gray-500 mt-1">{{ $event->location }}</p>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-gray-500">
                            No upcoming events scheduled
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Teacher Performance -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Teacher Performance</h3>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    View Details
                </a>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($teacherPerformance as $teacher)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-100">
                                                @if($teacher->user->profile_photo_path)
                                                    <img class="h-8 w-8 rounded-full" src="{{ asset('storage/' . $teacher->user->profile_photo_path) }}" alt="{{ $teacher->user->name }}">
                                                @else
                                                    <div class="h-8 w-8 rounded-full flex items-center justify-center bg-blue-100 text-blue-500">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900">{{ $teacher->user->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $teacher->courses_count }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $teacher->students_count }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="mr-2 text-sm font-medium text-gray-900">{{ $teacher->rating }}</div>
                                            <div class="flex text-yellow-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $teacher->rating)
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $teacher->rating)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                        No teacher performance data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Student Demographics Chart
        const studentDemographicsCtx = document.getElementById('studentDemographicsChart').getContext('2d');
        new Chart(studentDemographicsCtx, {
            type: 'bar',
            data: {
                labels: @json($charts['student_demographics']['labels'] ?? ['Undergraduate', 'Graduate', 'PhD', 'Certificate']),
                datasets: [{
                    label: 'Number of Students',
                    data: @json($charts['student_demographics']['data'] ?? [120, 45, 15, 30]),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(153, 102, 255, 0.7)',
                        'rgba(255, 159, 64, 0.7)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Faculty Composition Chart
        const facultyCompositionCtx = document.getElementById('facultyCompositionChart').getContext('2d');
        new Chart(facultyCompositionCtx, {
            type: 'doughnut',
            data: {
                labels: @json($charts['faculty_composition']['labels'] ?? ['Professors', 'Associate Professors', 'Assistant Professors', 'Lecturers']),
                datasets: [{
                    label: 'Faculty Composition',
                    data: @json($charts['faculty_composition']['data'] ?? [4, 6, 9, 12]),
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)',
                        'rgba(75, 192, 192, 0.7)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    }
                }
            }
        });

        // Course Performance Chart
        const coursePerformanceCtx = document.getElementById('coursePerformanceChart').getContext('2d');
        new Chart(coursePerformanceCtx, {
            type: 'line',
            data: {
                labels: @json($charts['course_performance']['labels'] ?? ['2023 Fall', '2024 Spring', '2024 Summer', '2024 Fall', '2025 Spring']),
                datasets: [{
                    label: 'Average GPA',
                    data: @json($charts['course_performance']['data'] ?? [3.2, 3.4, 3.0, 3.5, 3.3]),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 2.0,
                        max: 4.0
                    }
                }
            }
        });
    });
</script>
@endsection 