@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
        <h3 class="text-gray-700 text-2xl sm:text-3xl font-medium">{{ __('Edit Exam') }}</h3>
        <a href="{{ route('exams.index') }}" class="w-full sm:w-auto bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded flex items-center justify-center">
            <i class="fas fa-arrow-left mr-2"></i>{{ __('Back to Exams') }}
        </a>
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
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4 sm:p-6 lg:p-8 bg-white border-b border-gray-200">
                <!-- Validation Errors -->
                @if ($errors->any())
                <div class="mb-4 bg-red-50 p-4 rounded-md">
                    <div class="flex flex-col sm:flex-row">
                        <div class="flex-shrink-0 mb-2 sm:mb-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="sm:ml-3">
                            <h3 class="text-sm font-medium text-red-800">{{ __('Please fix the following errors:') }}</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <form action="{{ route('exams.update', $exam) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Basic Information -->
                        <div class="space-y-6">
                            <div>
                                <h6 class="text-sm font-medium text-gray-500 uppercase">{{ __('Basic Information') }}</h6>
                                <div class="mt-4 space-y-4">
                                    <div class="form-group">
                                        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Exam Name') }}</label>
                                        <input type="text" name="name" id="name" value="{{ old('name', $exam->name) }}"
                                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>

                                    <div class="form-group">
                                        <label for="exam_date" class="block text-sm font-medium text-gray-700">{{ __('Exam Date') }}</label>
                                        <input type="date" name="exam_date" id="exam_date" value="{{ old('exam_date', $exam->exam_date->format('Y-m-d')) }}"
                                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="form-group">
                                            <label for="total_marks" class="block text-sm font-medium text-gray-700">{{ __('Total Marks') }}</label>
                                            <input type="number" name="total_marks" id="total_marks" value="{{ old('total_marks', $exam->total_marks) }}"
                                                class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                required>
                                        </div>

                                        <div class="form-group">
                                            <label for="passing_marks" class="block text-sm font-medium text-gray-700">{{ __('Passing Marks') }}</label>
                                            <input type="number" name="passing_marks" id="passing_marks" value="{{ old('passing_marks', $exam->pass_marks) }}"
                                                class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Status') }}</label>
                                        <select name="status" id="status" 
                                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                            <option value="1" {{ old('status', $exam->status) == 1 ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="0" {{ old('status', $exam->status) == 0 ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information -->
                        <div class="space-y-6">
                            <div>
                                <h6 class="text-sm font-medium text-gray-500 uppercase">{{ __('Academic Information') }}</h6>
                                <div class="mt-4 space-y-4">
                                    <div class="form-group">
                                        <label for="session_id" class="block text-sm font-medium text-gray-700">{{ __('Session') }}</label>
                                        <select name="session_id" id="session_id" 
                                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                            <option value="">{{ __('Select Session') }}</option>
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->id }}" {{ old('session_id', $exam->session_id) == $session->id ? 'selected' : '' }}>
                                                    {{ $session->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="class_id" class="block text-sm font-medium text-gray-700">{{ __('Class') }}</label>
                                        <select name="class_id" id="class_id" 
                                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                            <option value="">{{ __('Select Class') }}</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                                                    {{ $class->class_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('description', $exam->description) }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 border-t border-gray-200 pt-6 flex flex-col sm:flex-row items-center justify-end gap-3">
                        <a href="{{ route('exams.index') }}"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-times mr-2"></i>
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#37a2bc] hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            {{ __('Update Exam') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-4 sm:p-6 bg-white border-b border-gray-200">
                <form action="{{ route('exams.view') }}" method="GET" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Academic Year Dropdown -->
                        <div class="form-group">
                            <label for="academic_year" class="block text-sm font-medium text-gray-700">{{ __('Academic Year') }}</label>
                            <select name="academic_year" id="academic_year" 
                                class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required>
                                <option value="">{{ __('Select Year') }}</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Class Dropdown -->
                        <div class="form-group">
                            <label for="class_id" class="block text-sm font-medium text-gray-700">{{ __('Class') }}</label>
                            <select name="class_id" id="class_id" 
                                class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required>
                                <option value="">{{ __('Select Class') }}</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Exam Term Dropdown -->
                        <div class="form-group">
                            <label for="exam_term" class="block text-sm font-medium text-gray-700">{{ __('Exam Term') }}</label>
                            <select name="exam_term" id="exam_term" 
                                class="form-input mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                required>
                                <option value="">{{ __('Select Term') }}</option>
                                @foreach($examTerms as $term)
                                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- View Button -->
                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('View') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card-grid">
  <!-- Card items -->
</div>

<nav class="nav-menu">
  <!-- Navigation items -->
</nav>

<img class="responsive-image" src="your-image.jpg" alt="Description">

<div class="responsive-table">
  <div class="overflow-x-auto">
    <table class="w-full">
      <!-- Table content -->
    </table>
  </div>
</div>

<h1 class="text-2xl sm:text-3xl lg:text-4xl">Title</h1>
@endsection
