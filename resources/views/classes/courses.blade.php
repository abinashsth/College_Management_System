@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Manage Courses for Class: {{ $class->class_name }}</h1>
        <a href="{{ route('classes.show', $class->id) }}" class="text-gray-600 hover:text-gray-800">
            Back to Class Details
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    @if (session('error'))
    <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        <button type="button" class="float-right text-red-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <div class="bg-white rounded shadow-md p-6">
        <form action="{{ route('classes.update-courses', $class->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Assign Courses to {{ $class->class_name }}</h3>
                
                <!-- Current Assigned Courses -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-700 mb-2">Currently Assigned Courses</h4>
                    
                    @if($class->courses->isEmpty())
                        <p class="text-gray-500 italic">No courses assigned to this class yet.</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-200 mb-4">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($class->courses as $course)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $course->code }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $course->name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $course->pivot->semester }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $course->pivot->year }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
                
                <!-- Add New Courses -->
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Add/Update Courses</h4>
                    
                    <div id="courses-container">
                        <!-- Initial course row -->
                        <div class="flex flex-wrap -mx-2 mb-4 course-row">
                            <div class="w-full md:w-3/12 px-2 mb-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="course-0">
                                    Course <span class="text-red-500">*</span>
                                </label>
                                <select name="courses[]" id="course-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">Select Course</option>
                                    @foreach($availableCourses as $course)
                                        <option value="{{ $course->id }}">
                                            {{ $course->code }} - {{ $course->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="w-full md:w-2/12 px-2 mb-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="semester-0">
                                    Semester <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="1" max="10" name="semester[]" id="semester-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="1">
                            </div>
                            
                            <div class="w-full md:w-2/12 px-2 mb-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="year-0">
                                    Year <span class="text-red-500">*</span>
                                </label>
                                <input type="number" min="1" max="5" name="year[]" id="year-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="1">
                            </div>
                            
                            <div class="w-full md:w-4/12 px-2 mb-2">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="notes-0">
                                    Notes
                                </label>
                                <input type="text" name="notes[]" id="notes-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            </div>
                            
                            <div class="w-full md:w-1/12 px-2 mb-2 flex items-end">
                                <button type="button" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 remove-course-row hidden">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <button type="button" id="add-course-row" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">
                            Add Another Course
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('classes.show', $class->id) }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Save Courses
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowCounter = 1;
        
        // Add new course row
        document.getElementById('add-course-row').addEventListener('click', function() {
            const container = document.getElementById('courses-container');
            const template = document.querySelector('.course-row').cloneNode(true);
            
            // Update IDs and names
            const selects = template.querySelectorAll('select');
            const inputs = template.querySelectorAll('input');
            
            selects.forEach(select => {
                select.id = select.id.replace('-0', `-${rowCounter}`);
                select.value = '';
            });
            
            inputs.forEach(input => {
                input.id = input.id.replace('-0', `-${rowCounter}`);
                if (input.type !== 'checkbox') {
                    input.value = input.name.includes('semester') || input.name.includes('year') ? '1' : '';
                } else {
                    input.checked = false;
                }
            });
            
            // Show remove button
            const removeBtn = template.querySelector('.remove-course-row');
            removeBtn.classList.remove('hidden');
            
            // Add event listener to remove button
            removeBtn.addEventListener('click', function() {
                template.remove();
            });
            
            container.appendChild(template);
            rowCounter++;
        });
        
        // Enable remove functionality for existing rows
        document.querySelectorAll('.remove-course-row').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.course-row').remove();
            });
        });
    });
</script>
@endpush
@endsection