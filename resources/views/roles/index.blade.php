@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Roles</h1>
        <a href="{{ route('roles.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Create Role
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <!-- Roles Table -->
    <div class="overflow-x-auto bg-white rounded shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b py-3 px-4">ID</th>
                    <th class="border-b py-3 px-4">Name</th>
                    <th class="border-b py-3 px-4">Permissions</th>
                    <th class="border-b py-3 px-4">Created At</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">{{ $role->id }}</td>
                    <td class="border-b py-3 px-4">{{ $role->name }}</td>
                    <td class="border-b py-3 px-4">
                        {{ $role->permissions->pluck('name')->implode(', ') }}
                    </td>
                    <td class="border-b py-3 px-4">{{ $role->created_at->format('d m, Y') }}</td>
                    <td class="border-b py-3 px-4 flex space-x-2">
                        <a href="{{ route('roles.edit', $role->id) }}" 
                        class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                            Edit
                        </a>
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this role?');" class="inline">
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
                    <td colspan="5" class="text-center py-4 text-gray-500">No roles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
        {{ $roles->links() }}
    </div>
</div>
@endsection
