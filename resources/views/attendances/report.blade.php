@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Attendance Report</h1>
        <div class="flex space-x-2">
            <a href="{{ route('attendances.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
        </div>
    </div>

    <!-- Report Filter Form -->
    <div class="bg-white shadow-md rounded-lg mb-6 p-4">
        <form action="{{ route('attendances.report') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                <select name="section_id" id="section_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">Select Section</option>
                    @foreach($sections as $section)
                    <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                        {{ $section->section_name }} ({{ $section->class->class_name ?? 'No Class' }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Subject (Optional)</label>
                <select name="subject_id" id="subject_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="from_date" class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date', now()->subDays(30)->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>

            <div>
                <label for="to_date" class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date', now()->format('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
            </div>

            <div class="flex items-end md:col-span-4">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                    Generate Report
                </button>
                <a href="{{ route('attendances.report') }}" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Reset
                </a>
                @if(isset($attendanceData))
                <a href="{{ route('attendances.report.export', request()->all()) }}" class="ml-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Export to Excel
                </a>
                <a href="{{ route('attendances.report.pdf', request()->all()) }}" class="ml-2 bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Export to PDF
                </a>
                @endif
            </div>
        </form>
    </div>

    @if(isset($attendanceData))
    <!-- Overall Statistics -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Overall Statistics</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-green-50 p-4 rounded-lg">
                    <div class="text-green-500 text-xl font-bold">{{ number_format($overall['present_percentage'], 1) }}%</div>
                    <div class="text-sm text-gray-700">Present</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $overall['present'] }} / {{ $overall['total_students'] * $overall['total_days'] }}</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg">
                    <div class="text-red-500 text-xl font-bold">{{ number_format($overall['absent_percentage'], 1) }}%</div>
                    <div class="text-sm text-gray-700">Absent</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $overall['absent'] }} / {{ $overall['total_students'] * $overall['total_days'] }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="text-yellow-500 text-xl font-bold">{{ number_format($overall['late_percentage'], 1) }}%</div>
                    <div class="text-sm text-gray-700">Late</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $overall['late'] }} / {{ $overall['total_students'] * $overall['total_days'] }}</div>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="text-blue-500 text-xl font-bold">{{ number_format($overall['excused_percentage'], 1) }}%</div>
                    <div class="text-sm text-gray-700">Excused</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $overall['excused'] }} / {{ $overall['total_students'] * $overall['total_days'] }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <div class="text-purple-500 text-xl font-bold">{{ number_format($overall['sick_leave_percentage'], 1) }}%</div>
                    <div class="text-sm text-gray-700">Sick Leave</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $overall['sick_leave'] }} / {{ $overall['total_students'] * $overall['total_days'] }}</div>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-500">
                Total Students: {{ $overall['total_students'] }} | Total Days: {{ $overall['total_days'] }}
            </div>
        </div>
    </div>

    <!-- Student-wise Attendance -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Student-wise Attendance</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50">
                            Student
                        </th>
                        @foreach($dates as $date)
                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ \Carbon\Carbon::parse($date)->format('d M') }}
                        </th>
                        @endforeach
                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Present %
                        </th>
                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Absent %
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($students as $student_id => $data)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $data['student']->name }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $data['student']->student_id }}
                            </div>
                        </td>
                        @foreach($dates as $date)
                        <td class="px-3 py-4 whitespace-nowrap text-center">
                            @if(isset($data['dates'][$date]))
                                @if($data['dates'][$date]['status'] === 'present')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-800">P</span>
                                @elseif($data['dates'][$date]['status'] === 'absent')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-800">A</span>
                                @elseif($data['dates'][$date]['status'] === 'late')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-yellow-100 text-yellow-800">L</span>
                                @elseif($data['dates'][$date]['status'] === 'excused')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-800">E</span>
                                @elseif($data['dates'][$date]['status'] === 'sick_leave')
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-purple-100 text-purple-800">S</span>
                                @else
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-800">-</span>
                                @endif
                            @else
                                <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-gray-100 text-gray-800">-</span>
                            @endif
                        </td>
                        @endforeach
                        <td class="px-3 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ number_format($data['present_percentage'], 1) }}%
                            </span>
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ number_format($data['absent_percentage'], 1) }}%
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white shadow-md rounded-lg p-6 text-center text-gray-500">
        Please select a section and date range to generate the attendance report.
    </div>
    @endif
</div>
@endsection 