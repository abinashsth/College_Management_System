@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ __('Session Details') }}
            </h2>
            <a href="{{ route('settings.academic-year.show', $session->academicYear) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Academic Year
            </a>
        </div>

        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 text-gray-900">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-xl font-medium text-gray-900">{{ $session->name }}</h3>
                        <p class="text-gray-500">{{ $session->academicYear->name }} - {{ $session->type }}</p>
                        @if($session->is_current)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                Current Session
                            </span>
                        @endif
                    </div>
                    <div class="flex gap-2">
                        <a href="{{ route('settings.academic-year.sessions.edit', [$session->academicYear, $session]) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-2 px-4 rounded">
                            <i class="fas fa-edit mr-2"></i> Edit
                        </a>
                        <form method="POST" action="{{ route('settings.academic-year.sessions.destroy', [$session->academicYear, $session]) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this session?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded">
                                <i class="fas fa-trash mr-2"></i> Delete
                            </button>
                        </form>
                        @if(!$session->is_current)
                            <form method="POST" action="{{ route('settings.academic-year.sessions.set-current', [$session->academicYear, $session]) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
                                    <i class="fas fa-check-circle mr-2"></i> Set as Current
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <h4 class="font-medium text-gray-900 border-b pb-2 mb-4">Session Information</h4>
                        <dl class="grid grid-cols-1 gap-4">
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Name:</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $session->name }}</dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Type:</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ ucfirst($session->type) }}</dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Period:</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                    {{ $session->start_date->format('M d, Y') }} to {{ $session->end_date->format('M d, Y') }}
                                </dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Duration:</dt>
                                <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                    {{ $session->start_date->diffInDays($session->end_date) + 1 }} days
                                </dd>
                            </div>
                            <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                <dt class="text-sm font-medium text-gray-500">Status:</dt>
                                <dd class="mt-1 text-sm sm:col-span-2 sm:mt-0">
                                    @if($session->is_current)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">Current</span>
                                    @else
                                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-medium">Inactive</span>
                                    @endif
                                </dd>
                            </div>
                            @if($session->description)
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Description:</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $session->description }}</dd>
                                </div>
                            @endif
                        </dl>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 border-b pb-2 mb-4">Important Dates</h4>
                        <dl class="grid grid-cols-1 gap-4">
                            @if($session->registration_start_date)
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Registration:</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $session->registration_start_date->format('M d, Y') }} to 
                                        {{ $session->registration_end_date ? $session->registration_end_date->format('M d, Y') : 'TBD' }}
                                    </dd>
                                </div>
                            @endif
                            
                            @if($session->class_start_date)
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Classes:</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $session->class_start_date->format('M d, Y') }} to 
                                        {{ $session->class_end_date ? $session->class_end_date->format('M d, Y') : 'TBD' }}
                                    </dd>
                                </div>
                            @endif
                            
                            @if($session->exam_start_date)
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Exams:</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $session->exam_start_date->format('M d, Y') }} to 
                                        {{ $session->exam_end_date ? $session->exam_end_date->format('M d, Y') : 'TBD' }}
                                    </dd>
                                </div>
                            @endif
                            
                            @if($session->result_date)
                                <div class="sm:grid sm:grid-cols-3 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">Results:</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                                        {{ $session->result_date->format('M d, Y') }}
                                    </dd>
                                </div>
                            @endif

                            @if(!$session->registration_start_date && !$session->class_start_date && !$session->exam_start_date && !$session->result_date)
                                <div class="text-sm text-gray-500 italic">No specific dates have been set for this session.</div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Session Statistics -->
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <h4 class="font-medium text-gray-900 mb-4">Session Statistics</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-blue-800">Classes</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $classCount ?? 0 }}</div>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-green-800">Students</div>
                            <div class="text-2xl font-bold text-green-600">{{ $studentCount ?? 0 }}</div>
                        </div>
                        
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-purple-800">Subjects</div>
                            <div class="text-2xl font-bold text-purple-600">{{ $subjectCount ?? 0 }}</div>
                        </div>
                        
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="text-sm font-medium text-yellow-800">Exams</div>
                            <div class="text-2xl font-bold text-yellow-600">{{ $examCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 