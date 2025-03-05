@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between">
        <h3 class="text-gray-700 text-3xl font-medium">Class Details</h3>
        <a href="{{ route('classes.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-2">Class Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Class Name</p>
                            <p class="mt-1">{{ $class->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Course</p>
                            <p class="mt-1">{{ $class->course->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Faculty</p>
                            <p class="mt-1">{{ $class->faculty->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Session</p>
                            <p class="mt-1">{{ $class->session->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Status</p>
                            <p class="mt-1">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $class->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($class->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    @can('edit classes')
                    <a href="{{ route('classes.edit', $class) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit mr-2"></i>Edit Class
                    </a>
                    @endcan
                    @can('delete classes')
                    <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this class?')">
                            <i class="fas fa-trash mr-2"></i>Delete Class
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection