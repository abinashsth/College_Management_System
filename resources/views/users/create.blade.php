@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold">Create New User</h1>
    <form action="{{ route('users.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" id="name" name="name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="roles" class="block text-gray-700 font-medium mb-2">Roles</label>
            <div class="flex flex-wrap -mx-3 mb-6">
                @foreach($roles as $role)
                            <div class="w-full md:w-1/2 xl:w-1/3 px
                    -3 mb-6 md:mb-0">
                                <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="grid-state">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" class="mr-2 leading-tight">
                                    {{ $role->name }}
                                </label>
                            </div>
                @endforeach
            </div>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md">Create User</button>
    </form>
</div>
@endsection
