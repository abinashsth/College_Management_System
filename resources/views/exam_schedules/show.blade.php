@extends('layouts.app')

@section('title', 'Exam Schedule Details')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row items-start mb-6">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $examSchedule->exam->title }} Schedule</h1>
            <p class="text-gray-600">{{ $examSchedule->section->name }} | {{ $examSchedule->exam_date->format('F d, Y') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            @can('manage exam schedules')
            <a href="{{ route('exams.edit-schedule', ['exam' => $examSchedule->exam->id, 'schedule' => $examSchedule->id]) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="fas fa-edit mr-2"></i>
                Edit Schedule
            </a>
            @endcan
            <a href="{{ route('exam.schedules', $examSchedule->exam) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#37a2bc]">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Schedules
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Schedule Details Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Schedule Details</h2>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Exam</h3>
                            <p class="mt-1">{{ $examSchedule->exam->title }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Section</h3>
                            <p class="mt-1">{{ $examSchedule->section->name }} ({{ $examSchedule->section->class->name ?? 'N/A' }})</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Exam Date</h3>
                            <p class="mt-1">{{ $examSchedule->exam_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Time</h3>
                            <p class="mt-1">{{ \Carbon\Carbon::parse($examSchedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($examSchedule->end_time)->format('h:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                            <p class="mt-1">{{ $examSchedule->getDurationInMinutes() }} minutes</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                @php
                                    $statusColors = [
                                        'scheduled' => 'bg-blue-100 text-blue-800',
                                        'in_progress' => 'bg-yellow-100 text-yellow-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                        'postponed' => 'bg-purple-100 text-purple-800',
                                    ];
                                    $statusColor = $statusColors[$examSchedule->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                    {{ ucfirst(str_replace('_', ' ', $examSchedule->status)) }}
                                </span>
                                @if($examSchedule->is_rescheduled)
                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                    Rescheduled
                                </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Location</h3>
                            <p class="mt-1">{{ $examSchedule->location ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Room Number</h3>
                            <p class="mt-1">{{ $examSchedule->room_number ?? 'Not specified' }}</p>
                        </div>
                        
                        @if($examSchedule->seating_capacity)
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Seating Capacity</h3>
                            <p class="mt-1">{{ $examSchedule->seating_capacity }} students</p>
                        </div>
                        @endif
                        
                        @if($examSchedule->is_rescheduled && $examSchedule->reschedule_reason)
                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Reschedule Reason</h3>
                            <p class="mt-1">{{ $examSchedule->reschedule_reason }}</p>
                        </div>
                        @endif
                        
                        @if($examSchedule->notes)
                        <div class="col-span-2">
                            <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                            <p class="mt-1">{{ $examSchedule->notes }}</p>
                        </div>
                        @endif
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created By</h3>
                            <p class="mt-1">{{ $examSchedule->creator->name ?? 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated By</h3>
                            <p class="mt-1">{{ $examSchedule->updater->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Supervisors Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900">Assigned Supervisors</h2>
                    @can('manage exam schedules')
                    <a href="{{ route('exams.assign-supervisor', ['exam' => $examSchedule->exam->id, 'schedule' => $examSchedule->id]) }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-user-plus mr-1"></i>
                        Assign
                    </a>
                    @endcan
                </div>
                <div class="p-4">
                    @if($examSchedule->supervisors->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supervisor</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reporting Time</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($examSchedule->supervisors as $supervisor)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($supervisor->user->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $supervisor->user->name }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $supervisor->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $supervisor->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $supervisorRoles[$supervisor->role] ?? ucfirst($supervisor->role) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $supervisor->reporting_time ? \Carbon\Carbon::parse($supervisor->reporting_time)->format('h:i A') : 'Not specified' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($supervisor->is_confirmed)
                                        @if($supervisor->is_attended)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Attended</span>
                                        @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>
                                        @endif
                                    @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Awaiting Confirmation</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @can('manage exam schedules')
                                    <form action="{{ route('exam-supervisors.destroy', $supervisor) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                            onclick="return confirm('Are you sure you want to remove this supervisor?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-user-slash text-4xl mb-3 text-gray-400"></i>
                        <p>No supervisors have been assigned to this exam schedule yet.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Students Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3 flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900">Participating Students</h2>
                    <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded">
                        Total: {{ $students->count() }}
                    </span>
                </div>
                <div class="p-4">
                    @if($students->count() > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($students as $student)
                        <div class="flex items-center p-2 hover:bg-gray-50 rounded-md">
                            <div class="flex-shrink-0 h-8 w-8">
                                <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $student->name }}">
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                <div class="text-xs text-gray-500">{{ $student->id_number ?? 'No ID' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-user-graduate text-4xl mb-3 text-gray-400"></i>
                        <p>No students found in this section</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Available Supervisors Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h2 class="text-lg font-medium text-gray-900">Available Supervisors</h2>
                </div>
                <div class="p-4">
                    @if(count($availableSupervisors) > 0)
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($availableSupervisors as $supervisor)
                        <div class="flex items-center p-2 hover:bg-gray-50 rounded-md">
                            <div class="flex-shrink-0 h-8 w-8">
                                <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($supervisor->name) }}&color=7F9CF5&background=EBF4FF" alt="{{ $supervisor->name }}">
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $supervisor->name }}</div>
                                <div class="text-xs text-gray-500">{{ $supervisor->email }}</div>
                            </div>
                            @can('manage exam schedules')
                            <a href="{{ route('exams.assign-supervisor', ['exam' => $examSchedule->exam->id, 'schedule' => $examSchedule->id, 'user_id' => $supervisor->id]) }}" class="text-indigo-600 hover:text-indigo-900">
                                <i class="fas fa-plus-circle"></i>
                            </a>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500">
                        <i class="fas fa-users text-4xl mb-3 text-gray-400"></i>
                        <p>No available supervisors found for this time slot</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 