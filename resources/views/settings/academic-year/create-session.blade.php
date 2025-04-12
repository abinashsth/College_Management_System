@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ __('Add New Session') }}
            </h2>
            <a href="{{ route('settings.academic-year.show', $academicYear) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
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

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                Academic Year Period: {{ $academicYear->start_date->format('M d, Y') }} to {{ $academicYear->end_date->format('Y-m-d') }}. All session dates must fall within this period.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('settings.academic-year.sessions.store', $academicYear) }}">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h3>
                            
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Session Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    required>
                                @error('name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-gray-500 text-xs mt-1">Example: Fall 2023, Spring 2024, etc.</p>
                            </div>
                            
                            <div class="mb-4">
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                    Session Type <span class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    required>
                                    <option value="">Select Type</option>
                                    <option value="semester" {{ old('type') === 'semester' ? 'selected' : '' }}>Semester</option>
                                    <option value="trimester" {{ old('type') === 'trimester' ? 'selected' : '' }}>Trimester</option>
                                    <option value="quarter" {{ old('type') === 'quarter' ? 'selected' : '' }}>Quarter</option>
                                    <option value="summer" {{ old('type') === 'summer' ? 'selected' : '' }}>Summer</option>
                                    <option value="winter" {{ old('type') === 'winter' ? 'selected' : '' }}>Winter</option>
                                    <option value="other" {{ old('type') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('type')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    required min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                @error('start_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    required min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                @error('end_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea name="description" id="description" rows="3" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Important Dates -->
                        <div>
                            <h3 class="font-medium text-gray-900 border-b pb-2 mb-4">Important Dates (Optional)</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="registration_start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Registration Start
                                    </label>
                                    <input type="date" name="registration_start_date" id="registration_start_date" 
                                        value="{{ old('registration_start_date') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                    @error('registration_start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="registration_end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Registration End
                                    </label>
                                    <input type="date" name="registration_end_date" id="registration_end_date" 
                                        value="{{ old('registration_end_date') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                    @error('registration_end_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="class_start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Classes Start
                                    </label>
                                    <input type="date" name="class_start_date" id="class_start_date" 
                                        value="{{ old('class_start_date') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                    @error('class_start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="class_end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Classes End
                                    </label>
                                    <input type="date" name="class_end_date" id="class_end_date" 
                                        value="{{ old('class_end_date') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                    @error('class_end_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="exam_start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Exams Start
                                    </label>
                                    <input type="date" name="exam_start_date" id="exam_start_date" 
                                        value="{{ old('exam_start_date') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                    @error('exam_start_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="exam_end_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Exams End
                                    </label>
                                    <input type="date" name="exam_end_date" id="exam_end_date" 
                                        value="{{ old('exam_end_date') }}" 
                                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                    @error('exam_end_date')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="result_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Results Publication Date
                                </label>
                                <input type="date" name="result_date" id="result_date" 
                                    value="{{ old('result_date') }}" 
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                    min="{{ $academicYear->start_date->format('Y-m-d') }}" max="{{ $academicYear->end_date->format('Y-m-d') }}">
                                @error('result_date')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="is_current" id="is_current" value="1" 
                                        {{ old('is_current') ? 'checked' : '' }}
                                        class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                    <label for="is_current" class="ml-2 block text-sm text-gray-700">
                                        Set as Current Session
                                    </label>
                                </div>
                                <p class="text-gray-500 text-xs mt-1">
                                    This will mark the session as the current active session. Only one session can be active at a time.
                                </p>
                                @error('is_current')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('settings.academic-year.show', $academicYear) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium py-2 px-4 rounded mr-2">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
                            Create Session
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection 