@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Edit User</h1>

    <!-- //crating a form to update the user details and id also\ -->
    <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- // created all the form required for update user -- name, email, password, password_confirmation, roles -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 px-4 py-2 rounded">
            @error('name')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>
        <!-- //continue the form for email, password, password_confirmation, roles -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 px-4 py-2 rounded">
            @error('email')
            <span class="text-red-500">{{ $message }}</span>
            @enderror

        </div>
        <!-- // continue the form for password, password_confirmation, roles -->
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full border
            border-gray-300 px-4 py-2 rounded">
            @error('password')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
            </div>
            <!-- // continue the form for password_confirmation, roles -->
            <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w
            -full border border-gray-300 px-4 py-2 rounded">
            </div>
            <!-- // continue the form for roles -->
            <div class="mb-4">
            <label>Roles:</label>
            @foreach($roles as $role)
            <div>
                <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                <label>{{ $role->name }}</label>
            </div>
            @endforeach
            </div>
            <!-- // continue the form for submit button -->
            <div class="mb-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-whit
                e font-bold py-2 px-4 rounded">Update User</button>
            </div>
        </form>
    



    <!-- <form action="{{ route('users.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full border border-gray-300 px-4 py-2 rounded">
            @error('name')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full border border-gray-300 px-4 py-2 rounded">
            @error('email')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full border border-gray-300 px-4 py-2 rounded">
            @error('password')
            <span class="text-red-500">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 px-4 py-2 rounded">
        </div>

        <div class="mb-4">
            <label>Roles:</label>
            @foreach($roles as $role)
                <div>
                    <input type="checkbox" id="role-{{ $role->id }}" name="roles[]" value="{{ $role->id }}" 
                        {{ $user->roles->contains($role->id) ? 'checked' : '' }}>
                    <label for="role-{{ $role->id }}">{{ $role->name }}</label>
                </div>
            @endforeach
        </div>

        <div class="mb-4">
            <button type="submit" class="bg-teal-600 text-white px-4 py-2 rounded hover:bg-teal-700">Update User</button>
        </div>
    </form> -->
</div>
@endsection
