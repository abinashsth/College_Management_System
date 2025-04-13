@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-2xl font-bold text-gray-900">Create New User</h3>
        <p class="mt-1 text-sm text-gray-600">
            Add a new user and assign their roles.
        </p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            
            <!-- Form Fields -->
            <div class="p-6 space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Full Name
                    </label>
                    <input type="text" name="name" id="name" 
                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror"
                        value="{{ old('name') }}" required>
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
                        value="{{ old('email') }}" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="space-y-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <input type="password" name="password" id="password" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('password') border-red-500 @enderror"
                            required>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            required>
                    </div>
                </div>

                <!-- Roles -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Assign Roles
                    </label>
                    <div class="bg-gray-50 p-4 rounded-md grid grid-cols-2 gap-4">
                        @foreach($roles as $role)
                            <div class="flex items-center">
                                <input type="checkbox" 
                                    name="roles[]" 
                                    value="{{ $role->name }}"
                                    id="role_{{ $role->id }}"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                    {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
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
                    Create User
                </button>
            </div>
        </form>
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
