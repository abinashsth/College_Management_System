@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Settings Dashboard</h1>
            <a href="{{ route('dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- College Profile Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <i class="fas fa-university text-blue-500"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-medium">College Profile</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Configure your institution details, branding, and contact information.
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('settings.college') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                            Manage Profile <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        @if($collegeSettings)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Configured</span>
                        @else
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Not Configured</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic Structure Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <i class="fas fa-sitemap text-purple-500"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-medium">Academic Structure</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Define faculties, departments, and programs within your institution.
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('settings.academic-structure.index') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                            Manage Structure <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $academicStructures ?? 0 }} Items</span>
                    </div>
                </div>
            </div>

            <!-- Academic Years Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full">
                            <i class="fas fa-calendar-alt text-green-500"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-medium">Academic Years</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Set up academic years, semesters/terms, and key academic dates.
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('settings.academic-year.index') }}" class="text-green-600 hover:text-green-800 font-medium">
                            Manage Years <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $academicYears ?? 0 }} Years</span>
                    </div>
                </div>
            </div>

            <!-- System Settings Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-yellow-100 p-3 rounded-full">
                            <i class="fas fa-cogs text-yellow-600"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-medium">System Settings</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Configure system-wide preferences, defaults, and technical settings.
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('settings.system.index') }}" class="text-yellow-600 hover:text-yellow-800 font-medium">
                            Manage Settings <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">{{ $systemSettings ?? 0 }} Items</span>
                    </div>
                </div>
            </div>

            <!-- Export Configuration Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-red-100 p-3 rounded-full">
                            <i class="fas fa-file-export text-red-500"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-medium">Export Configuration</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Export all system settings for backup or migration purposes.
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('settings.export') }}" class="text-red-600 hover:text-red-800 font-medium">
                            Export <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Import Configuration Card -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 p-3 rounded-full">
                            <i class="fas fa-file-import text-indigo-500"></i>
                        </div>
                        <h3 class="ml-4 text-lg font-medium">Import Configuration</h3>
                    </div>
                    <p class="text-gray-600 mb-4">
                        Import system settings from a previously exported configuration.
                    </p>
                    <div class="flex justify-between items-center">
                        <a href="{{ route('settings.import') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            Import <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($currentAcademicYear) && $currentAcademicYear)
            <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <h4 class="font-medium text-blue-800">Current Academic Year: {{ $currentAcademicYear->name }}</h4>
                <p class="text-sm text-blue-600 mt-1">{{ $currentAcademicYear->start_date->format('M d, Y') }} - {{ $currentAcademicYear->end_date->format('M d, Y') }}</p>
                
                @if(isset($currentSession) && $currentSession)
                    <div class="mt-2">
                        <h5 class="font-medium text-blue-800">Current Session: {{ $currentSession->name }}</h5>
                        <p class="text-sm text-blue-600">{{ $currentSession->start_date->format('M d, Y') }} - {{ $currentSession->end_date->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>
        @else
            <div class="mt-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                <h4 class="font-medium text-yellow-800">No active academic year set</h4>
                <p class="text-sm text-yellow-600 mt-1">Please set up an academic year and mark it as current.</p>
                <a href="{{ route('settings.academic-year.create') }}" class="mt-2 inline-block text-sm text-blue-600 hover:text-blue-800">
                    Create Academic Year <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        @endif
    </div>
@endsection 