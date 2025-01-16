@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <h1 class="text-2xl font-bold text-gray-800 mb-6">Add Class</h1>

  <div class="bg-white rounded shadow-md p-6">
    <form method="POST" action="{{ route('classes.store') }}">
      @csrf

      <div class="mb-4">
        <label for="class_name" class="block text-sm font-medium text-gray-700">Class Name</label>
        <input type="text" id="class_name" name="class_name" required class="block w-full px-3 py-2 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>

      <div class="mb-4">
        <label for="section" class="block text-sm font-medium text-gray-700">Section (Optional)</label>
        <input type="text" id="section" name="section" class="block w-full px-3 py-2 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
      </div>

      <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">Save Class</button>
    </form>
  </div>
</div>
@endsection