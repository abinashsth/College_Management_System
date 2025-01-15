@extends('layouts.app')

@section('content')
@if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        {{ session('success') }}
    </div>
@endif
<div class="container mx-auto px-4 py-6">

    <h2 class="text-xl font-semibold mb-2">Your Profile:</h2>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        
        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name</label>
            <input type="text" name="name" id="name" value="{{ auth()->user()->name }}" class="w-full border border-gray-300 px-4 py-2 rounded">
        </div>

        <!-- Email -->
        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email</label>
            <input type="email" name="email" id="email" value="{{ auth()->user()->email }}" class="w-full border border-gray-300 px-4 py-2 rounded">
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="block text-gray-700">Password</label>
            <input type="password" name="password" id="password" class="w-full border border-gray-300 px-4 py-2 rounded">
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="block text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full border border-gray-300 px-4 py-2 rounded">
        </div>

        <!-- Roles Section -->
        <h2 class="text-xl font-semibold mb-2">Your Roles:</h2>
        @if($roles && $roles->isNotEmpty())
            @foreach ($roles as $role)
                @if(auth()->user()->roles->contains($role->id))
                    <p class="text-gray-700">{{ $role->name }}</p>
                @endif
            @endforeach
        @else
            <p class="text-gray-500">No roles assigned.</p>
        @endif

        <!-- Submit Button -->
        <div class="mt-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Update Profile</button>
        </div>
    </form>
</div>
@endsection
