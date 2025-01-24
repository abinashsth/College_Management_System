@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Examiner Assignments</h2>
        @can('create', App\Models\ExaminerAssignment::class)
        <a href="{{ route('examiner-assignments.create') }}" class="bg-[#37a6bc] hover:bg-[#2c849c] text-white font-bold py-2 px-4 rounded">
            Add New Assignment
        </a>
        @endcan
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-md rounded my-6">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Examiner</th>
                    <th class="py-3 px-6 text-left">Class</th>
                    <th class="py-3 px-6 text-left">Subject</th>
                    <th class="py-3 px-6 text-left">Academic Session</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($assignments as $assignment)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">
                        <div class="flex items-center">
                            <div class="mr-2">
                                <img class="w-6 h-6 rounded-full" src="{{ $assignment->user->profile_photo_url }}" alt="">
                            </div>
                            <span>{{ $assignment->user->name }}</span>
                        </div>
                    </td>
                    <td class="py-3 px-6 text-left">{{ $assignment->class->name }}</td>
                    <td class="py-3 px-6 text-left">{{ $assignment->subject->name }}</td>
                    <td class="py-3 px-6 text-left">{{ $assignment->academicSession->name }}</td>
                    <td class="py-3 px-6 text-center">
                        <span class="@if($assignment->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center gap-2">
                            @can('update', $assignment)
                            <a href="{{ route('examiner-assignments.edit', $assignment) }}" class="text-yellow-500 hover:text-yellow-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            @can('delete', $assignment)
                            <form action="{{ route('examiner-assignments.destroy', $assignment) }}" method="POST" class="inline" 
                                onsubmit="return confirm('Are you sure you want to delete this assignment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr class="border-b border-gray-200">
                    <td colspan="6" class="py-3 px-6 text-center text-gray-500">
                        No examiner assignments found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
