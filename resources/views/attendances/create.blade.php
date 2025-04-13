@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Mark Attendance</h1>
        <a href="{{ route('attendances.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to List
        </a>
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
        <form action="{{ route('attendances.mark') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                    <select name="section_id" id="section_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
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
                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="attendance_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="attendance_date" id="attendance_date" value="{{ old('attendance_date', $today) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Continue to Mark Attendance
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 