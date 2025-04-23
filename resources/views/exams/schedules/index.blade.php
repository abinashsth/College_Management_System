@extends('layouts.app')

@section('title', 'Exam Schedules')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row items-start mb-6">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">{{ $exam->title }} - Schedules</h1>
            <p class="text-gray-600">Manage exam schedules for different sections</p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            @can('manage exam schedules')
            <a href="{{ route('exams.create-schedule', $exam) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#37a2bc] hover:bg-[#2c8ca3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#37a2bc]">
                <i class="fas fa-plus mr-2"></i>
                Add New Schedule
            </a>
            @endcan
            <a href="{{ route('exams.show', $exam) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#37a2bc]">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Exam
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

    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-medium text-gray-900">Exam Details</h2>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Subject</p>
                    <p class="mt-1">{{ $exam->subject->name ?? 'Multiple Subjects' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Class</p>
                    <p class="mt-1">{{ $exam->class->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Academic Session</p>
                    <p class="mt-1">{{ $exam->academicSession->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Exam Type</p>
                    <p class="mt-1">{{ ucfirst($exam->exam_type) }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Marks</p>
                    <p class="mt-1">{{ $exam->total_marks }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Passing Marks</p>
                    <p class="mt-1">{{ $exam->passing_marks }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">Schedules</h2>
            <span class="bg-gray-100 text-gray-800 text-xs font-medium inline-flex items-center px-2.5 py-0.5 rounded">
                Total: {{ $exam->schedules->count() }}
            </span>
        </div>
        
        <div class="overflow-x-auto">
            @if($exam->schedules->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Section
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Location
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Supervisors
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($exam->schedules as $schedule)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $schedule->section->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $schedule->section->class->name ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $schedule->exam_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - 
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $schedule->location ?? 'N/A' }}</div>
                            @if($schedule->room_number)
                            <div class="text-xs text-gray-500">Room: {{ $schedule->room_number }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColors = [
                                    'scheduled' => 'bg-blue-100 text-blue-800',
                                    'in_progress' => 'bg-yellow-100 text-yellow-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'postponed' => 'bg-purple-100 text-purple-800',
                                ];
                                $statusColor = $statusColors[$schedule->status] ?? 'bg-gray-100 text-gray-800';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColor }}">
                                {{ ucfirst(str_replace('_', ' ', $schedule->status)) }}
                            </span>
                            @if($schedule->is_rescheduled)
                            <div class="text-xs text-orange-600 mt-1">Rescheduled</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $schedule->supervisors->count() }}</div>
                            @if($schedule->supervisors->count() > 0)
                            <div class="text-xs text-gray-500">
                                {{ $schedule->supervisors->first()->user->name }}
                                @if($schedule->supervisors->count() > 1)
                                <span class="text-gray-400">+{{ $schedule->supervisors->count() - 1 }} more</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('exam-schedules.show', $schedule->id) }}" class="text-[#37a2bc] hover:text-[#2c8ca3] mr-3" title="View Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @can('manage exam schedules')
                            <a href="{{ route('exams.edit-schedule', ['exam' => $exam->id, 'schedule' => $schedule->id]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3" title="Edit Schedule">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <a href="{{ route('exams.assign-supervisor', ['exam' => $exam->id, 'schedule' => $schedule->id]) }}" class="text-green-600 hover:text-green-900 mr-3" title="Assign Supervisors">
                                <i class="fas fa-user-plus"></i>
                            </a>
                            
                            <form action="{{ route('exams.destroy-schedule', ['exam' => $exam->id, 'schedule' => $schedule->id]) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" 
                                    onclick="return confirm('Are you sure you want to delete this schedule? This action cannot be undone.')" title="Delete Schedule">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="p-8 text-center text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-4 text-gray-400"></i>
                <p>No schedules have been created for this exam yet.</p>
                <p class="text-sm mt-2">Click the "Add New Schedule" button to create a schedule.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 