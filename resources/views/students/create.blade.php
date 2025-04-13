@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Student</h1>
        <a href="{{ route('students.index') }}" class="text-gray-600 hover:text-gray-800">
            Back to Students
        </a>
    </div>

    <div class="bg-white rounded shadow-md max-w-4xl mx-auto p-6">
        <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Basic Information Section -->
            <div class="border-b mb-6 pb-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="mb-4">
                        <label for="first_name" class="block text-gray-700 font-medium mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('first_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="last_name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('last_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Registration Number</label>
                        <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-500">
                            Will be automatically generated
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Registration numbers are generated based on department, batch year, and sequence</p>
                    </div>

                    <div class="mb-4">
                        <label for="gender" class="block text-gray-700 font-medium mb-2">Gender</label>
                        <select id="gender" name="gender" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="dob" class="block text-gray-700 font-medium mb-2">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="{{ old('dob') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('dob')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="profile_photo" class="block text-gray-700 font-medium mb-2">Profile Photo</label>
                        <input type="file" id="profile_photo" name="profile_photo" accept="image/*"
                               class="w-full border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none p-2">
                        @error('profile_photo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="border-b mb-6 pb-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Contact Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="phone_number" class="block text-gray-700 font-medium mb-2">Contact Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('phone_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="student_address" class="block text-gray-700 font-medium mb-2">Address</label>
                        <textarea id="student_address" name="student_address" rows="2"
                                  class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('student_address') }}</textarea>
                        @error('student_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Academic Information Section -->
            <div class="border-b mb-6 pb-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Academic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="mb-4">
                        <label for="program_id" class="block text-gray-700 font-medium mb-2">Program</label>
                        <select id="program_id" name="program_id" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">Select Program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="department_id" class="block text-gray-700 font-medium mb-2">Department</label>
                        <select id="department_id" name="department_id" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="class_id" class="block text-gray-700 font-medium mb-2">Class</label>
                        <select id="class_id" name="class_id" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }} - {{ $class->section }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="batch_year" class="block text-gray-700 font-medium mb-2">Batch Year</label>
                        <select id="batch_year" name="batch_year" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">Select Batch Year</option>
                            @foreach($batchYears as $year)
                                <option value="{{ $year }}" {{ old('batch_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                        @error('batch_year')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="years_of_study" class="block text-gray-700 font-medium mb-2">Years of Study</label>
                        <input type="number" id="years_of_study" name="years_of_study" value="{{ old('years_of_study') }}" min="1" max="10"
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('years_of_study')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="admission_date" class="block text-gray-700 font-medium mb-2">Enrollment Date</label>
                        <input type="date" id="admission_date" name="admission_date" value="{{ old('admission_date', date('Y-m-d')) }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('admission_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="academic_session_id" class="block text-gray-700 font-medium mb-2">Academic Session</label>
                        <select id="academic_session_id" name="academic_session_id"
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="">Select Academic Session</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->id }}" {{ old('academic_session_id') == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_session_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Parent/Guardian Information Section -->
            <div class="border-b mb-6 pb-4">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Parent/Guardian Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="guardian_name" class="block text-gray-700 font-medium mb-2">Parent/Guardian Name</label>
                        <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('guardian_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="guardian_relation" class="block text-gray-700 font-medium mb-2">Relation</label>
                        <input type="text" id="guardian_relation" name="guardian_relation" value="{{ old('guardian_relation') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('guardian_relation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="guardian_contact" class="block text-gray-700 font-medium mb-2">Contact Number</label>
                        <input type="text" id="guardian_contact" name="guardian_contact" value="{{ old('guardian_contact') }}" required
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('guardian_contact')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="guardian_address" class="block text-gray-700 font-medium mb-2">Address</label>
                        <textarea id="guardian_address" name="guardian_address" rows="2"
                                  class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">{{ old('guardian_address') }}</textarea>
                        @error('guardian_address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="guardian_occupation" class="block text-gray-700 font-medium mb-2">Occupation</label>
                        <input type="text" id="guardian_occupation" name="guardian_occupation" value="{{ old('guardian_occupation') }}"
                               class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                        @error('guardian_occupation')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Status Information Section -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Status Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Enrollment Status</label>
                        <select id="enrollment_status" name="enrollment_status" required
                                class="w-full px-4 py-2 border rounded-md focus:ring-2 focus:ring-teal-500 focus:outline-none">
                            <option value="active" {{ old('enrollment_status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="admitted" {{ old('enrollment_status') == 'admitted' ? 'selected' : '' }}>Admitted</option>
                            <option value="applied" {{ old('enrollment_status') == 'applied' ? 'selected' : '' }}>Applied</option>
                            <option value="inactive" {{ old('enrollment_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('enrollment_status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 font-medium mb-2">Fee Status</label>
                        <div class="flex items-center space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="fee_status" value="1" 
                                       {{ old('fee_status', '0') === '1' ? 'checked' : '' }}
                                       class="text-teal-600 focus:ring-teal-500">
                                <span class="ml-2">Paid</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="fee_status" value="0" 
                                       {{ old('fee_status', '0') === '0' ? 'checked' : '' }}
                                       class="text-teal-600 focus:ring-teal-500">
                                <span class="ml-2">Pending</span>
                            </label>
                        </div>
                        @error('fee_status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <a href="{{ route('students.index') }}" 
                   class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300">
                    Cancel
                </a>
                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-md hover:bg-teal-700">
                    Create Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
