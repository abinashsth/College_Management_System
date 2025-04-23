@extends('layouts.app')

@section('title', $subject->name . ' - Subject Details')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $subject->name }}</h1>
        <div class="flex space-x-2 mt-4 md:mt-0">
            <a href="{{ route('subjects.edit', $subject) }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <i class="fas fa-edit mr-1"></i> Edit Subject
            </a>
            <a href="{{ route('subjects.classes', $subject) }}" class="px-4 py-2 bg-purple-600 text-white rounded shadow hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 transition">
                <i class="fas fa-layer-group mr-1"></i> Assign to Classes
            </a>
            <!-- <a href="{{ route('subjects.courses', $subject) }}" class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                <i class="fas fa-book mr-1"></i> Assign to Courses
            </a> -->
            <a href="{{ route('subjects.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Subjects
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Basic Subject Information -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h6 class="font-bold text-blue-600">Subject Information</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-y-3">
                    <div class="md:col-span-4 font-semibold text-gray-700">Subject Code:</div>
                    <div class="md:col-span-8">{{ $subject->code }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Subject Name:</div>
                    <div class="md:col-span-8">{{ $subject->name }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Department:</div>
                    <div class="md:col-span-8">
                        @if($subject->department)
                            <a href="{{ route('departments.show', $subject->department) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $subject->department->name }}
                            </a>
                        @else
                            <span class="text-gray-500">Not assigned</span>
                        @endif
                    </div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Type:</div>
                    <div class="md:col-span-8">
                        @if($subject->elective)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                Elective (Optional)
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                Core (Required)
                            </span>
                        @endif
                    </div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Credit Hours:</div>
                    <div class="md:col-span-8">{{ $subject->credit_hours }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Level:</div>
                    <div class="md:col-span-8">{{ ucfirst($subject->level ?? 'Not specified') }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Duration Type:</div>
                    <div class="md:col-span-8">{{ ucfirst($subject->duration_type ?? 'Semester') }} Based</div>
                    
                    @if(isset($subject->duration_type) && $subject->duration_type == 'year')
                    <div class="md:col-span-4 font-semibold text-gray-700">Year:</div>
                    <div class="md:col-span-8">Year {{ $subject->year ?? 'Not specified' }}</div>
                    @else
                    <div class="md:col-span-4 font-semibold text-gray-700">Semester:</div>
                    <div class="md:col-span-8">{{ ucfirst($subject->semester_offered ?? 'Not specified') }}</div>
                    @endif
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Status:</div>
                    <div class="md:col-span-8">
                        @if($subject->status == 'active')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Subject Information -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h6 class="font-bold text-blue-600">Additional Details</h6>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-y-3">
                    <div class="md:col-span-4 font-semibold text-gray-700">Lecture Hours:</div>
                    <div class="md:col-span-8">{{ $subject->lecture_hours ?? 'Not specified' }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Practical Hours:</div>
                    <div class="md:col-span-8">{{ $subject->practical_hours ?? 'Not specified' }}</div>
                    
                 
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Total Hours:</div>
                    <div class="md:col-span-8">{{ ($subject->lecture_hours ?? 0) + ($subject->practical_hours ?? 0) + ($subject->tutorial_hours ?? 0) }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Created:</div>
                    <div class="md:col-span-8">{{ $subject->created_at->format('d M Y H:i') }}</div>
                    
                    <div class="md:col-span-4 font-semibold text-gray-700">Last Updated:</div>
                    <div class="md:col-span-8">{{ $subject->updated_at->format('d M Y H:i') }}</div>
                </div>
                
                <div class="mt-4">
                    <p class="font-semibold text-gray-700">Description:</p>
                    <p class="mt-1 text-gray-600">{{ $subject->description ?? 'No description available.' }}</p>
                </div>
                
                <div class="mt-4">
                    <p class="font-semibold text-gray-700">Learning Outcomes:</p>
                    <p class="mt-1 text-gray-600">{{ $subject->learning_objectives ?? 'No learning outcomes specified.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6"> -->
        <!-- Programs this subject is part of -->
        <!-- <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h6 class="font-bold text-blue-600">Programs</h6>
                <a href="{{ route('subjects.courses', $subject) }}" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <i class="fas fa-plus-circle mr-1"></i> Manage Programs
                </a>
            </div>
            <div class="p-6">
                @if($subject->programs->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subject->programs as $program)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $program->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $program->department->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('programs.show', $program) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-gray-500">This subject is not assigned to any programs.</p>
                @endif
            </div>
        </div> -->

        <!-- Prerequisites for this subject -->
        <!-- <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="border-b border-gray-200 px-6 py-4">
                <h6 class="font-bold text-blue-600">Prerequisites</h6>
            </div>
            <div class="p-6">
                @if($subject->prerequisites->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($subject->prerequisites as $prerequisite)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $prerequisite->code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $prerequisite->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('subjects.show', $prerequisite) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-gray-500">This subject has no prerequisites.</p>
                @endif
            </div>
        </div>
    </div> -->

    <!-- Teachers assigned to this subject -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h6 class="font-bold text-blue-600">Teachers</h6>
            <a href="{{ route('subjects.teachers', $subject) }}" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                <i class="fas fa-plus-circle mr-1"></i> Assign Teachers
            </a>
        </div>
        <div class="p-6">
            @if($subject->teachers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subject->teachers as $teacher)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $teacher->department->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('teachers.show', $teacher) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500">No teachers are assigned to this subject.</p>
            @endif
        </div>
    </div>

    <!-- Classes this subject is taught in -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h6 class="font-bold text-blue-600">Classes</h6>
            <a href="{{ route('subjects.classes', $subject) }}" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                <i class="fas fa-plus-circle mr-1"></i> Assign to Classes
            </a>
        </div>
        <div class="p-6">
            @if($subject->classes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subject->classes as $class)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->class_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->department->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->program->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->pivot->semester }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->pivot->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($class->pivot->is_core)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Core</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Elective</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500">This subject is not assigned to any classes yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection 