@extends('layouts.app')

@section('content')
    <div class="container mx-auto py-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">
                {{ __('College Settings') }}
            </h2>
            <a href="{{ route('settings.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Settings
            </a>
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

                <!-- Import/Export Buttons -->
                <div class="mb-6 flex justify-end gap-2">
                    <a href="{{ route('settings.college.export') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Export Settings') }}
                    </a>

                    <button type="button" onclick="document.getElementById('import-modal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-700 focus:ring ring-gray-300 disabled:opacity-25 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        {{ __('Import Settings') }}
                    </button>
                </div>

                <form method="POST" action="{{ route('settings.college.update') }}" enclosure="multipart/form-data" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- College Basic Information -->
                        <div class="col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Basic Information</h3>
                        </div>

                        <!-- College Name -->
                        <div>
                            <x-input-label for="college_name" :value="__('College Name')" />
                            <input id="college_name" name="college_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('college_name', $settings->college_name ?? '') }}" required />
                            <x-input-error :messages="$errors->get('college_name')" class="mt-2" />
                        </div>

                        <!-- College Code -->
                        <div>
                            <x-input-label for="college_code" :value="__('College Code')" />
                            <input id="college_code" name="college_code" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('college_code', $settings->college_code ?? '') }}" required />
                            <x-input-error :messages="$errors->get('college_code')" class="mt-2" />
                        </div>

                        <!-- Address -->
                        <div class="col-span-2">
                            <x-input-label for="address" :value="__('Address')" />
                            <input id="address" name="address" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('address', $settings->address ?? '') }}" required />
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </div>

                        <!-- City -->
                        <div>
                            <x-input-label for="city" :value="__('City')" />
                            <input id="city" name="city" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('city', $settings->city ?? '') }}" required />
                            <x-input-error :messages="$errors->get('city')" class="mt-2" />
                        </div>

                        <!-- State -->
                        <div>
                            <x-input-label for="state" :value="__('State')" />
                            <input id="state" name="state" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" value="{{ old('state', $settings->state ?? '') }}" required />
                            <x-input-error :messages="$errors->get('state')" class="mt-2" />
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <x-input-label for="postal_code" :value="__('Postal Code')" />
                            <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" 
                                :value="old('postal_code', $settings->postal_code)" />
                            <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                        </div>

                        <!-- Country -->
                        <div>
                            <x-input-label for="country" :value="__('Country')" />
                            <x-text-input id="country" name="country" type="text" class="mt-1 block w-full" 
                                :value="old('country', $settings->country)" required />
                            <x-input-error :messages="$errors->get('country')" class="mt-2" />
                        </div>

                        <!-- Phone -->
                        <div>
                            <x-input-label for="phone" :value="__('Phone')" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" 
                                :value="old('phone', $settings->phone)" required />
                            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" 
                                :value="old('email', $settings->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Website -->
                        <div>
                            <x-input-label for="website" :value="__('Website')" />
                            <x-text-input id="website" name="website" type="url" class="mt-1 block w-full" 
                                :value="old('website', $settings->website)" />
                            <x-input-error :messages="$errors->get('website')" class="mt-2" />
                        </div>

                        <!-- Logo -->
                        <div class="col-span-2">
                            <x-input-label for="logo" :value="__('College Logo')" />
                            
                            @if($settings->logo)
                                <div class="mt-2 mb-4">
                                    <div class="text-sm text-gray-600 mb-2">Current Logo:</div>
                                    <div class="flex items-center gap-4">
                                        <img src="{{ $settings->getLogoUrl() }}" alt="College Logo" class="max-h-20">
                                        <form method="POST" action="{{ route('settings.college.reset-logo') }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Are you sure you want to reset the logo?')" 
                                                class="text-sm text-red-600 hover:text-red-900">
                                                {{ __('Reset Logo') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" id="logo" name="logo" class="mt-1 block w-full text-sm text-gray-500 
                                file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold
                                file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                            <div class="mt-1 text-sm text-gray-500">Recommended size: 200x200px. Max 2MB. Formats: JPG, PNG, GIF.</div>
                            <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                        </div>

                        <!-- Academic Information -->
                        <div class="col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Academic Information</h3>
                        </div>

                        <!-- Established Year -->
                        <div>
                            <x-input-label for="established_year" :value="__('Established Year')" />
                            <x-text-input id="established_year" name="established_year" type="number" min="1800" max="{{ date('Y') }}" 
                                class="mt-1 block w-full" :value="old('established_year', $settings->established_year)" />
                            <x-input-error :messages="$errors->get('established_year')" class="mt-2" />
                        </div>

                        <!-- Principal Name -->
                        <div>
                            <x-input-label for="principal_name" :value="__('Principal Name')" />
                            <x-text-input id="principal_name" name="principal_name" type="text" class="mt-1 block w-full" 
                                :value="old('principal_name', $settings->principal_name)" />
                            <x-input-error :messages="$errors->get('principal_name')" class="mt-2" />
                        </div>

                        <!-- Accreditation Info -->
                        <div class="col-span-2">
                            <x-input-label for="accreditation_info" :value="__('Accreditation Information')" />
                            <textarea id="accreditation_info" name="accreditation_info" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('accreditation_info', $settings->accreditation_info) }}</textarea>
                            <x-input-error :messages="$errors->get('accreditation_info')" class="mt-2" />
                        </div>

                        <!-- Academic Year Start -->
                        <div>
                            <x-input-label for="academic_year_start" :value="__('Academic Year Start')" />
                            <x-text-input id="academic_year_start" name="academic_year_start" type="date" 
                                class="mt-1 block w-full" :value="old('academic_year_start', $settings->academic_year_start)" />
                            <x-input-error :messages="$errors->get('academic_year_start')" class="mt-2" />
                        </div>

                        <!-- Academic Year End -->
                        <div>
                            <x-input-label for="academic_year_end" :value="__('Academic Year End')" />
                            <x-text-input id="academic_year_end" name="academic_year_end" type="date" 
                                class="mt-1 block w-full" :value="old('academic_year_end', $settings->academic_year_end)" />
                            <x-input-error :messages="$errors->get('academic_year_end')" class="mt-2" />
                        </div>

                        <!-- Grading System -->
                        <div class="col-span-2">
                            <x-input-label for="grading_system" :value="__('Grading System')" />
                            <x-text-input id="grading_system" name="grading_system" type="text" class="mt-1 block w-full" 
                                :value="old('grading_system', $settings->grading_system)" />
                            <x-input-error :messages="$errors->get('grading_system')" class="mt-2" />
                        </div>

                        <!-- Vision & Mission -->
                        <div class="col-span-2 mt-6">
                            <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Vision & Mission</h3>
                        </div>

                        <!-- Vision Statement -->
                        <div class="col-span-2">
                            <x-input-label for="vision_statement" :value="__('Vision Statement')" />
                            <textarea id="vision_statement" name="vision_statement" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('vision_statement', $settings->vision_statement) }}</textarea>
                            <x-input-error :messages="$errors->get('vision_statement')" class="mt-2" />
                        </div>

                        <!-- Mission Statement -->
                        <div class="col-span-2">
                            <x-input-label for="mission_statement" :value="__('Mission Statement')" />
                            <textarea id="mission_statement" name="mission_statement" rows="3" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('mission_statement', $settings->mission_statement) }}</textarea>
                            <x-input-error :messages="$errors->get('mission_statement')" class="mt-2" />
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="col-span-2 mt-6">
                            <div class="flex justify-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widths hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition">
                                    {{ __('Save Settings') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Settings Modal -->
    <div id="import-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <i class="fas fa-file-import text-green-600"></i>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Import Settings</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-3">
                        Upload a previously exported settings file (.json)
                    </p>
                    <form action="{{ route('settings.college.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mt-4">
                            <input type="file" name="settings_file" id="settings_file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                        </div>
                        <div class="items-center px-4 py-3">
                            <button
                                class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300"
                                type="submit">
                                Import
                            </button>
                        </div>
                    </form>
                    <div class="items-center px-4 py-3">
                        <button onclick="document.getElementById('import-modal').classList.add('hidden')"
                            class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 