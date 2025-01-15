@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Permission</h1>
    
    <form action="{{ route('permissions.store') }}" method="POST" class="bg-white p-6 rounded shadow-md max-w-lg mx-auto">
        @csrf
        <!-- Permission Name Input -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-medium mb-2">Permission Name</label>
            <input type="text" id="name" name="name" required 
                   class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none" 
                   placeholder="Enter permission name">
        </div>
        
        <!-- Submit Button -->
        <div class="text-right">
            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                Create Permission
            </button>
        </div>
    </form>
</div>
@endsection
