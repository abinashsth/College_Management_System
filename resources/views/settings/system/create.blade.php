@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Add System Setting</h1>
            <a href="{{ route('settings.system.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                        <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form action="{{ route('settings.system.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                        <!-- Setting Key -->
                        <div class="sm:col-span-3">
                            <label for="key" class="block text-sm font-medium text-gray-700">
                                Setting Key <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="text" name="key" id="key" value="{{ old('key') }}" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="e.g. app.name, mail.driver">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Use dot notation for hierarchical settings</p>
                        </div>

                        <!-- Group -->
                        <div class="sm:col-span-3">
                            <label for="group" class="block text-sm font-medium text-gray-700">
                                Group
                            </label>
                            <div class="mt-1">
                                <select id="group" name="group" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                                    @foreach($groups as $group)
                                        <option value="{{ $group }}" {{ old('group') == $group ? 'selected' : '' }}>{{ $group }}</option>
                                    @endforeach
                                    <option value="new_group" {{ old('group') == 'new_group' ? 'selected' : '' }}>+ Create New Group</option>
                                </select>
                            </div>
                            <div id="new_group_container" class="mt-2 {{ old('group') == 'new_group' ? '' : 'hidden' }}">
                                <input type="text" name="new_group" id="new_group" value="{{ old('new_group') }}"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                    placeholder="Enter new group name">
                            </div>
                        </div>

                        <!-- Value -->
                        <div class="sm:col-span-6">
                            <label for="value" class="block text-sm font-medium text-gray-700">
                                Value <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <textarea id="value" name="value" rows="3" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('value') }}</textarea>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-6">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="2"
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description') }}</textarea>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Optional: Add a description to explain the purpose of this setting</p>
                        </div>

                        <!-- Public Setting -->
                        <div class="sm:col-span-6">
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="is_public" name="is_public" type="checkbox" value="1" {{ old('is_public') ? 'checked' : '' }}
                                        class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="is_public" class="font-medium text-gray-700">Public Setting</label>
                                    <p class="text-gray-500">Make this setting accessible to the public API and frontend</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <a href="{{ route('settings.system.index') }}" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Save Setting
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const groupSelect = document.getElementById('group');
            const newGroupContainer = document.getElementById('new_group_container');
            const newGroupInput = document.getElementById('new_group');
            
            groupSelect.addEventListener('change', function() {
                if (this.value === 'new_group') {
                    newGroupContainer.classList.remove('hidden');
                    newGroupInput.setAttribute('required', 'required');
                } else {
                    newGroupContainer.classList.add('hidden');
                    newGroupInput.removeAttribute('required');
                }
            });
        });
    </script>
@endsection 