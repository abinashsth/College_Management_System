@extends('layouts.app')

@section('title', $faculty->name . ' Dashboard')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $faculty->name }} Dashboard</h1>
        <div class="flex space-x-2">
            <a href="{{ route('faculties.show', $faculty) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-eye mr-2"></i> View Details
            </a>
            <a href="{{ route('faculties.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Faculties
            </a>
        </div>
    </div>

    <!-- Faculty Summary -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center">
            @if($faculty->logo)
                <div class="w-24 h-24 mr-6 mb-4 md:mb-0 flex-shrink-0">
                    <img src="{{ asset('storage/faculty_logos/' . $faculty->logo) }}" alt="{{ $faculty->name }} Logo" class="w-full h-full object-contain">
                </div>
            @endif
            <div class="flex-grow">
                <h2 class="text-xl font-semibold text-gray-900">{{ $faculty->name }}</h2>
                <p class="text-gray-600">{{ $faculty->code }}</p>
                <div class="mt-2">
                    @if($faculty->contact_email)
                        <p class="text-gray-600">
                            <i class="fas fa-envelope mr-2 text-indigo-500"></i> {{ $faculty->contact_email }}
                        </p>
                    @endif
                    @if($faculty->contact_phone)
                        <p class="text-gray-600">
                            <i class="fas fa-phone mr-2 text-indigo-500"></i> {{ $faculty->contact_phone }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="mt-4 md:mt-0 md:ml-6 flex-shrink-0">
                <div class="flex items-center">
                    <div class="text-sm text-gray-600">Status:</div>
                    <div class="ml-2">
                        @if($faculty->status)
                            <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Active</span>
                        @else
                            <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-500 mr-4">
                    <i class="fas fa-building text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Departments</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $totalDepartments }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-500 mr-4">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Programs</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $totalPrograms }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-500 mr-4">
                    <i class="fas fa-chalkboard-teacher text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Teachers</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $totalTeachers }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-500 mr-4">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Students</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $totalStudents }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Department Stats -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden lg:col-span-2">
            <div class="px-6 py-4 bg-gray-100 border-b">
                <h3 class="font-semibold text-gray-900">Department Statistics</h3>
            </div>
            <div class="p-6">
                @if(count($departmentStats) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programs</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teachers</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Students</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($departmentStats as $department)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('departments.show', $department) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $department->name }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->programs_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->teachers_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $department->students_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500">
                        <p>No departments available</p>
                        <a href="{{ route('departments.create') }}?faculty_id={{ $faculty->id }}" class="text-indigo-600 hover:text-indigo-900 mt-2 inline-block">
                            <i class="fas fa-plus-circle mr-1"></i> Add Department
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-100 border-b flex justify-between items-center">
                <h3 class="font-semibold text-gray-900">Upcoming Events</h3>
                <a href="{{ route('faculties.events.index', $faculty) }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                    View All
                </a>
            </div>
            <div class="p-6">
                @if($faculty->events()->where('start_date', '>=', now())->count() > 0)
                    <div class="space-y-4">
                        @foreach($faculty->events()->where('start_date', '>=', now())->orderBy('start_date')->limit(5)->get() as $event)
                            <div class="border-b pb-4 last:border-b-0 last:pb-0">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-500 mr-3">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $event->title }}</h4>
                                        <p class="text-sm text-gray-600">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ date('M j, Y', strtotime($event->start_date)) }}
                                            @if($event->end_date && $event->end_date != $event->start_date)
                                                - {{ date('M j, Y', strtotime($event->end_date)) }}
                                            @endif
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            {{ $event->location }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4 text-gray-500">
                        <p>No upcoming events</p>
                        <a href="{{ route('faculties.events.create', $faculty) }}" class="text-indigo-600 hover:text-indigo-900 mt-2 inline-block">
                            <i class="fas fa-plus-circle mr-1"></i> Add Event
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('faculties.staff.index', $faculty) }}" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
                <i class="fas fa-users text-xl mr-3 text-indigo-500"></i>
                <span>Manage Faculty Staff</span>
            </a>
            <a href="{{ route('faculties.events.index', $faculty) }}" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
                <i class="fas fa-calendar-alt text-xl mr-3 text-indigo-500"></i>
                <span>Manage Events</span>
            </a>
            <a href="{{ route('faculties.edit', $faculty) }}" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
                <i class="fas fa-edit text-xl mr-3 text-indigo-500"></i>
                <span>Edit Faculty</span>
            </a>
            <a href="{{ route('departments.create') }}?faculty_id={{ $faculty->id }}" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
                <i class="fas fa-plus-circle text-xl mr-3 text-indigo-500"></i>
                <span>Add Department</span>
            </a>
        </div>
    </div>
</div>
@endsection 