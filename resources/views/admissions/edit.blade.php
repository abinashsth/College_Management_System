@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="text-2xl font-bold text-white">Edit Application #{{ $application->id }}</h4>
                        <p class="text-blue-100 mt-1">Update application information</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('admissions.show', $application->id) }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">View Details</a>
                        <a href="{{ route('admissions.index') }}" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">Back to List</a>
                    </div>
                </div>
            </div>

            <div class="p-6 sm:p-8">
                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('admissions.update', $application->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PUT')
                    
                    <!-- Personal Information Section -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('first_name') border-red-500 @enderror" 
                                    id="first_name" name="first_name" value="{{ old('first_name', $application->first_name) }}" required>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_name') border-red-500 @enderror" 
                                    id="last_name" name="last_name" value="{{ old('last_name', $application->last_name) }}" required>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Family Information Section -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-xl font-semibold text-gray-800">Family Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('father_name') border-red-500 @enderror" 
                                    id="father_name" name="father_name" value="{{ old('father_name', $application->father_name) }}" required>
                                @error('father_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('mother_name') border-red-500 @enderror" 
                                    id="mother_name" name="mother_name" value="{{ old('mother_name', $application->mother_name) }}" required>
                                @error('mother_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-xl font-semibold text-gray-800">Contact Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                <input type="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror" 
                                    id="email" name="email" value="{{ old('email', $application->email) }}" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone_number') border-red-500 @enderror" 
                                    id="phone_number" name="phone_number" value="{{ old('phone_number', $application->phone_number) }}" required>
                                @error('phone_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('gender') border-red-500 @enderror" 
                                    id="gender" name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $application->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $application->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $application->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('dob') border-red-500 @enderror" 
                                    id="dob" name="dob" value="{{ old('dob', $application->dob ? date('Y-m-d', strtotime($application->dob)) : '') }}" required>
                                @error('dob')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="student_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <textarea class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('student_address') border-red-500 @enderror" 
                                id="student_address" name="student_address" rows="3" required>{{ old('student_address', $application->student_address) }}</textarea>
                            @error('student_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('city') border-red-500 @enderror" 
                                    id="city" name="city" value="{{ old('city', $application->city) }}" required>
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('state') border-red-500 @enderror" 
                                    id="state" name="state" value="{{ old('state', $application->state) }}" required>
                                @error('state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information Section -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-xl font-semibold text-gray-800">Academic Information</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('program_id') border-red-500 @enderror" 
                                    id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('program_id', $application->program_id) == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('program_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department_id') border-red-500 @enderror" 
                                    id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $application->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="batch_year" class="block text-sm font-medium text-gray-700 mb-1">Batch Year</label>
                                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('batch_year') border-red-500 @enderror" 
                                    id="batch_year" name="batch_year" required>
                                    <option value="">Select Batch Year</option>
                                    @foreach($batchYears as $year)
                                        <option value="{{ $year }}" {{ old('batch_year', $application->batch_year) == $year ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('batch_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="last_qualification" class="block text-sm font-medium text-gray-700 mb-1">Last Qualification</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_qualification') border-red-500 @enderror" 
                                    id="last_qualification" name="last_qualification" value="{{ old('last_qualification', $application->last_qualification) }}" required>
                                @error('last_qualification')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="last_qualification_marks" class="block text-sm font-medium text-gray-700 mb-1">Last Qualification Marks/Percentage</label>
                                <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_qualification_marks') border-red-500 @enderror" 
                                    id="last_qualification_marks" name="last_qualification_marks" value="{{ old('last_qualification_marks', $application->last_qualification_marks) }}" required>
                                @error('last_qualification_marks')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="enrollment_status" class="block text-sm font-medium text-gray-700 mb-1">Application Status</label>
                                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('enrollment_status') border-red-500 @enderror" 
                                    id="enrollment_status" name="enrollment_status" required>
                                    <option value="applied" {{ old('enrollment_status', $application->enrollment_status) == 'applied' ? 'selected' : '' }}>Applied</option>
                                    <option value="admitted" {{ old('enrollment_status', $application->enrollment_status) == 'admitted' ? 'selected' : '' }}>Admitted</option>
                                    <option value="rejected" {{ old('enrollment_status', $application->enrollment_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    <option value="withdrawn" {{ old('enrollment_status', $application->enrollment_status) == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                                </select>
                                @error('enrollment_status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                            <textarea class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('remarks') border-red-500 @enderror" 
                                id="remarks" name="remarks" rows="3">{{ old('remarks', $application->remarks) }}</textarea>
                            @error('remarks')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Documents Section -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-3">
                            <h2 class="text-xl font-semibold text-gray-800">Documents</h2>
                            <p class="text-sm text-gray-500 mt-1">Update or keep existing documents</p>
                        </div>

                        <div>
                            <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Update Profile Photo</label>
                            @if($application->profile_photo)
                                <div class="mb-3 flex items-center space-x-3">
                                    <img src="{{ asset('storage/' . $application->profile_photo) }}" alt="Current Photo" class="h-20 w-20 object-cover rounded-md border border-gray-300">
                                    <span class="text-sm text-gray-600">Current photo</span>
                                </div>
                            @endif
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="profile_photo" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Upload a new photo</span>
                                            <input id="profile_photo" name="profile_photo" type="file" class="sr-only">
                                        </label>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                </div>
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Leave empty to keep the current photo</p>
                            @error('profile_photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('admissions.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            Update Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 