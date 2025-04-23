@extends('layouts.app')

@section('title', 'Edit Exam Schedule')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row items-start mb-6">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900">Edit Schedule for {{ $exam->title }}</h1>
            <p class="text-gray-600">Update schedule details</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('exam.schedules', $exam) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#37a2bc]">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Schedules
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Schedule Details</h2>
        </div>
        
        <form action="{{ route('exams.update-schedule', ['exam' => $exam->id, 'schedule' => $schedule->id]) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Section Selection -->
                <div>
                    <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">Section <span class="text-red-600">*</span></label>
                    <select id="section_id" name="section_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('section_id') border-red-300 @enderror" required>
                        <option value="">Select a section</option>
                        @foreach($sections as $section)
                        <option value="{{ $section->id }}" @if(old('section_id', $schedule->section_id) == $section->id) selected @endif>
                            {{ $section->name }} ({{ $section->class->name ?? 'Unknown Class' }})
                        </option>
                        @endforeach
                    </select>
                    @error('section_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Exam Date -->
                <div>
                    <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">Exam Date <span class="text-red-600">*</span></label>
                    <input type="date" id="exam_date" name="exam_date" value="{{ old('exam_date', $schedule->exam_date->format('Y-m-d')) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('exam_date') border-red-300 @enderror" required>
                    @error('exam_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Start Time -->
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-600">*</span></label>
                    <input type="time" id="start_time" name="start_time" value="{{ old('start_time', date('H:i', strtotime($schedule->start_time))) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('start_time') border-red-300 @enderror" required>
                    @error('start_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- End Time -->
                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-600">*</span></label>
                    <input type="time" id="end_time" name="end_time" value="{{ old('end_time', date('H:i', strtotime($schedule->end_time))) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('end_time') border-red-300 @enderror" required>
                    @error('end_time')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Location -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" id="location" name="location" value="{{ old('location', $schedule->location) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('location') border-red-300 @enderror">
                    @error('location')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number</label>
                    <input type="text" id="room_number" name="room_number" value="{{ old('room_number', $schedule->room_number) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('room_number') border-red-300 @enderror">
                    @error('room_number')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Seating Capacity -->
                <div>
                    <label for="seating_capacity" class="block text-sm font-medium text-gray-700 mb-1">Seating Capacity</label>
                    <input type="number" id="seating_capacity" name="seating_capacity" min="1" value="{{ old('seating_capacity', $schedule->seating_capacity) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('seating_capacity') border-red-300 @enderror">
                    @error('seating_capacity')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-600">*</span></label>
                    <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('status') border-red-300 @enderror" required>
                        @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" @if(old('status', $schedule->status) == $value) selected @endif>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                    @error('status')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Is Rescheduled -->
                <div>
                    <div class="flex items-center">
                        <input type="checkbox" id="is_rescheduled" name="is_rescheduled" value="1" @if(old('is_rescheduled', $schedule->is_rescheduled)) checked @endif
                            class="h-4 w-4 text-[#37a2bc] focus:ring-[#37a2bc] border-gray-300 rounded">
                        <label for="is_rescheduled" class="ml-2 block text-sm text-gray-700">This is a rescheduled exam</label>
                    </div>
                    @error('is_rescheduled')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Reschedule Reason (conditionally shown) -->
                <div id="reschedule_reason_container" class="@if(!old('is_rescheduled', $schedule->is_rescheduled)) hidden @endif col-span-1 md:col-span-2">
                    <label for="reschedule_reason" class="block text-sm font-medium text-gray-700 mb-1">Reschedule Reason</label>
                    <textarea id="reschedule_reason" name="reschedule_reason" rows="2" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('reschedule_reason') border-red-300 @enderror">{{ old('reschedule_reason', $schedule->reschedule_reason) }}</textarea>
                    @error('reschedule_reason')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Notes -->
                <div class="col-span-1 md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 @error('notes') border-red-300 @enderror">{{ old('notes', $schedule->notes) }}</textarea>
                    @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div class="mt-6 text-right">
                <a href="{{ route('exam.schedules', $exam) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#37a2bc]">
                    Cancel
                </a>
                <button type="submit" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-[#37a2bc] hover:bg-[#2c8ca3] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#37a2bc]">
                    Update Schedule
                </button>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isRescheduledCheckbox = document.getElementById('is_rescheduled');
        const rescheduleContainer = document.getElementById('reschedule_reason_container');
        
        isRescheduledCheckbox.addEventListener('change', function() {
            if (this.checked) {
                rescheduleContainer.classList.remove('hidden');
            } else {
                rescheduleContainer.classList.add('hidden');
            }
        });
    });
</script>
@endsection
@endsection 