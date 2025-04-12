<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Syllabus for') }}: {{ $subject->code }} - {{ $subject->name }}
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

                    <form method="POST" action="{{ route('subjects.syllabus.update', $subject) }}">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Learning Objectives') }}</h3>
                            <textarea name="learning_objectives" id="learning-objectives" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('learning_objectives', $subject->learning_objectives) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ __('What students should learn in this subject') }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Syllabus Content') }} *</h3>
                            <textarea name="syllabus" id="syllabus" rows="10" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>{{ old('syllabus', $subject->syllabus) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Detailed content to be covered in this subject') }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Teaching Methods') }}</h3>
                            <textarea name="teaching_methods" id="teaching-methods" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('teaching_methods', $subject->teaching_methods) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ __('How this subject will be taught (lectures, labs, discussions, etc.)') }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Grading Policy') }}</h3>
                            <textarea name="grading_policy" id="grading-policy" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('grading_policy', $subject->grading_policy) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ __('How students will be evaluated (assignments, exams, projects, etc.)') }}</p>
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Reference Materials') }}</h3>
                            <textarea name="reference_materials" id="reference-materials" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('reference_materials', $subject->reference_materials) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ __('Textbooks, online resources, and other materials for this subject') }}</p>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Save Syllabus') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // You can add a WYSIWYG editor integration here if needed
        document.addEventListener('DOMContentLoaded', function() {
            // For example, initialize a rich text editor for the syllabus
            // This is just a placeholder - you'd need to include the appropriate library
            /*
            if (typeof ClassicEditor !== 'undefined') {
                ClassicEditor
                    .create(document.querySelector('#syllabus'))
                    .catch(error => {
                        console.error(error);
                    });
            }
            */
        });
    </script>
</x-app-layout> 