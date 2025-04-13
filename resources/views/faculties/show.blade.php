@extends('layouts.app')

@section('title', 'Faculty Details')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $faculty->name }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('faculties.dashboard', $faculty) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-chart-bar mr-2"></i> Dashboard
            </a>
            <a href="{{ route('faculties.edit', $faculty) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-edit mr-2"></i> Edit
            </a>
            <a href="{{ route('faculties.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Faculty Details -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden md:col-span-2">
            <div class="p-6">
                <div class="flex flex-col md:flex-row">
                    @if($faculty->logo)
                        <div class="w-48 mb-4 md:mb-0 md:mr-6">
                            <img src="{{ asset('storage/faculty_logos/' . $faculty->logo) }}" alt="{{ $faculty->name }} Logo" class="w-full h-auto object-contain border rounded">
                        </div>
                    @endif
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">Faculty Details</h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 mt-4">
                            <div class="col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Code</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $faculty->code }}</dd>
                            </div>
                            @if($faculty->description)
                                <div class="col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $faculty->description }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $faculty->contact_email ?? 'Not specified' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $faculty->contact_phone ?? 'Not specified' }}</dd>
                            </div>
                            @if($faculty->website)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Website</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <a href="{{ $faculty->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                            {{ $faculty->website }}
                                        </a>
                                    </dd>
                                </div>
                            @endif
                            @if($faculty->address)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Address</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $faculty->address }}</dd>
                                </div>
                            @endif
                            @if($faculty->established_date)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Established</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ date('F j, Y', strtotime($faculty->established_date)) }}</dd>
                                </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($faculty->status)
                                        <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Inactive</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dean Information -->
        {{-- This section is removed as dean is no longer associated directly with faculty --}}

    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <a href="{{ route('faculties.staff.index', $faculty) }}" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
            <i class="fas fa-users text-2xl mr-3 text-indigo-500"></i>
            <div>
                <h3 class="font-medium">Manage Faculty Staff</h3>
                <p class="text-sm text-gray-500">Assign and manage staff members</p>
            </div>
        </a>
        <a href="{{ route('faculties.events.index', $faculty) }}" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
            <i class="fas fa-calendar-alt text-2xl mr-3 text-indigo-500"></i>
            <div>
                <h3 class="font-medium">Manage Events</h3>
                <p class="text-sm text-gray-500">Create and manage faculty events</p>
            </div>
        </a>
        <a href="#departments" class="bg-white p-4 shadow-md rounded-lg hover:shadow-lg transition-shadow flex items-center text-gray-700 hover:text-indigo-600">
            <i class="fas fa-building text-2xl mr-3 text-indigo-500"></i>
            <div>
                <h3 class="font-medium">View Departments</h3>
                <p class="text-sm text-gray-500">See all departments in this faculty</p>
            </div>
        </a>
    </div>

    <!-- Departments List -->
    <div id="departments" class="mt-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Departments</h2>
            <a href="{{ route('departments.create') }}?faculty_id={{ $faculty->id }}" class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded">
                <i class="fas fa-plus mr-1"></i> Add Department
            </a>
        </div>
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Name</th>
                            <th class="py-3 px-6 text-left">Code</th>
                            <th class="py-3 px-6 text-left">Head</th>
                            <th class="py-3 px-6 text-left">Programs</th>
                            <th class="py-3 px-6 text-left">Status</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-600 text-sm">
                        @forelse($faculty->departments as $department)
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="py-3 px-6 text-left whitespace-nowrap font-medium">
                                    <a href="{{ route('departments.show', $department) }}" class="text-blue-600 hover:text-blue-900">
                                        {{ $department->name }}
                                    </a>
                                </td>
                                <td class="py-3 px-6 text-left">{{ $department->code }}</td>
                                <td class="py-3 px-6 text-left">
                                    @if($department->head)
                                        {{ $department->head->user->name ?? 'Not assigned' }}
                                    @else
                                        <span class="text-gray-400">Not assigned</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-left">
                                    {{ $department->programs_count ?? $department->programs()->count() }}
                                </td>
                                <td class="py-3 px-6 text-left">
                                    @if($department->status)
                                        <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Active</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Inactive</span>
                                    @endif
                                </td>
                                <td class="py-3 px-6 text-center">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('departments.show', $department) }}" class="text-gray-600 hover:text-blue-600" title="View details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('departments.edit', $department) }}" class="text-gray-600 hover:text-yellow-600" title="Edit department">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-b border-gray-200">
                                <td class="py-4 px-6 text-center text-gray-500" colspan="6">No departments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($faculty->departments->isEmpty())
                <div class="p-4 text-center">
                    <a href="{{ route('departments.create') }}?faculty_id={{ $faculty->id }}" class="text-indigo-600 hover:text-indigo-900">
                        <i class="fas fa-plus-circle mr-1"></i> Create your first department
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 