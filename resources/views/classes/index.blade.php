@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Classes</h1>
        <a href="{{ route('classes.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Create Class
        </a>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    @if (session('error'))
    <div id="error-alert" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
        <button type="button" class="float-right text-red-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <!-- Classes Table -->
    <div class="overflow-x-auto bg-white rounded shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b py-3 px-4">ID</th>
                    <th class="border-b py-3 px-4">Class Name</th>
                    <th class="border-b py-3 px-4">Academic Year</th>
                    <th class="border-b py-3 px-4">Department</th>
                    <th class="border-b py-3 px-4">Program</th>
                    <th class="border-b py-3 px-4">Capacity</th>
                    <th class="border-b py-3 px-4">Students</th>
                    <th class="border-b py-3 px-4">Status</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($classes as $class)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">{{ $class->id }}</td>
                    <td class="border-b py-3 px-4">{{ $class->class_name }}</td>
                    <td class="border-b py-3 px-4">{{ $class->academicYear->name ?? 'Not Assigned' }}</td>
                    <td class="border-b py-3 px-4">{{ $class->department->name ?? 'Not Assigned' }}</td>
                    <td class="border-b py-3 px-4">{{ $class->program->name ?? 'Not Assigned' }}</td>
                    <td class="border-b py-3 px-4">{{ $class->capacity }}</td>
                    <td class="border-b py-3 px-4">{{ $class->students_count }}</td>
                    <td class="border-b py-3 px-4">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $class->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($class->status) }}
                        </span>
                    </td>
                    <td class="border-b py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('classes.show', $class->id) }}" 
                               class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                View
                            </a>
                            <a href="{{ route('classes.edit', $class->id) }}" 
                               class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                                Edit
                            </a>
                            <form action="{{ route('classes.destroy', $class->id) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this class?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500">No classes found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $classes->links() }}
    </div>
</div>
@endsection