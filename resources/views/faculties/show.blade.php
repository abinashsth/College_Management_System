@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center">
        <h3 class="text-gray-700 text-3xl font-medium">Faculty Details</h3>
        <a href="{{ route('faculties.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Faculties
        </a>
    </div>

    <div class="mt-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Faculty Name</label>
                    <p class="text-gray-600">{{ $faculty->faculty_name }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <p class="text-gray-600">{{ $faculty->description }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Created At</label>
                    <p class="text-gray-600">{{ $faculty->created_at->format('F d, Y H:i:s') }}</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Updated At</label>
                    <p class="text-gray-600">{{ $faculty->updated_at->format('F d, Y H:i:s') }}</p>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('faculties.edit', $faculty) }}" class="bg-[#37a2bc] hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded" 
                            onclick="return confirm('Are you sure you want to delete this faculty?')">
                            <i class="fas fa-trash mr-2"></i>Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 