@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Edit Attendance Record</h1>
        <div class="flex space-x-2">
            <a href="{{ route('attendances.show', $attendance) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancel
            </a>
            <a href="{{ route('attendances.index') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                All Attendances
            </a>
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

    <div class="bg-white shadow-md rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Student</h2>
                <p>{{ $attendance->student->name ?? 'N/A' }}</p>
                <p class="text-sm text-gray-500">{{ $attendance->student->student_id ?? 'N/A' }}</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Date</h2>
                <p>{{ $attendance->attendance_date->format('d M, Y') }}</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Class & Section</h2>
                <p>{{ $attendance->section->class->class_name ?? 'N/A' }} - {{ $attendance->section->section_name ?? 'N/A' }}</p>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Subject</h2>
                <p>{{ $attendance->subject->name ?? 'N/A' }}</p>
            </div>
        </div>

        <form action="{{ route('attendances.update', $attendance) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Present</option>
                        <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Absent</option>
                        <option value="late" {{ $attendance->status == 'late' ? 'selected' : '' }}>Late</option>
                        <option value="excused" {{ $attendance->status == 'excused' ? 'selected' : '' }}>Excused</option>
                        <option value="sick_leave" {{ $attendance->status == 'sick_leave' ? 'selected' : '' }}>Sick Leave</option>
                    </select>
                </div>
                
                <div>
                    <label for="check_in" class="block text-sm font-medium text-gray-700 mb-1">Check-in Time</label>
                    <input type="time" name="check_in" id="check_in" value="{{ old('check_in', $attendance->check_in ? $attendance->check_in->format('H:i') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <div>
                    <label for="check_out" class="block text-sm font-medium text-gray-700 mb-1">Check-out Time</label>
                    <input type="time" name="check_out" id="check_out" value="{{ old('check_out', $attendance->check_out ? $attendance->check_out->format('H:i') : '') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
            </div>
            
            <div class="mb-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea name="remarks" id="remarks" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('remarks', $attendance->remarks) }}</textarea>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Update Attendance Record
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 