@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Students</h1>
        <a href="{{ route('students.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Add New Student
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <!-- Students Table -->
    <div class="overflow-x-auto bg-white rounded shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b py-3 px-4">Name</th>
                    <th class="border-b py-3 px-4">Class</th>
                    <th class="border-b py-3 px-4">Contact</th>
                    <th class="border-b py-3 px-4">Email</th>
                    <th class="border-b py-3 px-4">Status</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">{{ $student->name }}</td>
                    <td class="border-b py-3 px-4">
                        @if($student->class)
                        {{ $student->class->class_name }} {{ $student->class->section }}
                        @else
                        <span class="text-gray-500 italic">No Class Assigned</span>
                        @endif
                    </td>
                    <td class="border-b py-3 px-4">{{ $student->contact_number }}</td>
                    <td class="border-b py-3 px-4">{{ $student->email }}</td>
                    <td class="border-b py-3 px-4">
                        <span class="{{ $student->status ? 'text-green-600' : 'text-red-600' }}">
                            {{ $student->status ? 'Enabled' : 'Disabled' }}
                        </span>
                    </td>
                    <td class="border-b py-3 px-4 flex space-x-2">
                        <a href="{{ route('students.edit', $student) }}" 
                        class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                            Edit
                        </a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" 
                              onsubmit="return confirm('Are you sure?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>
@endsection
