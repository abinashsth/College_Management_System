@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Student</h1>
        <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
            Back to Students
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-6">
                <div class="border-b border-gray-200 pb-3 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Personal Information</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $student->first_name) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('first_name') border-red-500 @enderror" required>
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $student->last_name) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_name') border-red-500 @enderror" required>
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $student->email) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror" required>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number" value="{{ old('phone_number', $student->phone_number ?? $student->contact_number) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone_number') border-red-500 @enderror" required>
                        @error('phone_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                        <select id="gender" name="gender" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('gender') border-red-500 @enderror" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $student->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="{{ old('dob', $student->dob ? date('Y-m-d', strtotime($student->dob)) : '') }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('dob') border-red-500 @enderror" required>
                        @error('dob')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="father_name" class="block text-sm font-medium text-gray-700 mb-1">Father's Name</label>
                        <input type="text" id="father_name" name="father_name" value="{{ old('father_name', $student->father_name) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('father_name') border-red-500 @enderror">
                        @error('father_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="mother_name" class="block text-sm font-medium text-gray-700 mb-1">Mother's Name</label>
                        <input type="text" id="mother_name" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('mother_name') border-red-500 @enderror">
                        @error('mother_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="student_address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <input type="text" id="student_address" name="student_address" value="{{ old('student_address', $student->student_address ?? $student->address) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('student_address') border-red-500 @enderror" required>
                        @error('student_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $student->city) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('city') border-red-500 @enderror" required>
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">State</label>
                        <input type="text" id="state" name="state" value="{{ old('state', $student->state) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('state') border-red-500 @enderror" required>
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-3 pt-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Academic Information</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="registration_number" class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                        <input type="text" id="registration_number" value="{{ $student->registration_number }}" readonly
                               class="w-full px-4 py-2.5 bg-gray-100 border border-gray-300 rounded-lg text-gray-700">
                        <p class="text-xs text-gray-500 mt-1">Registration numbers are automatically generated and cannot be changed</p>
                    </div>
                    
                    <div>
                        <label for="current_semester" class="block text-sm font-medium text-gray-700 mb-1">Current Semester</label>
                        <select id="current_semester" name="current_semester" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('current_semester') border-red-500 @enderror">
                            <option value="">Select Semester</option>
                            <option value="1st" {{ old('current_semester', $student->current_semester) == '1st' ? 'selected' : '' }}>1st Semester</option>
                            <option value="2nd" {{ old('current_semester', $student->current_semester) == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                            <option value="3rd" {{ old('current_semester', $student->current_semester) == '3rd' ? 'selected' : '' }}>3rd Semester</option>
                            <option value="4th" {{ old('current_semester', $student->current_semester) == '4th' ? 'selected' : '' }}>4th Semester</option>
                            <option value="5th" {{ old('current_semester', $student->current_semester) == '5th' ? 'selected' : '' }}>5th Semester</option>
                            <option value="6th" {{ old('current_semester', $student->current_semester) == '6th' ? 'selected' : '' }}>6th Semester</option>
                            <option value="7th" {{ old('current_semester', $student->current_semester) == '7th' ? 'selected' : '' }}>7th Semester</option>
                            <option value="8th" {{ old('current_semester', $student->current_semester) == '8th' ? 'selected' : '' }}>8th Semester</option>
                        </select>
                        @error('current_semester')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="program_id" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                        <select id="program_id" name="program_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('program_id') border-red-500 @enderror" required>
                            <option value="">Select Program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id', $student->program_id) == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('program_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="department_id" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select id="department_id" name="department_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('department_id') border-red-500 @enderror" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id', $student->department_id) == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                        <select id="class_id" name="class_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('class_id') border-red-500 @enderror" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                    {{ $class->class_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="years_of_study" class="block text-sm font-medium text-gray-700 mb-1">Years of Study</label>
                        <select id="years_of_study" name="years_of_study" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('years_of_study') border-red-500 @enderror">
                            <option value="">Select Years of Study</option>
                            <option value="2" {{ old('years_of_study', $student->years_of_study) == '2' ? 'selected' : '' }}>2 Years</option>
                            <option value="3" {{ old('years_of_study', $student->years_of_study) == '3' ? 'selected' : '' }}>3 Years</option>
                            <option value="4" {{ old('years_of_study', $student->years_of_study) == '4' ? 'selected' : '' }}>4 Years</option>
                            <option value="5" {{ old('years_of_study', $student->years_of_study) == '5' ? 'selected' : '' }}>5 Years</option>
                        </select>
                        @error('years_of_study')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="batch_year" class="block text-sm font-medium text-gray-700 mb-1">Batch Year</label>
                        <select id="batch_year" name="batch_year" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('batch_year') border-red-500 @enderror" required>
                            <option value="">Select Batch Year</option>
                            @foreach($batchYears as $year)
                                <option value="{{ $year }}" {{ old('batch_year', $student->batch_year) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                        @error('batch_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="academic_session_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Session</label>
                        <select id="academic_session_id" name="academic_session_id" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('academic_session_id') border-red-500 @enderror">
                            <option value="">Select Academic Session</option>
                            @foreach($academicSessions as $session)
                                <option value="{{ $session->id }}" {{ old('academic_session_id', $student->academic_session_id) == $session->id ? 'selected' : '' }}>
                                    {{ $session->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_session_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                
                    <div>
                        <label for="enrollment_status" class="block text-sm font-medium text-gray-700 mb-1">Enrollment Status</label>
                        <select id="enrollment_status" name="enrollment_status" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('enrollment_status') border-red-500 @enderror" required>
                            <option value="applied" {{ old('enrollment_status', $student->enrollment_status) == 'applied' ? 'selected' : '' }}>Applied</option>
                            <option value="admitted" {{ old('enrollment_status', $student->enrollment_status) == 'admitted' ? 'selected' : '' }}>Admitted</option>
                            <option value="active" {{ old('enrollment_status', $student->enrollment_status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('enrollment_status', $student->enrollment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ old('enrollment_status', $student->enrollment_status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="withdrawn" {{ old('enrollment_status', $student->enrollment_status) == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                        @error('enrollment_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="last_qualification" class="block text-sm font-medium text-gray-700 mb-1">Last Qualification</label>
                        <input type="text" id="last_qualification" name="last_qualification" value="{{ old('last_qualification', $student->last_qualification) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_qualification') border-red-500 @enderror">
                        @error('last_qualification')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="last_qualification_marks" class="block text-sm font-medium text-gray-700 mb-1">Last Qualification Marks</label>
                        <input type="text" id="last_qualification_marks" name="last_qualification_marks" value="{{ old('last_qualification_marks', $student->last_qualification_marks) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('last_qualification_marks') border-red-500 @enderror">
                        @error('last_qualification_marks')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-3 pt-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Emergency Contact Information</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('emergency_contact_name') border-red-500 @enderror">
                        @error('emergency_contact_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Number</label>
                        <input type="text" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number', $student->emergency_contact_number) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('emergency_contact_number') border-red-500 @enderror">
                        @error('emergency_contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="emergency_contact_relationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship', $student->emergency_contact_relationship) }}" 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('emergency_contact_relationship') border-red-500 @enderror">
                        @error('emergency_contact_relationship')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="border-b border-gray-200 pb-3 pt-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Additional Information</h2>
                </div>
                
                <div class="mb-6">
                    <label for="medical_information" class="block text-sm font-medium text-gray-700 mb-1">Medical Information</label>
                    <textarea id="medical_information" name="medical_information" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('medical_information') border-red-500 @enderror">{{ old('medical_information', $student->medical_information) }}</textarea>
                    @error('medical_information')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="3" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('remarks') border-red-500 @enderror">{{ old('remarks', $student->remarks) }}</textarea>
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="border-b border-gray-200 pb-3 pt-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Profile Photo</h2>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            @if($student->profile_photo)
                                <img class="h-16 w-16 object-cover rounded-full" src="{{ asset('storage/' . $student->profile_photo) }}" alt="Current profile photo">
                            @else
                                <div class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                    {{ substr($student->first_name, 0, 1) }}{{ substr($student->last_name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Change Profile Photo</label>
                            <input type="file" id="profile_photo" name="profile_photo" 
                                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('profile_photo') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Leave empty to keep current photo. JPG, PNG or GIF. Max 2MB.</p>
                            @error('profile_photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center justify-end space-x-4 px-6 py-4 bg-gray-50 border-t border-gray-200">
                <a href="{{ route('students.show', $student->id) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    Update Student
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
