@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ __('Academic Year Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('settings.academic-year.edit', $academicYear) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('settings.academic-year.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Academic Year Details -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $academicYear->name }}</h3>
                
                <div class="bg-gray-50 border rounded-lg p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="text-sm font-medium text-gray-500">Name</div>
                            <div class="mt-1 text-base">{{ $academicYear->name }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Year Range</div>
                            <div class="mt-1 text-base">{{ $academicYear->year_range }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Start Date</div>
                            <div class="mt-1 text-base">{{ $academicYear->start_date->format('F d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">End Date</div>
                            <div class="mt-1 text-base">{{ $academicYear->end_date->format('F d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Status</div>
                            <div class="mt-1">
                                @if($academicYear->is_current)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Current
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Sessions</div>
                            <div class="mt-1 text-base">{{ $academicYear->sessions->count() }}</div>
                        </div>
                        @if($academicYear->description)
                            <div class="col-span-2">
                                <div class="text-sm font-medium text-gray-500">Description</div>
                                <div class="mt-1 text-base">{{ $academicYear->description }}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Academic Sessions -->
                <div class="mt-10">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Academic Sessions</h3>
                        <a href="{{ route('settings.academic-year.sessions.create', $academicYear) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                            <i class="fas fa-plus mr-2"></i> Add New Session
                        </a>
                    </div>

                    @if($academicYear->sessions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Range</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($academicYear->sessions as $session)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $session->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ ucfirst($session->type) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $session->start_date->format('M d, Y') }} - {{ $session->end_date->format('M d, Y') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($session->is_current)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Current
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('settings.academic-year.sessions.edit', ['academicYear' => $academicYear, 'session' => $session]) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('settings.academic-year.sessions.destroy', ['academicYear' => $academicYear, 'session' => $session]) }}" method="POST" class="inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this session? This cannot be undone.')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 text-sm text-yellow-800">
                            No sessions found for this academic year. Click on "Add New Session" to create one.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 