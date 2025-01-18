@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Class</h1>
        <a href="{{ route('classes.index') }}" class="text-gray-600 hover:text-gray-800">
            Back to Classes
        </a>
    </div>

    <div class="bg-white rounded shadow-md max-w-lg mx-auto p-6">
        <form action="{{ route('classes.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="class_name" class="block text-gray-700 font-medium mb-2">Class Name</label>
                <input type="text" id="class_name" name="class_name" value="{{ old('class_name') }}" required
                       class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                       placeholder="Enter class name">
                @error('class_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="section" class="block text-gray-700 font-medium mb-2">Section</label>
                <input type="text" id="section" name="section" value="{{ old('section') }}"
                       class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                       placeholder="Enter section (optional)">
                @error('section')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('classes.index') }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Create Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection