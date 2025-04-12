@extends('layouts.app')

@section('title', 'Manage Faculties')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Faculty Management</h1>
        <a href="{{ route('faculties.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
            <i class="fas fa-plus mr-2"></i> Add New Faculty
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200 text-gray-700 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-left">Code</th>
                        <th class="py-3 px-6 text-left">Departments</th>
                        <th class="py-3 px-6 text-left">Status</th>
                        <th class="py-3 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                    @forelse($faculties as $faculty)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-6 text-left whitespace-nowrap font-medium">
                                <a href="{{ route('faculties.show', $faculty) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $faculty->name }}
                                </a>
                            </td>
                            <td class="py-3 px-6 text-left">{{ $faculty->code }}</td>
                            <td class="py-3 px-6 text-left">
                                {{ $faculty->departments->count() }}
                            </td>
                            <td class="py-3 px-6 text-left">
                                @if($faculty->status)
                                    <span class="bg-green-100 text-green-800 py-1 px-3 rounded-full text-xs">Active</span>
                                @else
                                    <span class="bg-red-100 text-red-800 py-1 px-3 rounded-full text-xs">Inactive</span>
                                @endif
                            </td>
                            <td class="py-3 px-6 text-center">
                                <div class="flex justify-center space-x-2">
                                    <a href="{{ route('faculties.dashboard', $faculty) }}" class="text-gray-600 hover:text-blue-600" title="Dashboard">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="{{ route('faculties.show', $faculty) }}" class="text-gray-600 hover:text-blue-600" title="View details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('faculties.edit', $faculty) }}" class="text-gray-600 hover:text-yellow-600" title="Edit faculty">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('faculties.destroy', $faculty) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this faculty? All associated data will be lost.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-600 hover:text-red-600" title="Delete faculty">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="border-b border-gray-200">
                            <td class="py-4 px-6 text-center text-gray-500" colspan="5">No faculties found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-3">
            {{ $faculties->links() }}
        </div>
    </div>
</div>
@endsection 