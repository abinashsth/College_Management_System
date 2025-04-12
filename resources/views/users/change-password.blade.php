@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <div class="max-w-xl mx-auto">
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">
                            {{ __('Change Password') }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-600">
                            {{ __('Ensure your account is using a secure password.') }}
                        </p>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-400 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-400 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('change.password') }}" class="space-y-6">
                        @csrf

                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700">
                                {{ __('Current Password') }}
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="password" 
                                       name="current_password" 
                                       id="current_password"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 @error('current_password') border-red-300 @enderror"
                                       required>
                                @error('current_password')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                {{ __('New Password') }}
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50 @error('password') border-red-300 @enderror"
                                       required>
                                @error('password')
                                    <div class="mt-1 text-sm text-red-600">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                {{ __('Confirm New Password') }}
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring focus:ring-teal-200 focus:ring-opacity-50"
                                       required>
                            </div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="mt-4 text-sm text-gray-600">
                            <p class="font-medium mb-2">Password must contain:</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-500">
                                <li>At least 8 characters</li>
                                <li>At least one uppercase letter</li>
                                <li>At least one lowercase letter</li>
                                <li>At least one number</li>
                                <li>At least one special character</li>
                            </ul>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" 
                                    class="px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500 transition-colors duration-200">
                                {{ __('Change Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 