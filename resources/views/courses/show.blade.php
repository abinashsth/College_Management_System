@extends('layouts.app')

@section('title', 'Course: ' . $course->code . ' - ' . $course->name)

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Course: {{ $course->code }}</h1>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('courses.edit', $course) }}" class="px-4 py-2 bg-yellow-600 text-white rounded shadow hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 transition">
                <i class="fas fa-edit mr-1"></i> Edit Course
            </a>
            <a href="{{ route('courses.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Courses
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

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded relative" role="alert">
            {{ session('error') }}
            <button type="button" class="absolute top-0 right-0 mt-4 mr-4" onclick="this.parentElement.remove()">
                <span class="text-red-700">&times;</span>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Course Details -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-blue-600 text-white px-6 py-4">
                    <h2 class="text-xl font-semibold">{{ $course->name }}</h2>
                    <p class="text-blue-100">{{ $course->code }}</p>
                </div>
                <div class="p-6">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Description</h3>
                        <p class="text-gray-600">{{ $course->description ?? 'No description available.' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Department -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Department</h4>
                            <p class="text-gray-600">
                                @if($course->department)
                                    <a href="{{ route('departments.show', $course->department) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $course->department->name }} ({{ $course->department->code }})
                                    </a>
                                @else
                                    No department assigned
                                @endif
                            </p>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Status</h4>
                            <p>
                                @if($course->status === 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @elseif($course->status === 'inactive')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Inactive
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Archived
                                    </span>
                                @endif
                            </p>
                        </div>
                        
                        <!-- Type -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Type</h4>
                            <p class="text-gray-600">{{ ucfirst($course->type ?? 'Not specified') }}</p>
                        </div>
                        
                        <!-- Level -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Level</h4>
                            <p class="text-gray-600">{{ ucfirst($course->level ?? 'Not specified') }}</p>
                        </div>
                        
                        <!-- Credit Hours -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Credit Hours</h4>
                            <p class="text-gray-600">{{ $course->credit_hours }}</p>
                        </div>
                        
                        <!-- Total Hours -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Total Hours</h4>
                            <p class="text-gray-600">{{ $course->getTotalHoursAttribute() }}</p>
                        </div>
                        
                        <!-- Lecture Hours -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Lecture Hours</h4>
                            <p class="text-gray-600">{{ $course->lecture_hours ?? 0 }}</p>
                        </div>
                        
                        <!-- Lab Hours -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Lab Hours</h4>
                            <p class="text-gray-600">{{ $course->lab_hours ?? 0 }}</p>
                        </div>
                        
                        <!-- Tutorial Hours -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Tutorial Hours</h4>
                            <p class="text-gray-600">{{ $course->tutorial_hours ?? 0 }}</p>
                        </div>
                        
                        <!-- Created At -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Created</h4>
                            <p class="text-gray-600">{{ $course->created_at->format('M d, Y') }}</p>
                        </div>
                        
                        <!-- Updated At -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-700">Last Updated</h4>
                            <p class="text-gray-600">{{ $course->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    @if($course->learning_outcomes)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Learning Outcomes</h3>
                            <div class="text-gray-600">
                                {!! nl2br(e($course->learning_outcomes)) !!}
                            </div>
                        </div>
                    @endif
                    
                    @if($course->evaluation_criteria)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Evaluation Criteria</h3>
                            <div class="text-gray-600">
                                {!! nl2br(e($course->evaluation_criteria)) !!}
                            </div>
                        </div>
                    @endif
                    
                    @if($course->syllabus)
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Syllabus</h3>
                            <div class="text-gray-600">
                                {!! nl2br(e($course->syllabus)) !!}
                            </div>
                        </div>
                    @endif
                </div>
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between">
                    <div>
                        <a href="{{ route('courses.edit', $course) }}" class="text-blue-600 hover:text-blue-900">
                            <i class="fas fa-edit mr-1"></i> Edit Course
                        </a>
                    </div>
                    <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 bg-transparent border-0 p-0 focus:outline-none">
                            <i class="fas fa-trash mr-1"></i> Delete Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar - Course Relationships -->
        <div>
            <!-- Programs -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <div class="bg-green-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Programs</h2>
                    <a href="{{ route('courses.programs', $course) }}" class="text-white hover:text-green-100">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if($course->programs->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($course->programs as $program)
                                <li class="py-2">
                                    <a href="{{ route('programs.show', $program) }}" class="flex items-start">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                                {{ $program->name }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ $program->code }} 
                                                @if($program->pivot->semester)
                                                    - Semester {{ $program->pivot->semester }}
                                                @endif
                                                @if($program->pivot->year)
                                                    / Year {{ $program->pivot->year }}
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                @if($program->pivot->is_elective)
                                                    <span class="text-orange-600">Elective</span>
                                                @else
                                                    <span class="text-green-600">Core</span>
                                                @endif
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 text-center">
                            <a href="{{ route('courses.programs', $course) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                Manage Program Assignments
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-2">This course is not assigned to any programs.</p>
                        <div class="mt-4 text-center">
                            <a href="{{ route('courses.programs', $course) }}" class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition text-sm">
                                <i class="fas fa-plus mr-1"></i> Assign to Programs
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Prerequisites -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
                <div class="bg-indigo-600 text-white px-6 py-4 flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Prerequisites</h2>
                    <a href="{{ route('courses.prerequisites', $course) }}" class="text-white hover:text-indigo-100">
                        <i class="fas fa-edit"></i>
                    </a>
                </div>
                <div class="p-6">
                    @if($course->prerequisites->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($course->prerequisites as $prerequisite)
                                <li class="py-2">
                                    <a href="{{ route('courses.show', $prerequisite) }}" class="flex items-start">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                                {{ $prerequisite->name }} ({{ $prerequisite->code }})
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ ucfirst($prerequisite->pivot->requirement_type ?? 'Required') }}
                                            </p>
                                            @if($prerequisite->pivot->notes)
                                                <p class="text-xs text-gray-500">
                                                    {{ $prerequisite->pivot->notes }}
                                                </p>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 text-center">
                            <a href="{{ route('courses.prerequisites', $course) }}" class="text-sm text-blue-600 hover:text-blue-900">
                                Manage Prerequisites
                            </a>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-2">This course has no prerequisites.</p>
                        <div class="mt-4 text-center">
                            <a href="{{ route('courses.prerequisites', $course) }}" class="px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Prerequisites
                            </a>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Prerequisite For -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="bg-amber-600 text-white px-6 py-4">
                    <h2 class="text-lg font-semibold">Prerequisite For</h2>
                </div>
                <div class="p-6">
                    @if($course->prerequisiteFor->count() > 0)
                        <ul class="divide-y divide-gray-200">
                            @foreach($course->prerequisiteFor as $course)
                                <li class="py-2">
                                    <a href="{{ route('courses.show', $course) }}" class="flex items-start">
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                                {{ $course->name }} ({{ $course->code }})
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                {{ ucfirst($course->pivot->requirement_type ?? 'Required') }}
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500 text-center py-2">This course is not a prerequisite for any other courses.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 