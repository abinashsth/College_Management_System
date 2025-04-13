@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Import Configuration</h1>
            <a href="{{ route('settings.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
        </div>
        
        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif
        
        <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900">Import System Configuration</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Import configuration settings from a previously exported JSON file. You can choose which specific settings to import.
                </p>
            </div>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            <strong>Warning:</strong> Importing configuration will overwrite existing settings. Make sure to backup your current configuration before proceeding.
                        </p>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('settings.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-6">
                    <label for="import_file" class="block text-sm font-medium text-gray-700 mb-2">Configuration File</label>
                    <input type="file" name="import_file" id="import_file" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100
                        border border-gray-300 rounded-md"
                        accept=".json" required>
                    @error('import_file')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <span class="block text-sm font-medium text-gray-700 mb-2">Select Settings to Import</span>
                    <div class="mt-4 space-y-4">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="college_settings" name="import_options[]" value="college_settings" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="college_settings" class="font-medium text-gray-700">College Profile</label>
                                <p class="text-gray-500">Institution name, contact details, and branding settings</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="academic_structures" name="import_options[]" value="academic_structures" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="academic_structures" class="font-medium text-gray-700">Academic Structures</label>
                                <p class="text-gray-500">Faculties, departments, and programs</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="academic_years" name="import_options[]" value="academic_years" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="academic_years" class="font-medium text-gray-700">Academic Years & Sessions</label>
                                <p class="text-gray-500">Academic years, semesters/terms, and their date ranges</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="system_settings" name="import_options[]" value="system_settings" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="system_settings" class="font-medium text-gray-700">System Settings</label>
                                <p class="text-gray-500">General system configuration and preferences</p>
                            </div>
                        </div>
                    </div>
                    @error('import_options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        <i class="fas fa-file-import mr-2"></i> Import Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection 