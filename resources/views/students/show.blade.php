@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Student Profile</h1>
        <div class="space-x-2">
            <a href="{{ route('students.edit', $student->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition">
                Edit Profile
            </a>
            <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition">
                Back to List
            </a>
        </div>
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Student Photo and Basic Info -->
            <div class="md:col-span-1 p-6 border-b md:border-b-0 md:border-r border-gray-200">
                <div class="flex flex-col items-center">
                    <div class="w-32 h-32 rounded-full overflow-hidden mb-4 border-2 border-blue-500">
                        @if($student->profile_photo)
                            <img src="{{ asset('storage/' . $student->profile_photo) }}" alt="{{ $student->first_name }} {{ $student->last_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $student->first_name }} {{ $student->last_name }}</h2>
                    <p class="text-gray-600 mb-4">{{ $student->student_id ?? 'ID: Not assigned' }}</p>
                    
                    <div class="w-full">
                        <div class="mb-6">
                            <h3 class="font-medium text-gray-700 border-b pb-2 mb-2">Status Information</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Registration No:</span>
                                    <span class="font-medium">{{ $student->registration_number ?? 'Not assigned' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Enrollment Status:</span>
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $student->enrollment_status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($student->enrollment_status ?? 'Not set') }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Batch Year:</span>
                                    <span class="font-medium">{{ $student->batch_year ?? 'Not set' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Current Semester:</span>
                                    <span class="font-medium">{{ $student->current_semester ?? 'Not set' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Fee Status:</span>
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $student->fee_status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $student->fee_status ? 'Paid' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Details -->
            <div class="md:col-span-2 p-6">
                <div class="mb-6">
                    <h3 class="font-medium text-gray-700 border-b pb-2 mb-4">Personal Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2"><span class="font-medium">Email:</span> {{ $student->email }}</p>
                            <p class="mb-2"><span class="font-medium">Phone Number:</span> {{ $student->phone_number ?? $student->contact_number ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Gender:</span> {{ ucfirst($student->gender ?? 'Not specified') }}</p>
                            <p class="mb-2"><span class="font-medium">Date of Birth:</span> {{ $student->dob ? date('d M Y', strtotime($student->dob)) : 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Father's Name:</span> {{ $student->father_name ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Mother's Name:</span> {{ $student->mother_name ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="mb-2"><span class="font-medium">Address:</span> {{ $student->address ?? $student->student_address ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">City:</span> {{ $student->city ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">State:</span> {{ $student->state ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Last Qualification:</span> {{ $student->last_qualification ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Last Qualification Marks:</span> {{ $student->last_qualification_marks ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="font-medium text-gray-700 border-b pb-2 mb-4">Academic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2"><span class="font-medium">Program:</span> {{ $student->program->name ?? 'Not assigned' }}</p>
                            <p class="mb-2"><span class="font-medium">Department:</span> {{ $student->department->name ?? 'Not assigned' }}</p>
                            <p class="mb-2"><span class="font-medium">Class:</span> {{ $student->class->class_name ?? 'Not assigned' }}</p>
                            <p class="mb-2"><span class="font-medium">Academic Session:</span> {{ $student->academicSession->name ?? 'Not assigned' }}</p>
                        </div>
                        <div>
                            <p class="mb-2"><span class="font-medium">Admission Date:</span> {{ $student->admission_date ? date('d M Y', strtotime($student->admission_date)) : 'Not specified' }}</p>
                            <p class="mb-2"><span class="font-medium">Years of Study:</span> {{ $student->years_of_study ?? 'Not specified' }}</p>
                            <p class="mb-2"><span class="font-medium">Previous Education:</span> {{ $student->previous_education ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h3 class="font-medium text-gray-700 border-b pb-2 mb-4">Emergency Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="mb-2"><span class="font-medium">Emergency Contact:</span> {{ $student->emergency_contact_name ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Contact Number:</span> {{ $student->emergency_contact_number ?? 'Not provided' }}</p>
                            <p class="mb-2"><span class="font-medium">Relationship:</span> {{ $student->emergency_contact_relationship ?? 'Not specified' }}</p>
                        </div>
                      
                    </div>
                </div>

                @if($student->medical_information || $student->remarks)
                <div class="mb-6">
                    <h3 class="font-medium text-gray-700 border-b pb-2 mb-4">Additional Information</h3>
                    @if($student->medical_information)
                    <div class="mb-4">
                        <h4 class="font-medium text-gray-600 mb-1">Medical Information</h4>
                        <div class="bg-gray-50 p-3 rounded">
                            {{ $student->medical_information }}
                        </div>
                    </div>
                    @endif
                    
                    @if($student->remarks)
                    <div>
                        <h4 class="font-medium text-gray-600 mb-1">Remarks</h4>
                        <div class="bg-gray-50 p-3 rounded">
                            {{ $student->remarks }}
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($student->exams && $student->exams->count() > 0)
    <div class="mt-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Examination Records</h2>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($student->exams as $exam)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->name ?? $exam->exam_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->date ? date('d M Y', strtotime($exam->date)) : 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->pivot->score ?? $exam->score ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $exam->pivot->grade ?? $exam->grade ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded {{ ($exam->pivot->passed ?? $exam->passed) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ($exam->pivot->passed ?? $exam->passed) ? 'Passed' : 'Failed' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection 