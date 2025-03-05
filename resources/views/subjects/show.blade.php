@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Subject Details</h3>
        <div class="flex space-x-4">
            @can('edit subjects')
            <a href="{{ route('subjects.edit', $subject) }}" class="bg-[#37a2bc] hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-edit mr-2"></i>Edit Subject
            </a>
            @endcan
            <a href="{{ route('subjects.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back to Subjects
            </a>
        </div>
    </div>

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-lg font-semibold mb-2">Subject Code</h4>
                        <p class="text-gray-600">{{ $subject->subject_code }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-2">Subject Name</h4>
                        <p class="text-gray-600">{{ $subject->subject_name }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-2">Course</h4>
                        <p class="text-gray-600">{{ $subject->course->course_name }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-2">Credit Hours</h4>
                        <p class="text-gray-600">{{ $subject->credit_hours }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-2">Description</h4>
                        <p class="text-gray-600">{{ $subject->description ?? 'No description available' }}</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-2">Status</h4>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $subject->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $subject->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                @can('delete subjects')
                <div class="mt-6 border-t pt-6">
                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" 
                            onclick="return confirm('Are you sure you want to delete this subject?')">
                            <i class="fas fa-trash mr-2"></i>Delete Subject
                        </button>
                    </form>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection 