@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ $academicStructure->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('settings.academic-structure.edit', $academicStructure) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('settings.academic-structure.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
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

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h3>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600">Name</p>
                        <p class="text-base text-gray-900">{{ $academicStructure->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600">Code</p>
                        <p class="text-base text-gray-900">{{ $academicStructure->code }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600">Type</p>
                        <p class="text-base text-gray-900">{{ ucfirst($academicStructure->type) }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600">Status</p>
                        @if($academicStructure->is_active)
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @else
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Inactive
                            </span>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600">Parent Structure</p>
                        <p class="text-base text-gray-900">
                            @if($academicStructure->parent)
                                <a href="{{ route('settings.academic-structure.show', $academicStructure->parent) }}" class="text-blue-600 hover:text-blue-900">
                                    {{ $academicStructure->parent->name }} ({{ ucfirst($academicStructure->parent->type) }})
                                </a>
                            @else
                                <span class="text-gray-500">None (Top Level)</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-600">Created At</p>
                        <p class="text-base text-gray-900">{{ $academicStructure->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div class="col-span-2">
                        <p class="text-sm font-medium text-gray-600">Description</p>
                        <p class="text-base text-gray-900">{{ $academicStructure->description ?? 'No description provided.' }}</p>
                    </div>

                    @if($academicStructure->children->count() > 0)
                        <div class="col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Child Structures</h3>

                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($academicStructure->children as $child)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $child->name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $child->code }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ ucfirst($child->type) }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if($child->is_active)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Active
                                                        </span>
                                                    @else
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            Inactive
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('settings.academic-structure.show', $child) }}" class="text-blue-600 hover:text-blue-900">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('settings.academic-structure.edit', $child) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection 