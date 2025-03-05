<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('School Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between mb-6">
                        <h3 class="text-lg font-semibold">{{ $school->name }}</h3>
                        <div>
                            <a href="{{ route('schools.edit', $school) }}" class="px-4 py-2 bg-indigo-500 text-white rounded hover:bg-indigo-600 mr-2">Edit</a>
                            <a href="{{ route('schools.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Back to List</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">School Name</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $school->name }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Address</h4>
                                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $school->address }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $school->phone }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Email</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $school->email }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Status</h4>
                                <p class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $school->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $school->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div>
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Logo</h4>
                                @if($school->logo)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }} Logo" class="max-h-40 w-auto">
                                    </div>
                                @else
                                    <p class="mt-1 text-sm text-gray-500">No logo uploaded</p>
                                @endif
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Created At</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $school->created_at->format('F d, Y h:i A') }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">Last Updated</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $school->updated_at->format('F d, Y h:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 