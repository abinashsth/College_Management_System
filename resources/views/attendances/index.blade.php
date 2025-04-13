@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Attendance Management</h1>
        @can('create attendance')
        <a href="{{ route('attendances.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Mark Attendance
        </a>
        @endcan
    </div>

    <!-- Search & Filter Form -->
    <div class="bg-white shadow-md rounded-lg mb-6 p-4">
        <form action="{{ route('attendances.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="section" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                <select name="section_id" id="section" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->section_name }} ({{ $section->class->class_name ?? 'No Class' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" id="date" value="{{ request('date', $today) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Statuses</option>
                    <option value="present" {{ request('status') == 'present' ? 'selected' : '' }}>Present</option>
                    <option value="absent" {{ request('status') == 'absent' ? 'selected' : '' }}>Absent</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Late</option>
                    <option value="excused" {{ request('status') == 'excused' ? 'selected' : '' }}>Excused</option>
                    <option value="sick_leave" {{ request('status') == 'sick_leave' ? 'selected' : '' }}>Sick Leave</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Filter
                </button>
                <a href="{{ route('attendances.index') }}" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Reset
                </a>
                <a href="{{ route('attendances.report') }}" class="ml-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Generate Report
                </a>
            </div>
        </form>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        @if($attendances->count() > 0)
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Student
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Class & Section
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subject
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($attendances as $attendance)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $attendance->student->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $attendance->student->student_id ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $attendance->section->class->class_name ?? 'N/A' }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $attendance->section->section_name ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $attendance->subject->name ?? 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $attendance->attendance_date->format('d M, Y') }}
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $attendance->check_in ? $attendance->check_in->format('h:i A') : 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                            $attendance->status == 'present' ? 'bg-green-100 text-green-800' : 
                            ($attendance->status == 'absent' ? 'bg-red-100 text-red-800' : 
                            ($attendance->status == 'late' ? 'bg-yellow-100 text-yellow-800' : 
                            ($attendance->status == 'excused' ? 'bg-blue-100 text-blue-800' : 
                            'bg-purple-100 text-purple-800'))) }}">
                            {{ ucfirst($attendance->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('attendances.show', $attendance) }}" class="text-indigo-600 hover:text-indigo-900">
                                View
                            </a>
                            @can('edit attendance')
                            <a href="{{ route('attendances.edit', $attendance) }}" class="text-amber-600 hover:text-amber-900">
                                Edit
                            </a>
                            @endcan
                            @can('delete attendance')
                            <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this attendance record?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Delete
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4">
            {{ $attendances->links() }}
        </div>
        @else
        <div class="px-6 py-4 text-center text-gray-500">
            No attendance records found for the selected criteria.
        </div>
        @endif
    </div>
</div>
@endsection 