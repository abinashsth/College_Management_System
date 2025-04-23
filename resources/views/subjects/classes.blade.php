@extends('layouts.app')

@section('title', 'Assign Classes - ' . $subject->name)

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Assign Classes: {{ $subject->code }} - {{ $subject->name }}</h1>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('subjects.show', $subject) }}" class="px-4 py-2 bg-gray-600 text-white rounded shadow hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-1"></i> Back to Subject
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Current Class Assignments</h6>
        </div>
        <div class="p-6">
            @if($subject->classes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Program</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($subject->classes as $class)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->class_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->department->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->program->name ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->pivot->semester }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $class->pivot->year }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($class->pivot->is_core)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Core</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Elective</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center text-gray-500">This subject is not assigned to any classes yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden mt-6">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Assign to Classes</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('subjects.classes.update', $subject) }}" method="POST">
                @csrf
                @method('POST')

                <div id="classes-container">
                    <!-- Initial class row -->
                    <div class="flex flex-wrap -mx-2 mb-6 class-row">
                        <div class="w-full md:w-1/3 px-2 mb-4">
                            <label for="class-0" class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-600">*</span></label>
                            <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                id="class-0" name="classes[]" required>
                                <option value="">Select Class</option>
                                @foreach($availableClasses as $class)
                                    <option value="{{ $class->id }}">
                                        {{ $class->class_name }} | {{ $class->department->name ?? 'N/A' }} | {{ $class->program->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classes.*')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full md:w-1/6 px-2 mb-4">
                            <label for="semester-0" class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-600">*</span></label>
                            <input type="number" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                id="semester-0" name="semester[]" min="1" value="1" required>
                            @error('semester.*')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full md:w-1/6 px-2 mb-4">
                            <label for="year-0" class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-600">*</span></label>
                            <input type="number" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                id="year-0" name="year[]" min="1" value="1" required>
                            @error('year.*')
                                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="w-full md:w-1/6 px-2 mb-4 flex items-end">
                            <div class="flex items-center h-10">
                                <input type="hidden" name="is_core[0]" value="0">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                    id="is-core-0" name="is_core[0]" value="1" checked>
                                <label for="is-core-0" class="ml-2 block text-sm text-gray-700">Core Subject</label>
                            </div>
                        </div>

                        <div class="w-full md:w-1/6 px-2 mb-4">
                            <label for="notes-0" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                                id="notes-0" name="notes[]" placeholder="Optional notes">
                        </div>
                    </div>
                </div>

                <div class="flex items-center mb-6">
                    <button type="button" id="add-class" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <i class="fas fa-plus mr-1"></i> Add Another Class
                    </button>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('subjects.show', $subject) }}" class="px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition mr-2">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded shadow hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                        <i class="fas fa-save mr-1"></i> Save Class Assignments
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let classCount = 1;
    
    document.getElementById('add-class').addEventListener('click', function() {
        const container = document.getElementById('classes-container');
        const newRow = document.createElement('div');
        newRow.className = 'flex flex-wrap -mx-2 mb-6 class-row';
        
        newRow.innerHTML = `
            <div class="w-full md:w-1/3 px-2 mb-4">
                <label for="class-${classCount}" class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-600">*</span></label>
                <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                    id="class-${classCount}" name="classes[]" required>
                    <option value="">Select Class</option>
                    @foreach($availableClasses as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->class_name }} | {{ $class->department->name ?? 'N/A' }} | {{ $class->program->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-1/6 px-2 mb-4">
                <label for="semester-${classCount}" class="block text-sm font-medium text-gray-700 mb-1">Semester <span class="text-red-600">*</span></label>
                <input type="number" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                    id="semester-${classCount}" name="semester[]" min="1" value="1" required>
            </div>

            <div class="w-full md:w-1/6 px-2 mb-4">
                <label for="year-${classCount}" class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-600">*</span></label>
                <input type="number" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                    id="year-${classCount}" name="year[]" min="1" value="1" required>
            </div>

            <div class="w-full md:w-1/6 px-2 mb-4 flex items-end">
                <div class="flex items-center h-10">
                    <input type="hidden" name="is_core[${classCount}]" value="0">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                        id="is-core-${classCount}" name="is_core[${classCount}]" value="1" checked>
                    <label for="is-core-${classCount}" class="ml-2 block text-sm text-gray-700">Core Subject</label>
                </div>
            </div>

            <div class="w-full md:w-1/6 px-2 mb-4">
                <label for="notes-${classCount}" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                    id="notes-${classCount}" name="notes[]" placeholder="Optional notes">
            </div>
            
            <div class="w-full flex justify-end px-2 mb-2">
                <button type="button" class="remove-class px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                    <i class="fas fa-trash mr-1"></i> Remove
                </button>
            </div>
        `;
        
        container.appendChild(newRow);
        classCount++;
        
        // Add event listeners to all remove buttons
        document.querySelectorAll('.remove-class').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.class-row').remove();
            });
        });
    });
});
</script>
@endsection 