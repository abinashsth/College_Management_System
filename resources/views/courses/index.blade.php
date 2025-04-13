@extends('layouts.app')

@section('title', 'Course Management')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Course Management</h1>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('courses.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <i class="fas fa-plus mr-1"></i> Add Course
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

    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="bg-gray-50 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-700">Filter Courses</h2>
        </div>
        <div class="p-6 border-t border-gray-200">
            <form action="{{ route('courses.index') }}" method="GET" class="flex flex-wrap -mx-2">
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select id="department_id" name="department_id" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }} ({{ $department->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select id="status" name="status" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                    <select id="level" name="level" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Levels</option>
                        <option value="beginner" {{ request('level') == 'beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="advanced" {{ request('level') == 'advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                
                <div class="w-full md:w-1/4 px-2 mb-4">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or code" class="border border-gray-300 rounded-md shadow-sm py-2 px-3 w-full focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div class="w-full px-2 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('courses.index') }}" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                        <i class="fas fa-sync mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            @if($courses->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Hours</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($courses as $course)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $course->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('courses.show', $course) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $course->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($course->department)
                                        <a href="{{ route('departments.show', $course->department) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $course->department->name }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">Not assigned</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $course->credit_hours }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('courses.show', $course) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('courses.edit', $course) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="px-6 py-4 text-center text-gray-500">
                    No courses found. 
                    <a href="{{ route('courses.create') }}" class="text-blue-500 hover:underline">Create a new course</a>.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 