@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="text-xl font-semibold text-gray-800">Application Details #{{ $application->id }}</h4>
            <div class="space-x-2">
                <a href="{{ route('admissions.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition">Back to List</a>
                <a href="{{ route('admissions.edit', $application->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm transition">Edit</a>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:space-x-6">
                <div class="md:w-2/3">
                    <div class="mb-8">
                        <h5 class="text-lg font-semibold text-blue-600 mb-4">Personal Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="mb-2"><span class="font-medium">First Name:</span> {{ $application->first_name }}</p>
                                <p class="mb-2"><span class="font-medium">Last Name:</span> {{ $application->last_name }}</p>
                                <p class="mb-2"><span class="font-medium">Father's Name:</span> {{ $application->father_name }}</p>
                                <p class="mb-2"><span class="font-medium">Mother's Name:</span> {{ $application->mother_name }}</p>
                                <p class="mb-2"><span class="font-medium">Date of Birth:</span> {{ $application->dob ? date('d M Y', strtotime($application->dob)) : 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Gender:</span> {{ ucfirst($application->gender) }}</p>
                            </div>
                            <div>
                                <p class="mb-2"><span class="font-medium">Email:</span> {{ $application->email }}</p>
                                <p class="mb-2"><span class="font-medium">Phone:</span> {{ $application->phone_number }}</p>
                                <p class="mb-2"><span class="font-medium">Address:</span> {{ $application->student_address }}</p>
                                <p class="mb-2"><span class="font-medium">City:</span> {{ $application->city }}</p>
                                <p class="mb-2"><span class="font-medium">State:</span> {{ $application->state }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h5 class="text-lg font-semibold text-blue-600 mb-4">Academic Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="mb-2"><span class="font-medium">Program:</span> {{ $application->program->name ?? 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Department:</span> {{ $application->department->name ?? 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Batch Year:</span> {{ $application->batch_year }}</p>
                            </div>
                            <div>
                                <p class="mb-2"><span class="font-medium">Last Qualification:</span> {{ $application->last_qualification ?? 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Last Qualification Marks:</span> {{ $application->last_qualification_marks ?? 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Previous Education:</span> {{ $application->previous_education ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-8">
                        <h5 class="text-lg font-semibold text-blue-600 mb-4">Application Status</h5>
                        <div>
                            <p class="mb-2">
                                <span class="font-medium">Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full text-white 
                                {{ $application->enrollment_status == 'applied' ? 'bg-yellow-500' :
                                   ($application->enrollment_status == 'admitted' ? 'bg-green-500' : 
                                   ($application->enrollment_status == 'rejected' ? 'bg-red-500' : 'bg-gray-500')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->enrollment_status)) }}
                                </span>
                            </p>
                            <p class="mb-2"><span class="font-medium">Applied On:</span> {{ $application->created_at->format('d M Y h:i A') }}</p>
                            @if($application->enrollment_status == 'admitted' || $application->enrollment_status == 'rejected')
                                <p class="mb-2"><span class="font-medium">Decision Date:</span> {{ $application->updated_at->format('d M Y h:i A') }}</p>
                            @endif
                            @if($application->documents_verified_at)
                                <p class="mb-2"><span class="font-medium">Documents Verified On:</span> {{ date('d M Y h:i A', strtotime($application->documents_verified_at)) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="md:w-1/3">
                    <div class="mb-8">
                        <h5 class="text-lg font-semibold text-blue-600 mb-4">Documents</h5>
                        
                        @if($application->profile_photo)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Passport Photo</label>
                                <div>
                                    <img src="{{ asset('storage/' . $application->profile_photo) }}" alt="Passport Photo" class="border border-gray-200 rounded max-h-36">
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $application->profile_photo) }}" class="inline-block px-3 py-1 text-sm border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition" target="_blank">View Full Size</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($application->id_proof)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ID Proof</label>
                                <div>
                                    <a href="{{ asset('storage/' . $application->id_proof) }}" class="inline-block px-3 py-1 text-sm border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition" target="_blank">View Document</a>
                                </div>
                            </div>
                        @endif

                        @if($application->qualification_certificate)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Qualification Certificate</label>
                                <div>
                                    <a href="{{ asset('storage/' . $application->qualification_certificate) }}" class="inline-block px-3 py-1 text-sm border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition" target="_blank">View Document</a>
                                </div>
                            </div>
                        @endif

                        @if(!$application->profile_photo && !$application->id_proof && !$application->qualification_certificate)
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                <p class="text-yellow-700">No documents uploaded yet.</p>
                            </div>
                        @endif
                    </div>

                    <div class="mb-4">
                        <h5 class="text-lg font-semibold text-blue-600 mb-4">Actions</h5>
                        <div class="space-y-2">
                            @if($application->enrollment_status == 'applied')
                                <a href="{{ route('admissions.verify-documents', $application->id) }}" class="block w-full text-center bg-cyan-500 hover:bg-cyan-600 text-white py-2 px-4 rounded transition">Verify Documents</a>
                                <form action="{{ route('admissions.admit', $application->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded transition mb-2">Approve Application</button>
                                </form>
                                <form action="{{ route('admissions.reject', $application->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded transition">Reject Application</button>
                                </form>
                            @elseif($application->enrollment_status == 'documents_pending')
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                                    <p class="text-blue-700">Waiting for document verification</p>
                                </div>
                            @elseif($application->enrollment_status == 'admitted')
                                <div class="bg-green-50 border-l-4 border-green-400 p-4">
                                    <p class="text-green-700">This application has been approved</p>
                                </div>
                            @elseif($application->enrollment_status == 'rejected')
                                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                                    <p class="text-red-700">This application has been rejected</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 