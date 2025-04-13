@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Process Results</h1>
        <div class="flex space-x-2">
            <a href="{{ route('results.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Results
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Error</p>
        <p>{{ session('error') }}</p>
    </div>
    @endif

    @if ($errors->any())
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
        <p class="font-bold">Please fix the following errors:</p>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Process Results by Section</h2>
            <p class="text-sm text-gray-500 mt-1">
                This will calculate results for all students in the selected section based on their published marks.
            </p>
        </div>
        
        <div class="p-6">
            <form action="{{ route('results.process-section') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                        <select name="exam_id" id="exam_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Select Exam</option>
                            @foreach($exams as $exam)
                            <option value="{{ $exam->id }}" {{ old('exam_id') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }} ({{ $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'No Date' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="section_id" class="block text-sm font-medium text-gray-700 mb-1">Section</label>
                        <select name="section_id" id="section_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->section_name }} ({{ $section->class->class_name ?? 'No Class' }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="grade_system_id" class="block text-sm font-medium text-gray-700 mb-1">Grade System (Optional)</label>
                        <select name="grade_system_id" id="grade_system_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">Use Default</option>
                            @foreach($gradeSystems as $gradeSystem)
                            <option value="{{ $gradeSystem->id }}" {{ old('grade_system_id') == $gradeSystem->id ? 'selected' : '' }}>
                                {{ $gradeSystem->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input id="confirm" name="confirm" type="checkbox" required class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="confirm" class="font-medium text-gray-700">Confirm Processing</label>
                            <p class="text-gray-500">
                                I confirm that marks for the selected exam have been entered and published. 
                                I understand that this action will calculate results for all students in the selected section.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Process Results
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Processing Information</h2>
        </div>
        
        <div class="p-6">
            <div class="mb-6">
                <h3 class="font-medium text-md text-gray-700 mb-2">Process</h3>
                <ol class="list-decimal list-inside text-gray-600 space-y-2">
                    <li>Select the exam for which you want to process results</li>
                    <li>Select the section for which you want to calculate results</li>
                    <li>Optionally choose a grade system (or use the default)</li>
                    <li>Confirm that marks have been entered and published</li>
                    <li>Click "Process Results" to calculate results for all students</li>
                </ol>
            </div>
            
            <div class="mb-6">
                <h3 class="font-medium text-md text-gray-700 mb-2">Note</h3>
                <ul class="list-disc list-inside text-gray-600 space-y-2">
                    <li>Results are calculated based on published marks only</li>
                    <li>The system calculates total marks, percentage, GPA, and grade based on the selected grade system</li>
                    <li>All existing results for the selected students and exam will be updated</li>
                    <li>Results must be verified before they can be published</li>
                </ul>
            </div>
            
            <div>
                <h3 class="font-medium text-md text-gray-700 mb-2">Next Steps</h3>
                <ol class="list-decimal list-inside text-gray-600 space-y-2">
                    <li>After processing, verify the results to ensure accuracy</li>
                    <li>You can verify results individually or in batch</li>
                    <li>Once verified, results can be published for students to view</li>
                    <li>Published results can be printed or exported to PDF/Excel</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection 