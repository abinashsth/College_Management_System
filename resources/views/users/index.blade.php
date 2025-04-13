@extends('layouts.app', ['title' => 'User Management'])

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">User Management</h1>
            <p class="text-gray-600 text-sm mt-1">Manage system users and their access permissions</p>
        </div>
        @can('create users')
        <a href="{{ route('users.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Create User
        </a>
        @endcan
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" class="pl-10 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" placeholder="Search by name or email" value="{{ request('search') }}">
                </div>
            </div>
            
            <div class="w-full md:w-1/4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                <select name="role" id="role" class="focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    <option value="">All Roles</option>
                    @foreach(Spatie\Permission\Models\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-md">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="overflow-x-auto bg-white rounded-lg shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50">
                <tr>
                    <th class="border-b py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="border-b py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th class="border-b py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="border-b py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                    <th class="border-b py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                    <th class="border-b py-3 px-4 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 text-sm text-gray-900">{{ $user->id }}</td>
                    <td class="py-3 px-4">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                @if($user->hasRole('super-admin'))
                                    <div class="text-xs text-red-600">Super Administrator</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-900">{{ $user->email }}</td>
                    <td class="py-3 px-4">
                        <div class="flex flex-wrap gap-1">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $role->name == 'super-admin' ? 'bg-red-100 text-red-800' : 
                                   ($role->name == 'admin' ? 'bg-blue-100 text-blue-800' : 
                                   ($role->name == 'teacher' ? 'bg-green-100 text-green-800' : 
                                   ($role->name == 'accountant' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-purple-100 text-purple-800'))) }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                    <td class="py-3 px-4 text-sm font-medium">
                        <div class="flex space-x-2">
                            @can('edit users')
                                <a href="{{ route('users.edit', $user->id) }}" 
                                class="text-blue-600 hover:text-blue-900" title="Edit User">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            
                            @if(!$user->hasRole('super-admin') && auth()->id() != $user->id)
                                @can('delete users')
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" 
                                        onsubmit="return confirm('Are you sure you want to delete this user?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-gray-500">No users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="mt-6">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
