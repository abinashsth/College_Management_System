@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Marks Dashboard</h1>
        @can('manage marks')
        <a href="{{ route('marks.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Add New Marks
        </a>
        @endcan
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Total Marks Entries</h3>
            <p class="text-3xl font-bold text-blue-600">{{ $totalMarks ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Recent Entries</h3>
            <p class="text-3xl font-bold text-green-600">{{ $recentMarks ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">Pending Reviews</h3>
            <p class="text-3xl font-bold text-yellow-600">{{ $pendingReviews ?? 0 }}</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('marks.create') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-plus-circle text-blue-500 mr-3"></i>
                    <span>Add New Marks</span>
                </a>
                <a href="{{ route('marks.index') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-list text-green-500 mr-3"></i>
                    <span>View All Marks</span>
                </a>
                <a href="{{ route('marks.report') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-chart-bar text-purple-500 mr-3"></i>
                    <span>Generate Report</span>
                </a>
                <a href="{{ route('student.grades') }}" class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-graduation-cap text-indigo-500 mr-3"></i>
                    <span>Student Grades</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Marks -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Recent Marks Entries</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentMarksList ?? [] as $mark)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $mark->student->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $mark->subject->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $mark->marks }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $mark->exam_date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('marks.edit', $mark) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">No recent marks entries</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection 