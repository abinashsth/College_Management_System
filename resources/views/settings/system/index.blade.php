@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">System Settings</h1>
            <a href="{{ route('settings.system.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i> Add New Setting
            </a>
        </div>
        
        <!-- Filters -->
        <div class="bg-white shadow rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Filter Settings</h3>
                <form action="{{ route('settings.system.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" 
                               class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                               placeholder="Search by key or value">
                    </div>
                    
                    <div>
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Group</label>
                        <select name="group" id="group" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Groups</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" {{ request('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="is_public" class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                        <select name="is_public" id="is_public" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Settings</option>
                            <option value="1" {{ request('is_public') === '1' ? 'selected' : '' }}>Public Only</option>
                            <option value="0" {{ request('is_public') === '0' ? 'selected' : '' }}>Private Only</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end space-x-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-filter mr-2"></i> Apply Filters
                        </button>
                        
                        <a href="{{ route('settings.system.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-undo mr-2"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Settings Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            @if(count($settings) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Key</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Group</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Public</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($settings as $setting)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $setting->key }}
                                        @if($setting->description)
                                            <button class="text-gray-400 hover:text-gray-500 ml-2 tooltip-trigger" data-tooltip="{{ $setting->description }}">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $setting->group }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                        {{ Str::limit($setting->value, 50) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($setting->is_public)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i> Public
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                <i class="fas fa-lock mr-1"></i> Private
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('settings.system.edit', $setting->id) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('settings.system.duplicate', $setting->id) }}" class="text-green-600 hover:text-green-900" title="Duplicate">
                                                <i class="fas fa-copy"></i>
                                            </a>
                                            <form action="{{ route('settings.system.destroy', $setting->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this setting?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 sm:px-6">
                    {{ $settings->withQueryString()->links() }}
                </div>
            @else
                <div class="px-4 py-5 sm:p-6 text-center">
                    <div class="text-sm text-gray-500 mb-4">No settings found</div>
                    <a href="{{ route('settings.system.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i> Add Your First Setting
                    </a>
                </div>
            @endif
        </div>
    </div>
    
    <div id="tooltip" class="hidden absolute z-10 p-2 bg-gray-900 text-white text-xs rounded shadow-lg max-w-xs"></div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggers = document.querySelectorAll('.tooltip-trigger');
            const tooltip = document.getElementById('tooltip');
            
            tooltipTriggers.forEach(trigger => {
                trigger.addEventListener('mouseenter', e => {
                    const tooltipText = e.currentTarget.getAttribute('data-tooltip');
                    tooltip.textContent = tooltipText;
                    tooltip.classList.remove('hidden');
                    
                    // Position the tooltip
                    const rect = e.currentTarget.getBoundingClientRect();
                    tooltip.style.left = rect.left + window.scrollX + 'px';
                    tooltip.style.top = rect.bottom + window.scrollY + 5 + 'px';
                });
                
                trigger.addEventListener('mouseleave', () => {
                    tooltip.classList.add('hidden');
                });
            });
        });
    </script>
@endsection 