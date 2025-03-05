@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">{{ __('Exam Details') }}</h3>
        <div class="flex space-x-3">
            <a href="{{ route('exams.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Exams') }}
            </a>
            @can('edit exams')
            <a href="{{ route('exams.edit', $exam) }}" 
                class="bg-[#37a2bc] hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
            </a>
            @endcan
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif
    <!-- End Flash Messages -->

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase">{{ __('Basic Information') }}</h6>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Exam Name') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Exam Date') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->exam_date->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Total Marks') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->total_marks }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Passing Marks') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->pass_marks }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Status') }}</label>
                                    <p class="mt-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $exam->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $exam->status ? __('Active') : __('Inactive') }}
                                        </span>
                                    </p>
                                </div>
                                @if($exam->description)
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Description') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->description }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="space-y-6">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase">{{ __('Academic Information') }}</h6>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Session') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->session->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Class') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->class->class_name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Audit Information -->
                        <div class="mt-8">
                            <h6 class="text-sm font-medium text-gray-500 uppercase">{{ __('Audit Information') }}</h6>
                            <div class="mt-4 space-y-4">
                                @if(isset($exam->created_by))
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Created By') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->creator->name ?? 'N/A' }}</p>
                                </div>
                                @endif
                                @if(isset($exam->updated_by))
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Updated By') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->updater->name ?? 'N/A' }}</p>
                                </div>
                                @endif
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Created At') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->created_at->format('F j, Y H:i:s') }}</p>
                                </div>
                                @if($exam->updated_at && $exam->updated_at->ne($exam->created_at))
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">{{ __('Updated At') }}</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $exam->updated_at->format('F j, Y H:i:s') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exam Results Section -->
                @if($exam->examResults->count() > 0)
                <div class="mt-8">
                    <h6 class="text-sm font-medium text-gray-500 uppercase mb-4">{{ __('Exam Results') }}</h6>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Student') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Roll No') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Marks') }}</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($exam->examResults as $result)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->student->student_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->student->roll_no }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $result->marks }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $result->marks >= ($exam->pass_marks) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $result->marks >= ($exam->pass_marks) ? __('Pass') : __('Fail') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Actions -->
                <div class="mt-8 border-t border-gray-200 pt-6 flex items-center justify-between">
                    <a href="{{ route('exams.index') }}" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('Back to Exams') }}
                    </a>
                    
                    <div class="flex space-x-3">
                        <a href="{{ route('exams.enter-marks', $exam->id) }}" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-pen mr-2"></i>
                            {{ __('Enter Marks') }}
                        </a>
                        
                        <a href="{{ route('exams.view-results', $exam->id) }}" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gray-600 rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                            <i class="fas fa-chart-bar mr-2"></i>
                            {{ __('View Results') }}
                        </a>
                        
                        @can('delete exams')
                        <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this exam?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash mr-2"></i>
                                {{ __('Delete') }}
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Flash message auto-hide
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const flashMessages = document.querySelectorAll('.close-flash');
            flashMessages.forEach(function(message) {
                message.closest('.mb-4').style.display = 'none';
            });
        }, 5000);

        // Manual close button for flash messages
        document.querySelectorAll('.close-flash').forEach(function(button) {
            button.addEventListener('click', function() {
                this.closest('.mb-4').style.display = 'none';
            });
        });
    });
</script>
@endpush
@endsection
