@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Prerequisites: {{ $course->name }}</h1>
        <a href="{{ route('courses.show', $course) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
            Back to Course
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Course Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Course Code</p>
                    <p class="font-medium">{{ $course->code }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Course Name</p>
                    <p class="font-medium">{{ $course->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Credit Hours</p>
                    <p class="font-medium">{{ $course->credit_hours }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Department</p>
                    <p class="font-medium">{{ $course->department->name ?? 'Not Assigned' }}</p>
                </div>
            </div>

            <form action="{{ route('courses.prerequisites.update', $course) }}" method="POST" class="mt-4">
                @csrf
                
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Assign Prerequisites</h2>
                <div class="mb-5">
                    <p class="text-sm text-gray-500 mb-2">Select courses that should be prerequisites for this course and specify the requirement type and any notes.</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Select</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requirement Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($availableCourses as $index => $prereqCourse)
                                @php
                                    $isPrerequisite = $course->prerequisites->contains($prereqCourse->id);
                                    $pivotData = $isPrerequisite ? $course->prerequisites->find($prereqCourse->id)->pivot : null;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" 
                                               name="prerequisites[]" 
                                               value="{{ $prereqCourse->id }}" 
                                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                               {{ $isPrerequisite ? 'checked' : '' }}
                                               id="prerequisite_{{ $prereqCourse->id }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $prereqCourse->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $prereqCourse->code }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <select name="requirement_type[{{ $prereqCourse->id }}]" 
                                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <option value="required" {{ $pivotData && $pivotData->requirement_type == 'required' ? 'selected' : '' }}>Required</option>
                                            <option value="recommended" {{ $pivotData && $pivotData->requirement_type == 'recommended' ? 'selected' : '' }}>Recommended</option>
                                            <option value="optional" {{ $pivotData && $pivotData->requirement_type == 'optional' ? 'selected' : '' }}>Optional</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="text" 
                                               name="notes[{{ $prereqCourse->id }}]" 
                                               value="{{ old('notes.' . $prereqCourse->id, $pivotData ? $pivotData->notes : '') }}" 
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                               placeholder="Any special requirements or notes">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                        Save Prerequisites
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 