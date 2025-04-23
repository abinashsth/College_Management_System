@extends('layouts.app')

@section('title', $program->name)

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $program->name }} ({{ $program->code }})</h1>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('programs.edit', $program) }}" class="px-4 py-2 bg-yellow-600 text-white rounded shadow hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition">
                <i class="fas fa-edit mr-1"></i> Edit Program
            </a>
            <a href="{{ route('programs.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Programs
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded relative" role="alert">
            {{ session('success') }}
            <button type="button" class="absolute top-0 right-0 mt-4 mr-4" onclick="this.parentElement.remove()">
                <span class="text-green-700">&times;</span>
            </button>
        </div>
    @endif

    <!-- Program Overview -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Program Overview</h6>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Department</span>
                        <span class="block text-base text-gray-800">
                            @if($program->department)
                                <a href="{{ route('departments.show', $program->department) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $program->department->name }}
                                </a>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Degree Level</span>
                        <span class="block text-base text-gray-800">{{ $program->degree_level ?? 'Not specified' }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Duration</span>
                        <span class="block text-base text-gray-800">{{ $program->duration }} {{ Str::title($program->duration_unit) }}</span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Credit Hours</span>
                        <span class="block text-base text-gray-800">{{ $program->credit_hours }}</span>
                    </div>
                </div>
                
                <div>
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Program Coordinator</span>
                        <span class="block text-base text-gray-800">
                            @if($program->coordinator)
                                <a href="{{ route('users.show', $program->coordinator) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ $program->coordinator->name }}
                                </a>
                            @else
                                <span class="text-gray-500">Not assigned</span>
                            @endif
                        </span>
                    </div>
                    
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Status</span>
                        <span class="block text-base">
                            @if($program->status)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </span>
                    </div>
                    
                    @if($program->start_date)
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">Start Date</span>
                        <span class="block text-base text-gray-800">{{ $program->start_date->format('M d, Y') }}</span>
                    </div>
                    @endif
                    
                    @if($program->end_date)
                    <div class="mb-4">
                        <span class="block text-sm font-medium text-gray-500">End Date</span>
                        <span class="block text-base text-gray-800">{{ $program->end_date->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($program->description)
            <div class="mt-6 border-t border-gray-200 pt-6">
                <span class="block text-sm font-medium text-gray-500 mb-2">Description</span>
                <p class="text-gray-800">{{ $program->description }}</p>
            </div>
            @endif

            @if($program->admission_requirements)
            <div class="mt-6 border-t border-gray-200 pt-6">
                <span class="block text-sm font-medium text-gray-500 mb-2">Admission Requirements</span>
                <div class="prose text-gray-800">
                    {!! nl2br(e($program->admission_requirements)) !!}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Program Statistics -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Program Statistics</h6>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-lg p-6 text-center">
                    <span class="text-4xl font-bold text-blue-600">{{ $program->students->count() }}</span>
                    <p class="text-gray-600 mt-2">Students Enrolled</p>
                </div>
                <div class="bg-green-50 rounded-lg p-6 text-center">
                    <span class="text-4xl font-bold text-green-600">{{ $program->courses->count() }}</span>
                    <p class="text-gray-600 mt-2">Courses</p>
                </div>
                <div class="bg-purple-50 rounded-lg p-6 text-center">
                    <span class="text-4xl font-bold text-purple-600">{{ $program->credit_hours }}</span>
                    <p class="text-gray-600 mt-2">Credit Hours</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses -->
    <!-- <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h6 class="font-bold text-blue-600">Courses</h6>
            <a href="{{ route('programs.courses', $program) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                Manage Courses <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($program->courses->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester/Year</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Hours</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($program->courses->take(5) as $course)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('courses.show', $course) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $course->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->pivot->semester ?? 'N/A' }}
                                    @if($course->pivot->year)
                                        / Year {{ $course->pivot->year }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $course->credit_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($course->pivot && $course->pivot->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($program->courses->count() > 5)
                    <div class="px-6 py-4 border-t border-gray-200">
                        <a href="{{ route('programs.courses', $program) }}" class="text-blue-600 hover:underline">
                            View all {{ $program->courses->count() }} courses
                        </a>
                    </div>
                @endif
            @else
                <div class="px-6 py-4 text-center text-gray-500">
                    No courses have been added to this program yet.
                </div>
            @endif
        </div>
    </div> -->

    <!-- Students -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h6 class="font-bold text-blue-600">Students</h6>
            <a href="{{ route('programs.students', $program) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                View All Students <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($program->students->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($program->students->take(5) as $student)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->student_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $student->user->name ?? $student->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->user->email ?? $student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($student->status === 'active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @elseif($student->status === 'graduated')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Graduated
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($program->students->count() > 5)
                    <div class="px-6 py-4 border-t border-gray-200">
                        <a href="{{ route('programs.students', $program) }}" class="text-blue-600 hover:underline">
                            View all {{ $program->students->count() }} students
                        </a>
                    </div>
                @endif
            @else
                <div class="px-6 py-4 text-center text-gray-500">
                    No students are currently enrolled in this program.
                </div>
            @endif
        </div>
    </div>

    @if($program->curriculum)
    <!-- Curriculum -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Curriculum</h6>
        </div>
        <div class="p-6">
            <div class="prose max-w-none text-gray-800">
                {!! nl2br(e($program->curriculum)) !!}
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="flex justify-end space-x-4 mt-8">
        <a href="{{ route('programs.edit', $program) }}" class="px-6 py-2 bg-yellow-600 text-white rounded shadow hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition">
            <i class="fas fa-edit mr-1"></i> Edit Program
        </a>
        <form action="{{ route('programs.destroy', $program) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this program? All associated data will be lost.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                <i class="fas fa-trash mr-1"></i> Delete Program
            </button>
        </form>
    </div>
</div>
@endsection 