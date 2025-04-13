@extends('layouts.app', ['title' => 'Profile'])

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Profile</h1>
            <p class="text-gray-600 text-sm mt-1">Manage your account information and settings</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Summary Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden col-span-1">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-6 text-white">
                <div class="flex flex-col items-center">
                    <div class="h-24 w-24 rounded-full bg-white p-1">
                        <img class="h-full w-full object-cover rounded-full" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&size=200&background=random" 
                             alt="{{ auth()->user()->name }}">
                    </div>
                    <h3 class="mt-4 text-xl font-semibold">{{ auth()->user()->name }}</h3>
                    <span class="text-blue-100">{{ auth()->user()->roles->pluck('name')->first() }}</span>
                </div>
            </div>
            <div class="p-4 border-t">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500">Email</label>
                    <div class="text-gray-800 flex items-center">
                        <i class="fas fa-envelope mr-2 text-gray-400"></i>
                        {{ auth()->user()->email }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-500">Role</label>
                    <div class="text-gray-800 flex items-center">
                        <i class="fas fa-user-tag mr-2 text-gray-400"></i>
                        @foreach(auth()->user()->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $role->name == 'super-admin' ? 'bg-red-100 text-red-800' : 
                                   ($role->name == 'admin' ? 'bg-blue-100 text-blue-800' : 
                                   ($role->name == 'teacher' ? 'bg-green-100 text-green-800' : 
                                   ($role->name == 'accountant' ? 'bg-yellow-100 text-yellow-800' : 
                                   'bg-purple-100 text-purple-800'))) }}">
                                {{ ucfirst($role->name) }}
                            </span>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Member Since</label>
                    <div class="text-gray-800 flex items-center">
                        <i class="fas fa-calendar-alt mr-2 text-gray-400"></i>
                        {{ auth()->user()->created_at->format('F d, Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Update Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b px-4 py-3 bg-gray-50">
                    <h3 class="font-semibold text-gray-700">Profile Information</h3>
                </div>
                <div class="p-4">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Update -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b px-4 py-3 bg-gray-50">
                    <h3 class="font-semibold text-gray-700">Update Password</h3>
                </div>
                <div class="p-4">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="border-b px-4 py-3 bg-gray-50">
                    <h3 class="font-semibold text-red-600">Delete Account</h3>
                </div>
                <div class="p-4">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
