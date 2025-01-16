@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Edit Class</h1>
    <a href="{{ route('classes.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">Cancel</a>
  </div>

  <div class="bg-white rounded shadow-md p-6">
    <form method="POST" action="{{ route('classes.update', $class) }}">
      @csrf
      @method('PUT')

      <div class="mb-4">
        <label for="class_name" class="block text-sm font-medium text-gray-700">Class Name</label>
        <input type="text" id="class_name" name="class_name" value="{{ old('class_name', $class->class_name) }}" required class="block w-full px-3 py-2 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('class_name') border-red-500 @enderror">
        @error('class_name')
        <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
      </div>

      <div class="mb-4">
        <label for="section" class="block text-sm font-medium text-gray-700">Section (Optional)</label>
        <input type="text" id="section" name="section" value="{{ old('section', $class->section) }}" class="block w-full px-3 py-2 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('section') border-red-500 @enderror">
        @error('section')
        <span class="text-xs text-red-500">{{ $message }}</span>
        @enderror
      </div>

      <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Update Class</button>
    </form>
  </div>
</div>
@endsection