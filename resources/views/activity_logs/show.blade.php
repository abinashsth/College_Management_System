@extends('layouts.app', ['title' => 'Activity Log Details'])

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('activity-logs.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Activity Log Details</h1>
        </div>
        <p class="text-gray-600 text-sm">Viewing detailed information for activity log #{{ $activityLog->id }}</p>
    </div>

    <!-- Activity Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="border-b px-6 py-4 bg-gray-50">
            <h2 class="font-semibold text-gray-700">Basic Information</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Date & Time</label>
                        <div class="text-gray-800">{{ $activityLog->created_at->format('F d, Y H:i:s') }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">User</label>
                        <div class="text-gray-800">{{ $activityLog->user->name ?? 'System' }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">IP Address</label>
                        <div class="text-gray-800">{{ $activityLog->ip_address ?? 'N/A' }}</div>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Module</label>
                        <div class="text-gray-800">{{ ucfirst($activityLog->module) }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Action</label>
                        <div>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                              {{ $activityLog->action == 'created' ? 'bg-green-100 text-green-800' : 
                                ($activityLog->action == 'updated' ? 'bg-blue-100 text-blue-800' : 
                                ($activityLog->action == 'deleted' ? 'bg-red-100 text-red-800' : 
                                ($activityLog->action == 'login' ? 'bg-purple-100 text-purple-800' : 
                                ($activityLog->action == 'logout' ? 'bg-yellow-100 text-yellow-800' : 
                                'bg-gray-100 text-gray-800')))) }}">
                                {{ ucfirst($activityLog->action) }}
                            </span>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-500 mb-1">Model Type & ID</label>
                        <div class="text-gray-800">
                            {{ class_basename($activityLog->loggable_type) }} #{{ $activityLog->loggable_id }}
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-2">
                <label class="block text-sm font-medium text-gray-500 mb-1">Description</label>
                <div class="text-gray-800 p-3 bg-gray-50 rounded">
                    {{ $activityLog->description ?? 'No description provided' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Changes Made -->
    @if($activityLog->action == 'updated')
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="border-b px-6 py-4 bg-gray-50">
            <h2 class="font-semibold text-gray-700">Changes Made</h2>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Old Value</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Value</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $oldValues = $activityLog->old_values;
                            $newValues = $activityLog->new_values;
                            $changedFields = array_keys(array_diff_assoc($newValues, $oldValues));
                        @endphp
                        
                        @forelse($changedFields as $field)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ ucfirst(str_replace('_', ' ', $field)) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ is_array($oldValues[$field] ?? null) ? json_encode($oldValues[$field]) : ($oldValues[$field] ?? 'null') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ is_array($newValues[$field] ?? null) ? json_encode($newValues[$field]) : ($newValues[$field] ?? 'null') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-gray-500">No changed fields detected</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Data Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Old Values -->
        @if($activityLog->old_values)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b px-6 py-4 bg-gray-50">
                <h2 class="font-semibold text-gray-700">Original Data</h2>
            </div>
            <div class="p-6">
                <pre class="language-json bg-gray-50 p-4 rounded text-sm overflow-auto max-h-96">{{ json_encode($activityLog->old_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif

        <!-- New Values -->
        @if($activityLog->new_values)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="border-b px-6 py-4 bg-gray-50">
                <h2 class="font-semibold text-gray-700">New Data</h2>
            </div>
            <div class="p-6">
                <pre class="language-json bg-gray-50 p-4 rounded text-sm overflow-auto max-h-96">{{ json_encode($activityLog->new_values, JSON_PRETTY_PRINT) }}</pre>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection 