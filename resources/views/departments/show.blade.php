@extends('layouts.app')

@section('title', $department->name)

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Department Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('departments.dashboard', $department) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-chart-bar mr-2"></i> Dashboard
            </a>
            <a href="{{ route('departments.edit', $department) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('departments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Department Info -->
        <div class="lg:col-span-2 bg-white shadow-md rounded-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center mb-6">
                    @if($department->logo)
                        <div class="mr-4 w-16 h-16 bg-gray-100 rounded-full overflow-hidden">
                            <img src="{{ asset('storage/' . $department->logo) }}" alt="{{ $department->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="mr-4 w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-building text-3xl text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h2>
                        <div class="flex items-center text-sm text-gray-600 mt-1">
                            <span class="bg-blue-100 text-blue-800 px-2 py-0.5 rounded-full text-xs font-medium">{{ $department->code }}</span>
                            <span class="mx-2">•</span>
                            @if($department->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-0.5 rounded-full text-xs font-medium">Active</span>
                            @else
                                <span class="bg-red-100 text-red-800 px-2 py-0.5 rounded-full text-xs font-medium">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700">
                        {{ $department->description ?? 'No description available.' }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Faculty</h3>
                        @if($department->faculty)
                            <div class="flex items-center">
                                @if($department->faculty->logo)
                                    <div class="mr-3 w-10 h-10 bg-gray-100 rounded-full overflow-hidden">
                                        <img src="{{ asset('storage/' . $department->faculty->logo) }}" alt="{{ $department->faculty->name }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('faculties.show', $department->faculty) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $department->faculty->name }}
                                    </a>
                                    <p class="text-sm text-gray-600">Code: {{ $department->faculty->code }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500">Not assigned to any faculty</p>
                        @endif
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Department Head</h3>
                        @if($department->head)
                            <div class="flex items-center">
                                <div class="mr-3 w-10 h-10 bg-gray-100 rounded-full overflow-hidden">
                                    @if($department->head->user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $department->head->user->profile_photo_path) }}" alt="{{ $department->head->user->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium">{{ $department->head->user->name }}</p>
                                    <p class="text-sm text-gray-600">
                                        Since: {{ \Carbon\Carbon::parse($department->head->appointment_date)->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center">
                                <p class="text-gray-500 mr-3">No head assigned</p>
                                <a href="{{ route('departments.heads.create', $department) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                    <i class="fas fa-plus-circle mr-1"></i> Assign Head
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Contact Information</h3>
                        <ul class="space-y-2">
                            @if(isset($department->metadata['contact_email']))
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-envelope text-gray-500 w-5 mr-2"></i>
                                    <a href="mailto:{{ $department->metadata['contact_email'] }}" class="hover:text-blue-600">
                                        {{ $department->metadata['contact_email'] }}
                                    </a>
                                </li>
                            @endif
                            @if(isset($department->metadata['contact_phone']))
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-phone text-gray-500 w-5 mr-2"></i>
                                    <a href="tel:{{ $department->metadata['contact_phone'] }}" class="hover:text-blue-600">
                                        {{ $department->metadata['contact_phone'] }}
                                    </a>
                                </li>
                            @endif
                            @if(isset($department->metadata['website']))
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-globe text-gray-500 w-5 mr-2"></i>
                                    <a href="{{ $department->metadata['website'] }}" target="_blank" class="hover:text-blue-600">
                                        {{ $department->metadata['website'] }}
                                    </a>
                                </li>
                            @endif
                            @if(isset($department->metadata['address']))
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-map-marker-alt text-gray-500 w-5 mr-2"></i>
                                    {{ $department->metadata['address'] }}
                                </li>
                            @endif
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Additional Information</h3>
                        <ul class="space-y-2">
                            @if(isset($department->metadata['established_date']))
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-calendar-alt text-gray-500 w-5 mr-2"></i>
                                    <span>Established: {{ \Carbon\Carbon::parse($department->metadata['established_date'])->format('F d, Y') }}</span>
                                </li>
                            @endif
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-users text-gray-500 w-5 mr-2"></i>
                                <span>{{ $department->teachers->count() }} Teachers</span>
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-graduation-cap text-gray-500 w-5 mr-2"></i>
                                <span>{{ $department->programs->count() }} Programs</span>
                            </li>
                            <li class="flex items-center text-gray-700">
                                <i class="fas fa-clock text-gray-500 w-5 mr-2"></i>
                                <span>Created: {{ $department->created_at->format('M d, Y') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Side Panel -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Department Stats</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-600 font-medium">Teachers</p>
                            <p class="text-2xl font-bold text-blue-800">{{ $department->teachers->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-green-600 font-medium">Programs</p>
                            <p class="text-2xl font-bold text-green-800">{{ $department->programs->count() }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-purple-600 font-medium">Courses</p>
                            <p class="text-2xl font-bold text-purple-800">{{ $department->courses()->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-yellow-600 font-medium">Students</p>
                            <p class="text-2xl font-bold text-yellow-800">{{ $department->students()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('departments.teachers.index', $department) }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-users mr-3 w-5 text-center"></i>
                                <span>Manage Teachers</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('departments.courses', $department) }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-book mr-3 w-5 text-center"></i>
                                <span>View Courses</span>
                            </a>
                        </li>
                        @if($department->head)
                            <li>
                                <a href="{{ route('department-heads.edit', $department->head) }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-user-tie mr-3 w-5 text-center"></i>
                                    <span>Manage Head</span>
                                </a>
                            </li>
                        @else
                            <li>
                                <a href="{{ route('departments.heads.create', $department) }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-user-plus mr-3 w-5 text-center"></i>
                                    <span>Assign Head</span>
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('departments.dashboard', $department) }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-chart-line mr-3 w-5 text-center"></i>
                                <span>View Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('departments.edit', $department) }}" class="flex items-center text-blue-600 hover:text-blue-800">
                                <i class="fas fa-edit mr-3 w-5 text-center"></i>
                                <span>Edit Department</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Programs Section -->
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Programs Offered</h3>
            @if(auth()->user()->can('manage programs'))
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">
                    <i class="fas fa-plus-circle mr-1"></i> Add Program
                </a>
            @endif
        </div>
        <div class="overflow-x-auto">
            @if($department->programs->count() > 0)
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                            <th class="py-3 px-6 text-left">Program Name</th>
                            <th class="py-3 px-6 text-left">Code</th>
                            <th class="py-3 px-6 text-left">Level</th>
                            <th class="py-3 px-6 text-left">Duration</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($department->programs as $program)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <a href="#" class="text-blue-600 hover:text-blue-900 font-medium">
                                        {{ $program->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-6 text-left">{{ $program->code }}</td>
                                <td class="py-3 px-6 text-left">{{ $program->level }}</td>
                                <td class="py-3 px-6 text-left">{{ $program->duration }} {{ Str::plural('Year', $program->duration) }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if($program->is_active)
                                        <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="#" class="text-gray-600 hover:text-blue-600" title="View details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->can('manage programs'))
                                            <a href="#" class="text-gray-600 hover:text-yellow-600" title="Edit program">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-gray-500">
                    <p>No programs have been added to this department yet.</p>
                    @if(auth()->user()->can('manage programs'))
                        <a href="#" class="inline-block mt-3 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-plus-circle mr-1"></i> Add Program
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Teachers Section -->
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Department Teachers</h3>
            <a href="{{ route('departments.teachers.assign', $department) }}" class="text-sm text-blue-600 hover:text-blue-800">
                <i class="fas fa-plus-circle mr-1"></i> Assign Teachers
            </a>
        </div>
        <div class="overflow-x-auto">
            @if($department->teachers->count() > 0)
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-sm uppercase font-semibold">
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Position</th>
                            <th class="py-3 px-6 text-left">Email</th>
                            <th class="py-3 px-6 text-left">Joined</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @foreach($department->teachers as $teacher)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="mr-2 w-8 h-8 bg-gray-100 rounded-full overflow-hidden">
                                            @if($teacher->user->profile_photo_path)
                                                <img src="{{ asset('storage/' . $teacher->user->profile_photo_path) }}" alt="{{ $teacher->user->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <span>{{ $teacher->user->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-6 text-left">{{ $teacher->position ?? 'Teacher' }}</td>
                                <td class="py-3 px-6 text-left">{{ $teacher->user->email }}</td>
                                <td class="py-3 px-6 text-left">{{ \Carbon\Carbon::parse($teacher->start_date)->format('M d, Y') }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if($teacher->is_active)
                                        <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="#" class="text-gray-600 hover:text-blue-600" title="View profile">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        <form action="{{ route('departments.teachers.remove', [$department, $teacher]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this teacher from the department?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-600 hover:text-red-600" title="Remove from department">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="p-6 text-center text-gray-500">
                    <p>No teachers have been assigned to this department yet.</p>
                    <a href="{{ route('departments.teachers.assign', $department) }}" class="inline-block mt-3 text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus-circle mr-1"></i> Assign Teachers
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 