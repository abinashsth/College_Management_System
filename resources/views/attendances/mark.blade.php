@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Mark Attendance</h1>
        <div class="flex space-x-2">
            <a href="{{ route('attendances.create') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back
            </a>
            <a href="{{ route('attendances.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                All Attendances
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Section</h2>
                <p>{{ $section->section_name }} ({{ $section->class->class_name ?? 'No Class' }})</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Subject</h2>
                <p>{{ $subject ? $subject->name : 'All Subjects' }}</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Date</h2>
                <p>{{ \Carbon\Carbon::parse($date)->format('d M, Y') }}</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Please fix the following errors:</p>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('attendances.store') }}" method="POST">
            @csrf
            <input type="hidden" name="section_id" value="{{ $section->id }}">
            <input type="hidden" name="subject_id" value="{{ $subject ? $subject->id : '' }}">
            <input type="hidden" name="attendance_date" value="{{ $date }}">

            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Student
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Remarks
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($students) > 0)
                        @foreach($students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $student->name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $student->student_id }}
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="student_ids[]" value="{{ $student->id }}">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <select name="statuses[]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="present" {{ isset($existingAttendances[$student->id]) && $existingAttendances[$student->id]->status == 'present' ? 'selected' : '' }}>Present</option>
                                    <option value="absent" {{ isset($existingAttendances[$student->id]) && $existingAttendances[$student->id]->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                    <option value="late" {{ isset($existingAttendances[$student->id]) && $existingAttendances[$student->id]->status == 'late' ? 'selected' : '' }}>Late</option>
                                    <option value="excused" {{ isset($existingAttendances[$student->id]) && $existingAttendances[$student->id]->status == 'excused' ? 'selected' : '' }}>Excused</option>
                                    <option value="sick_leave" {{ isset($existingAttendances[$student->id]) && $existingAttendances[$student->id]->status == 'sick_leave' ? 'selected' : '' }}>Sick Leave</option>
                                </select>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" name="remarks[]" value="{{ isset($existingAttendances[$student->id]) ? $existingAttendances[$student->id]->remarks : '' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Optional remarks">
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                                No students found in this class.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if(count($students) > 0)
            <div class="px-6 py-4 bg-gray-50 text-right">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Save Attendance
                </button>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection 