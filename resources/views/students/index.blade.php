@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Students</h1>
        <a href="{{ route('students.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Create Student
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
                    <th class="border-b py-3 px-4">Photo</th>
                    <th class="border-b py-3 px-4">Reg. No.</th>
                    <th class="border-b py-3 px-4">Name</th>
                    <th class="border-b py-3 px-4">Gender</th>
                    <th class="border-b py-3 px-4">Program</th>
                    <th class="border-b py-3 px-4">Year</th>
                    <th class="border-b py-3 px-4">Enroll Date</th>
                    <th class="border-b py-3 px-4">Parent/Guardian</th>
                    <th class="border-b py-3 px-4">Fee Status</th>
                    <th class="border-b py-3 px-4">Status</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">
                        @if($student->profile_photo)
                        <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->name }}" class="h-10 w-10 rounded-full object-cover">
                        @else
                        <div class="h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                            {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                        </div>
                        @endif
                    </td>
                    <td class="border-b py-3 px-4">{{ $student->registration_number }}</td>
                    <td class="border-b py-3 px-4">{{ $student->name }}</td>
                    <td class="border-b py-3 px-4">{{ ucfirst($student->gender ?? 'N/A') }}</td>
                    <td class="border-b py-3 px-4">{{ $student->program->name ?? 'N/A' }}</td>
                    <td class="border-b py-3 px-4">{{ $student->batch_year ?? 'N/A' }}</td>
                    <td class="border-b py-3 px-4">{{ $student->admission_date ? $student->admission_date->format('d-m-Y') : 'N/A' }}</td>
                    <td class="border-b py-3 px-4">
                        @if($student->guardian_name)
                            {{ $student->guardian_name }}
                        @elseif($student->father_name)
                            {{ $student->father_name }}
                        @elseif($student->mother_name)
                            {{ $student->mother_name }}
                        @else
                            N/A
                        @endif
                    </td>
                    <td class="border-b py-3 px-4">
                        <span class="px-2 py-1 rounded text-sm {{ $student->fee_status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $student->fee_status ? 'Paid' : 'Pending' }}
                        </span>
                    </td>
                    <td class="border-b py-3 px-4">
                        <span class="px-2 py-1 rounded text-sm {{ $student->enrollment_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($student->enrollment_status ?? 'N/A') }}
                        </span>
                    </td>
                    <td class="border-b py-3 px-4 flex space-x-2">
                        <a href="{{ route('students.show', $student->id) }}" 
                           class="bg-teal-500 text-white px-3 py-1 rounded hover:bg-teal-600">
                            View
                        </a>
                        <a href="{{ route('students.edit', $student->id) }}" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                            Edit
                        </a>
                        <form action="{{ route('students.destroy', $student->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this student?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center py-4 text-gray-500">No students found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $students->links() }}
    </div>
</div>
@endsection
