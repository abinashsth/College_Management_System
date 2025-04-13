@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    
        <div class="mb-6">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Create New Role</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Create a new role and assign permissions to it.
                </p>
            </div>
        </div>

        <div class="mb-6">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="shadow overflow-hidden sm:rounded-md">
                    <div class="px-4 py-5 bg-white space-y-6 sm:p-6">
                        <!-- Role Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                            <input type="text" name="name" id="name" 
                                   class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md @error('name') border-red-500 @enderror"
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Permissions -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-md">
                                @foreach($permissions as $permission)
                                    <div class="flex items-center">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->name }}"
                                               id="permission_{{ $permission->id }}"
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                               {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                        <label for="permission_{{ $permission->id }}" 
                                               class="ml-2 block text-sm text-gray-900">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('permissions')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 space-x-2">
                        <a href="{{ route('roles.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Role
                        </button>
                    </div>
                </div>
            </form>
        </div>
    
</div>
@endsection
