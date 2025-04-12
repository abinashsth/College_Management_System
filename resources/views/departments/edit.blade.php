@extends('layouts.app')

@section('title', 'Edit Department')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Department</h1>
        <div class="flex space-x-3">
            <a href="{{ route('departments.show', $department) }}" class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-eye mr-2"></i> View Department
            </a>
            <a href="{{ route('departments.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Departments
            </a>
        </div>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('departments.update', $department) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Department Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                        Department Name <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $department->name) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department Code -->
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-1">
                        Department Code <span class="text-red-600">*</span>
                    </label>
                    <input type="text" name="code" id="code" value="{{ old('code', $department->code) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('code') border-red-300 @enderror">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">A unique code for the department (e.g., CS, EE, ME)</p>
                </div>

                <!-- Faculty -->
                <div>
                    <label for="faculty_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Faculty <span class="text-red-600">*</span>
                    </label>
                    <select name="faculty_id" id="faculty_id" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('faculty_id') border-red-300 @enderror">
                        <option value="">Select Faculty</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id', $department->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('faculty_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-1">
                        Slug
                    </label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $department->slug) }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('slug') border-red-300 @enderror">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave empty to auto-generate from name</p>
                </div>

                <!-- Department Logo -->
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">
                        Department Logo
                    </label>
                    <div class="flex items-center space-x-3">
                        @if($department->logo)
                            <div class="w-16 h-16 bg-gray-100 rounded">
                                <img src="{{ asset('storage/' . $department->logo) }}" alt="{{ $department->name }}" class="w-full h-full object-cover rounded">
                            </div>
                        @endif
                        <input type="file" name="logo" id="logo"
                            class="mt-1 block w-full border border-gray-300 px-3 py-2 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('logo') border-red-300 @enderror">
                    </div>
                    @error('logo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave empty to keep current logo</p>
                </div>

                <!-- Contact Email -->
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">
                        Contact Email
                    </label>
                    <input type="email" name="contact_email" id="contact_email" 
                        value="{{ old('contact_email', $department->metadata['contact_email'] ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('contact_email') border-red-300 @enderror">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Phone -->
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        Contact Phone
                    </label>
                    <input type="text" name="contact_phone" id="contact_phone" 
                        value="{{ old('contact_phone', $department->metadata['contact_phone'] ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('contact_phone') border-red-300 @enderror">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Website -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                        Website
                    </label>
                    <input type="url" name="website" id="website" 
                        value="{{ old('website', $department->metadata['website'] ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('website') border-red-300 @enderror">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        Address
                    </label>
                    <input type="text" name="address" id="address" 
                        value="{{ old('address', $department->metadata['address'] ?? '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('address') border-red-300 @enderror">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Established Date -->
                <div>
                    <label for="established_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Established Date
                    </label>
                    <input type="date" name="established_date" id="established_date" 
                        value="{{ old('established_date', isset($department->metadata['established_date']) ? date('Y-m-d', strtotime($department->metadata['established_date'])) : '') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('established_date') border-red-300 @enderror">
                    @error('established_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select name="status" id="status"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('status') border-red-300 @enderror">
                        <option value="1" {{ old('status', $department->is_active) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $department->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea name="description" id="description" rows="4"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('description') border-red-300 @enderror">{{ old('description', $department->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Update Department
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-generate slug from name if slug is empty
    document.getElementById('name').addEventListener('input', function () {
        const slugField = document.getElementById('slug');
        if (slugField.value === '') {
            const name = this.value;
            const slug = name.toLowerCase()
                .replace(/[\s\W-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slugField.value = slug;
        }
    });
</script>
@endsection 