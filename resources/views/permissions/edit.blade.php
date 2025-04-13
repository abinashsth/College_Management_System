@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Permission</h1>
        <a href="{{ route('permissions.index') }}" class="text-gray-600 hover:text-gray-800">
            Back to Permissions
        </a>
    </div>

    <div class="bg-white rounded shadow-md max-w-lg mx-auto p-6">
        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Permission Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $permission->name) }}" required
                       class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none"
                       placeholder="Enter permission name">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end space-x-2">
                <a href="{{ route('permissions.index') }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Update Permission
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
