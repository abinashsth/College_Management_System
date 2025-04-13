<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Courses for') }}: {{ $subject->code }} - {{ $subject->name }}
            </h2>
            <a href="{{ route('subjects.show', $subject) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                <span>{{ __('Back to Subject') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>{{ __('Whoops!') }}</strong> {{ __('There were some problems with your input.') }}<br><br>
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('subjects.courses.update', $subject) }}">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Currently Associated Courses') }}</h3>
                            
                            @if($subject->courses->count() > 0)
                                <table class="min-w-full bg-white mb-4">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Code') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Course Name') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Semester') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Year') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Core/Elective') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Notes') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subject->courses as $course)
                                            <tr>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $course->code }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $course->name }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $course->pivot->semester ?? 'N/A' }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $course->pivot->year ?? 'N/A' }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $course->pivot->is_core ? __('Core') : __('Elective') }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $course->pivot->notes ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 italic">{{ __('No courses have been associated with this subject yet.') }}</p>
                            @endif
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Associate Courses') }}</h3>
                            
                            <div id="courses-container">
                                <!-- Initial course row -->
                                <div class="flex flex-wrap -mx-2 mb-4 course-row">
                                    <div class="w-full md:w-1/3 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="course-0">
                                            {{ __('Course') }} *
                                        </label>
                                        <select name="courses[]" id="course-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">{{ __('Select Course') }}</option>
                                            @foreach($availableCourses as $course)
                                                <option value="{{ $course->id }}">
                                                    {{ $course->code }} - {{ $course->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="semester-0">
                                            {{ __('Semester') }} *
                                        </label>
                                        <input type="number" name="semester[]" id="semester-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="year-0">
                                            {{ __('Year') }} *
                                        </label>
                                        <input type="number" name="year[]" id="year-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2 flex items-center mt-6">
                                        <input type="checkbox" name="is_core[]" id="is-core-0" class="mr-2" checked>
                                        <label for="is-core-0" class="text-gray-700 text-sm font-bold">
                                            {{ __('Core Subject') }}
                                        </label>
                                    </div>
                                    
                                    <div class="w-full px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes-0">
                                            {{ __('Notes') }}
                                        </label>
                                        <input type="text" name="notes[]" id="notes-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Additional information about this course association">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <button type="button" id="add-course" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm focus:outline-none focus:shadow-outline">
                                    {{ __('+ Add Another Course') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Save Course Associations') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let courseCount = 1;
            
            document.getElementById('add-course').addEventListener('click', function() {
                const container = document.getElementById('courses-container');
                const newRow = document.createElement('div');
                newRow.className = 'flex flex-wrap -mx-2 mb-4 course-row';
                
                newRow.innerHTML = `
                    <div class="w-full md:w-1/3 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="course-${courseCount}">
                            {{ __('Course') }} *
                        </label>
                        <select name="courses[]" id="course-${courseCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">{{ __('Select Course') }}</option>
                            @foreach($availableCourses as $course)
                                <option value="{{ $course->id }}">
                                    {{ $course->code }} - {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="semester-${courseCount}">
                            {{ __('Semester') }} *
                        </label>
                        <input type="number" name="semester[]" id="semester-${courseCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="year-${courseCount}">
                            {{ __('Year') }} *
                        </label>
                        <input type="number" name="year[]" id="year-${courseCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1" required>
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2 flex items-center mt-6">
                        <input type="checkbox" name="is_core[]" id="is-core-${courseCount}" class="mr-2" checked>
                        <label for="is-core-${courseCount}" class="text-gray-700 text-sm font-bold">
                            {{ __('Core Subject') }}
                        </label>
                    </div>
                    
                    <div class="w-full md:w-5/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes-${courseCount}">
                            {{ __('Notes') }}
                        </label>
                        <input type="text" name="notes[]" id="notes-${courseCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Additional information about this course association">
                    </div>
                    
                    <div class="w-full md:w-1/12 px-2 mb-2 flex items-center mt-6">
                        <button type="button" class="remove-course bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm focus:outline-none focus:shadow-outline">
                            {{ __('Remove') }}
                        </button>
                    </div>
                `;
                
                container.appendChild(newRow);
                courseCount++;
                
                // Add event listeners to all remove buttons
                document.querySelectorAll('.remove-course').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.course-row').remove();
                    });
                });
            });
        });
    </script>
</x-app-layout> 