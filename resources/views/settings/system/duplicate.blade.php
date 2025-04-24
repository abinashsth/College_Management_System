@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Duplicate System Setting</h1>
            <a href="{{ route('settings.system.index') }}" class="text-blue-600 hover:text-blue-800">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>
        
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p class="font-bold">Validation Error</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div class="bg-white shadow overflow-hidden rounded-lg">
            <form action="{{ route('settings.system.store') }}" method="POST" class="p-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-1">
                        <label for="key" class="block text-sm font-medium text-gray-700 mb-1">Setting Key <span class="text-red-600">*</span></label>
                        <input type="text" name="key" id="key" value="{{ old('key', $setting->key . '_copy') }}" required 
                               class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               placeholder="e.g., system.maintenance_mode">
                        <p class="mt-1 text-sm text-gray-500">Unique identifier for this setting</p>
                    </div>
                    
                    <div class="col-span-1">
                        <label for="group" class="block text-sm font-medium text-gray-700 mb-1">Group <span class="text-red-600">*</span></label>
                        <select name="group" id="group" required class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            <option value="">Select Group</option>
                            @foreach($groups as $group)
                                <option value="{{ $group }}" {{ old('group', $setting->group) == $group ? 'selected' : '' }}>{{ $group }}</option>
                            @endforeach
                            <option value="new">Add New Group</option>
                        </select>
                    </div>
                    
                    <div class="col-span-2" id="newGroupDiv" style="display: none;">
                        <label for="new_group" class="block text-sm font-medium text-gray-700 mb-1">New Group Name <span class="text-red-600">*</span></label>
                        <input type="text" name="new_group" id="new_group" value="{{ old('new_group') }}" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    
                    <div class="col-span-2">
                        <label for="value" class="block text-sm font-medium text-gray-700 mb-1">Value <span class="text-red-600">*</span></label>
                        <textarea name="value" id="value" rows="3" required
                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                  placeholder="Setting value">{{ old('value', $setting->value) }}</textarea>
                    </div>
                    
                    <div class="col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                  placeholder="Describe what this setting is used for">{{ old('description', $setting->description . ' (Copy)') }}</textarea>
                    </div>
                    
                    <div class="col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_public" name="is_public" type="checkbox" value="1" {{ old('is_public', $setting->is_public) ? 'checked' : '' }}
                                       class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_public" class="font-medium text-gray-700">Public Setting</label>
                                <p class="text-gray-500">Enable this if the setting should be accessible to the public API</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('settings.system.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Create Duplicate
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const groupSelect = document.getElementById('group');
            const newGroupDiv = document.getElementById('newGroupDiv');
            
            function toggleNewGroupInput() {
                if (groupSelect.value === 'new') {
                    newGroupDiv.style.display = 'block';
                    document.getElementById('new_group').setAttribute('required', 'required');
                } else {
                    newGroupDiv.style.display = 'none';
                    document.getElementById('new_group').removeAttribute('required');
                }
            }
            
            // Initial check
            toggleNewGroupInput();
            
            // Event listener for changes
            groupSelect.addEventListener('change', toggleNewGroupInput);
        });
    </script>
@endsection 