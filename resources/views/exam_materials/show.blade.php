@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold">{{ $material->title }}</h1>
            <p class="text-gray-600 mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $material->getTypeColorClass() }}">
                    {{ $material->getTypeText() }}
                </span>
                <span class="ml-2">{{ $material->created_at->format('M d, Y') }}</span>
            </p>
        </div>
        <div class="mt-4 md:mt-0 space-x-2 flex flex-wrap">
            @can('update', $material)
            <a href="{{ route('exam-materials.edit', ['exam_material' => $material]) }}" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
            @endcan
            @can('delete', $material)
            <form action="{{ route('exam-materials.destroy', ['exam_material' => $material]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this material? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600 transition-colors">
                    <i class="fas fa-trash-alt mr-1"></i> Delete
                </button>
            </form>
            @endcan
            <a href="{{ route('exam-materials.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-2">Material Details</h2>
                        <div class="border-t border-gray-200 pt-4">
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Related Exam</dt>
                                    <dd class="mt-1 text-gray-900">
                                        <a href="{{ route('exams.show', ['exam' => $material->exam]) }}" class="text-blue-600 hover:underline">
                                            {{ $material->exam->title }}
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Subject</dt>
                                    <dd class="mt-1 text-gray-900">{{ $material->exam->subject->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Class</dt>
                                    <dd class="mt-1 text-gray-900">{{ $material->exam->class->name ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                    <dd class="mt-1 text-gray-900">{{ $material->creator->name ?? 'Unknown' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Release Date</dt>
                                    <dd class="mt-1 text-gray-900">
                                        @if($material->release_date)
                                            {{ $material->release_date->format('M d, Y') }}
                                            @if($material->release_date->isFuture())
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Scheduled
                                                </span>
                                            @endif
                                        @else
                                            Immediately Available
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1 text-gray-900">
                                        @if($material->is_active)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-gray-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Inactive
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($material->description)
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold mb-2">Description</h2>
                        <div class="bg-gray-50 rounded-lg p-4 prose max-w-none">
                            {{ $material->description }}
                        </div>
                    </div>
                    @endif

                    @if($material->file_path)
                    <div>
                        <h2 class="text-xl font-semibold mb-2">Download Material</h2>
                        <div class="bg-blue-50 rounded-lg p-4 flex items-center justify-between">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas {{ $material->getFileIconClass() }} text-2xl text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-lg font-medium text-blue-800">{{ basename($material->file_path) }}</h3>
                                    <p class="text-blue-600">{{ $material->formatted_file_size }} · {{ $material->getFileExtension() }}</p>
                                </div>
                            </div>
                            <a href="{{ route('exam-materials.download', ['exam_material' => $material]) }}" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition-colors">
                                <i class="fas fa-download mr-1"></i> Download
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Visibility Settings</h2>
                    <ul class="divide-y divide-gray-200">
                        <li class="py-3 flex justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-user-graduate text-gray-500 mr-3"></i>
                                <span>Available for Students</span>
                            </div>
                            <span>
                                @if($material->is_for_students)
                                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                @endif
                            </span>
                        </li>
                        <li class="py-3 flex justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-chalkboard-teacher text-gray-500 mr-3"></i>
                                <span>Available for Teachers</span>
                            </div>
                            <span>
                                @if($material->is_for_teachers)
                                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                @endif
                            </span>
                        </li>
                        <li class="py-3 flex justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-user-shield text-gray-500 mr-3"></i>
                                <span>Confidential</span>
                            </div>
                            <span>
                                @if($material->is_confidential)
                                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Activity</h2>
                    <div class="space-y-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fas fa-plus"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Created</p>
                                <p class="text-sm text-gray-500">{{ $material->created_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                        @if($material->created_at->timestamp !== $material->updated_at->timestamp)
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-600">
                                    <i class="fas fa-edit"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                <p class="text-sm text-gray-500">{{ $material->updated_at->format('M d, Y \a\t h:i A') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($material->downloads_count > 0)
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-download"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-900">Downloads</p>
                                <p class="text-sm text-gray-500">{{ $material->downloads_count }} total downloads</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection