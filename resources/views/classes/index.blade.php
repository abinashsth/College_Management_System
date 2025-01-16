@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Classes</h1>
    <a href="{{ route('classes.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
      Add New Class
    </a>
  </div>

  @if (session('success'))
  <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
    <span class="block sm:inline">{{ session('success') }}</span>
    <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
  </div>
  @endif

  <div class="overflow-x-auto bg-white rounded shadow-md">
    <table class="w-full text-left border-collapse">
      <thead class="bg-teal-100">
        <tr>
          <th class="border-b py-3 px-4">Class Name</th>
          <th class="border-b py-3 px-4">Section</th>
          <th class="border-b py-3 px-4">Total Students</th>
          <th class="border-b py-3 px-4">Actions</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($classes as $class)
        <tr class="hover:bg-gray-50">
          <td class="border-b py-3 px-4">{{ $class->class_name }}</td>
          <td class="border-b py-3 px-4">{{ $class->section ?? 'N/A' }}</td>
          <td class="border-b py-3 px-4">{{ $class->students_count }}</td>
          <td class="border-b py-3 px-4 flex space-x-2">
            <a href="{{ route('classes.edit', $class) }}" class="bg-teal-600 text-white px-3 py-1 rounded hover:bg-teal-700">Edit</a>
            <form action="{{ route('classes.destroy', $class) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure? This will also delete all students in this class.')">
              @csrf
              @method('DELETE')
              <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Delete</button>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $classes->links() }}
  </div>
</div>
@endsection