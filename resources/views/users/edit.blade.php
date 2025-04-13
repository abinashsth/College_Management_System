@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Edit User</h3>
        <p class="mt-1 text-sm text-gray-600">
            Update user information and manage their roles.
        </p>
        <p class="mt-2 text-sm text-gray-500">
            Last updated: {{ $user->updated_at->format('M d, Y H:i A') }}
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            
            <!-- Form Fields -->
            <div class="p-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Full Name
                    </label>
                    <input type="text" name="name" id="name" 
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror"
                        value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">
                        Email Address
                    </label>
                    <input type="email" name="email" id="email" 
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('email') border-red-500 @enderror"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            New Password
                        </label>
                        <input type="password" name="password" id="password" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror"
                            placeholder="Leave blank to keep current password">
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            placeholder="Confirm new password">
                    </div>
                </div>

                <!-- Roles -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Assigned Roles
                    </label>
                    <div class="bg-gray-50 p-4 rounded-md grid grid-cols-2 gap-4">
                        @foreach($roles as $role)
                            <div class="flex items-center">
                                <input type="checkbox" 
                                    name="roles[]" 
                                    value="{{ $role->name }}"
                                    id="role_{{ $role->id }}"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    {{ in_array($role->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                                <label for="role_{{ $role->id }}" 
                                    class="ml-2 block text-sm text-gray-900">
                                    {{ ucfirst($role->name) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('users.index') }}" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit"
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update User
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="p-6">
            <h3 class="text-lg font-medium text-red-600">
                Danger Zone
            </h3>
            <div class="mt-3 text-sm text-gray-500">
                <p>Once you delete a user, there is no going back. Please be certain.</p>
            </div>
            <div class="mt-4">
                <form action="{{ route('users.destroy', $user) }}" method="POST" 
                    onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-flex items-center justify-center px-4 py-2 border border-transparent font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:text-sm">
                        Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" 
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg"
         x-data="{ show: true }"
         x-show="show"
         x-init="setTimeout(() => show = false, 3000)">
        {{ session('error') }}
    </div>
@endif
@endsection
