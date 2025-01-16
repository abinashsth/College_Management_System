@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Users</h1>
        <a href="{{ route('users.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Create User
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <!-- Users Table -->
    <div class="overflow-x-auto bg-white rounded shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b py-3 px-4">Name</th>
                    <th class="border-b py-3 px-4">Email</th>
                    <th class="border-b py-3 px-4">Role</th>
                    <th class="border-b py-3 px-4">Created At</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">{{ $user->name }}</td>
                    <td class="border-b py-3 px-4">{{ $user->email }}</td>
                    <td class="border-b py-3 px-4"> {{ $user->roles->pluck('name')->implode(', ') }}
                        <!-- @forelse($user->roles as $role)
                            {{ $role->name }}<br>
                        @empty
                            N/A
                        @endforelse -->
                    </td>
                    <td class="border-b py-3 px-4">{{ $user->created_at->format('d m, Y') }}</td>
                    <td class="border-b py-3 px-4 text-center">
                        <a href="{{ route('users.edit', $user->id) }}" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600" 
                           aria-label="Edit User">
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-4">
    {{ $users->links() }}
</div>
</div>
@endsection
