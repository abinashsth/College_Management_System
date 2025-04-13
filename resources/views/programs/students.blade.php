@extends('layouts.app')

@section('title', $program->name . ' - Students')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Students Enrolled in {{ $program->name }} ({{ $program->code }})</h1>
        <div class="mt-4 md:mt-0 flex space-x-2">
            <a href="{{ route('programs.show', $program) }}" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <i class="fas fa-eye mr-1"></i> View Program
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

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded relative" role="alert">
            {{ session('error') }}
            <button type="button" class="absolute top-0 right-0 mt-4 mr-4" onclick="this.parentElement.remove()">
                <span class="text-red-700">&times;</span>
            </button>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h6 class="font-bold text-blue-600">Enrolled Students</h6>
            <div class="flex items-center">
                <!-- Search Form if needed -->
                <!-- <form action="{{ route('programs.students', $program) }}" method="GET" class="mr-2">
                    <div class="relative">
                        <input type="search" name="search" placeholder="Search students..." class="pr-10 pl-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" value="{{ request('search') }}">
                        <button type="submit" class="absolute right-0 top-0 mt-2 mr-3">
                            <i class="fas fa-search text-gray-400"></i>
                        </button>
                    </div>
                </form> -->
                <!-- Add Student Button if needed -->
                <!-- <a href="#" class="px-4 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                    <i class="fas fa-plus mr-1"></i> Add Student
                </a> -->
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($students->count() > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($students as $student)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $student->student_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $student->user->name ?? $student->name }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->user->email ?? $student->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ isset($student->pivot->enrollment_date) ? date('M d, Y', strtotime($student->pivot->enrollment_date)) : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($student->status === 'active' || (isset($student->pivot->status) && $student->pivot->status === 'active'))
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="{{ route('students.show', $student) }}" class="text-blue-600 hover:text-blue-800 mr-2">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- Add other action buttons as needed -->
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4">
                    {{ $students->links() }}
                </div>
            @else
                <div class="px-6 py-4 text-center text-gray-500">
                    No students are enrolled in this program yet.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 