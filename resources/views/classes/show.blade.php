@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Class Details: {{ $class->class_name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('classes.edit', $class->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-md hover:bg-yellow-600">
                Edit Class
            </a>
            <a href="{{ route('classes.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                Back to Classes
            </a>
        </div>
    </div>

    <!-- Class Information -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h2 class="text-lg font-semibold text-gray-800">Class Information</h2>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Class Name</p>
                <p class="font-medium">{{ $class->class_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Academic Year</p>
                <p class="font-medium">{{ $class->academicYear->name ?? 'Not Assigned' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Department</p>
                <p class="font-medium">{{ $class->department->name ?? 'Not Assigned' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Program</p>
                <p class="font-medium">{{ $class->program->name ?? 'Not Assigned' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Capacity</p>
                <p class="font-medium">{{ $class->capacity }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <p class="font-medium">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                        {{ $class->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($class->status) }}
                    </span>
                </p>
            </div>
            <div class="col-span-2">
                <p class="text-sm text-gray-600">Description</p>
                <p class="font-medium">{{ $class->description ?? 'No description available' }}</p>
            </div>
        </div>
    </div>

    <!-- Sections Tab -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Sections</h2>
            <a href="{{ route('sections.create', ['class_id' => $class->id]) }}" class="bg-teal-600 text-white px-3 py-1 rounded text-sm hover:bg-teal-700">
                Add Section
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($class->sections as $section)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $section->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $section->teacher->name ?? 'Not Assigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $section->students_count ?? 0 }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('sections.edit', $section->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                <a href="{{ route('sections.show', $section->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No sections found for this class.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Students Tab -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Students</h2>
            <a href="{{ route('students.create', ['class_id' => $class->id]) }}" class="bg-teal-600 text-white px-3 py-1 rounded text-sm hover:bg-teal-700">
                Add Student
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Section</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($class->students as $student)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->full_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->registration_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->section->name ?? 'Not Assigned' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('students.show', $student->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                                <a href="{{ route('students.edit', $student->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No students found for this class.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Classroom Allocation -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Classroom Allocations</h2>
            <a href="{{ route('classroom-allocations.create', ['class_id' => $class->id]) }}" class="bg-teal-600 text-white px-3 py-1 rounded text-sm hover:bg-teal-700">
                Allocate Classroom
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($classroomAllocations) && count($classroomAllocations) > 0)
                        @foreach($classroomAllocations as $allocation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $allocation->room_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $allocation->day }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $allocation->start_time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $allocation->end_time }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('classroom-allocations.edit', $allocation->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                    <form action="{{ route('classroom-allocations.destroy', $allocation->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this allocation?')">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No classroom allocations found for this class.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Courses Tab -->
    <div class="bg-white rounded-lg shadow-md mb-6 overflow-hidden">
        <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Courses</h2>
            <a href="{{ route('classes.courses', $class->id) }}" class="bg-teal-600 text-white px-3 py-1 rounded text-sm hover:bg-teal-700">
                Manage Courses
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($class->courses as $course)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $course->code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $course->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $course->credit_hours }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $course->pivot->semester ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $course->pivot->year ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $course->pivot->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No courses assigned to this class. Click "Manage Courses" to add courses.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 