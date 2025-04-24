<x-app-layout>
    <x-slot name="title">
        Welcome to Marks System
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Welcome to Marks Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Marks System Test Page</h1>
                    
                    <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        <p>If you can see this page, the marks routes are working correctly!</p>
                        <p class="mt-2">Current time: {{ now() }}</p>
                    </div>
                    
                    <div class="mt-6">
                        <h2 class="text-xl font-semibold mb-3">Available Routes:</h2>
                        <ul class="list-disc pl-6 space-y-2">
                            <li>
                                <a href="{{ route('marks.dashboard') }}" class="text-blue-600 hover:underline">
                                    Marks Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('marks.select') }}" class="text-blue-600 hover:underline">
                                    Select Exam/Subject
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('marks.subjectEntry') }}" class="text-blue-600 hover:underline">
                                    Subject-Based Marks Entry
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 