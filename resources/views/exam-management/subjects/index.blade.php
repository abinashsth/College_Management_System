@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Subjects</h2>
        @can('create', App\Models\Subject::class)
        <a href="{{ route('subjects.create') }}" class="bg-[#37a6bc] hover:bg-[#2c849c] text-white font-bold py-2 px-4 rounded">
            Add New Subject
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
                    <th class="py-3 px-6 text-left">Code</th>
                    <th class="py-3 px-6 text-left">Name</th>
                    <th class="py-3 px-6 text-left">Classes</th>
                    <th class="py-3 px-6 text-center">Status</th>
                    <th class="py-3 px-6 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @forelse($subjects as $subject)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6 text-left">{{ $subject->code }}</td>
                    <td class="py-3 px-6 text-left">{{ $subject->name }}</td>
                    <td class="py-3 px-6 text-left">
                        <div class="flex flex-wrap gap-1">
                            @foreach($subject->classes as $class)
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                {{ $class->name }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <span class="@if($subject->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif text-xs font-medium px-2.5 py-0.5 rounded">
                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-3 px-6 text-center">
                        <div class="flex item-center justify-center gap-2">
                            @can('view', $subject)
                            <a href="{{ route('subjects.show', $subject) }}" class="text-blue-500 hover:text-blue-700">
                                <i class="fas fa-eye"></i>
                            </a>
                            @endcan

                            @can('update', $subject)
                            <a href="{{ route('subjects.edit', $subject) }}" class="text-yellow-500 hover:text-yellow-700">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan

                            @can('delete', $subject)
                            <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this subject?');">
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
                    <td colspan="5" class="py-3 px-6 text-center text-gray-500">
                        No subjects found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $subjects->links() }}
    </div>
</div>
@endsection
