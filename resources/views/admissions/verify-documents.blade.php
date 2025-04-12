@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h4 class="text-xl font-semibold text-gray-800">Verify Documents - {{ $application->first_name }} {{ $application->last_name }}</h4>
            <div>
                <a href="{{ route('admissions.show', $application->id) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm transition">Back to Application</a>
            </div>
        </div>
        
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4" role="alert">
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4" role="alert">
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="md:col-span-2">
                    <div class="mb-6">
                        <h5 class="text-lg font-semibold text-blue-600 border-b border-gray-200 pb-2 mb-4">Applicant Information</h5>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="mb-2"><span class="font-medium">Name:</span> {{ $application->first_name }} {{ $application->last_name }}</p>
                                <p class="mb-2"><span class="font-medium">Email:</span> {{ $application->email }}</p>
                                <p class="mb-2"><span class="font-medium">Phone:</span> {{ $application->phone_number }}</p>
                            </div>
                            <div>
                                <p class="mb-2"><span class="font-medium">Program:</span> {{ $application->program->name ?? 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Department:</span> {{ $application->department->name ?? 'N/A' }}</p>
                                <p class="mb-2"><span class="font-medium">Batch Year:</span> {{ $application->batch_year }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <div class="mb-6">
                        <h5 class="text-lg font-semibold text-blue-600 border-b border-gray-200 pb-2 mb-4">Verification Status</h5>
                        <p>
                            <span class="font-medium">Status:</span>
                            @if($application->documents_verified_at)
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Verified on {{ date('d M Y', strtotime($application->documents_verified_at)) }}
                                </span>
                            @else
                                <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending Verification
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Existing Documents -->
                <div>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden mb-6">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h5 class="text-lg font-semibold text-gray-800">Submitted Documents</h5>
                        </div>
                        <div class="p-4">
                            @if(is_array($application->documents) && count($application->documents) > 0)
                                <div class="space-y-3">
                                    @foreach($application->documents as $document)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                            <div>
                                                <h6 class="font-medium text-gray-800">{{ $document['type'] ?? 'Document' }}</h6>
                                                <span class="text-sm text-gray-600">{{ $document['original_name'] ?? 'Uploaded file' }}</span>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    Uploaded: {{ isset($document['uploaded_at']) ? date('d M Y', strtotime($document['uploaded_at'])) : 'N/A' }}
                                                </p>
                                            </div>
                                            <div>
                                                <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($application->photo || $application->id_proof || $application->qualification_certificate || $application->profile_photo)
                                <div class="space-y-3">
                                    @if($application->photo || $application->profile_photo)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                            <div>
                                                <h6 class="font-medium text-gray-800">Passport Photo</h6>
                                            </div>
                                            <div>
                                                <a href="{{ asset('storage/' . ($application->photo ?? $application->profile_photo)) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    @if($application->id_proof)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                            <div>
                                                <h6 class="font-medium text-gray-800">ID Proof</h6>
                                            </div>
                                            <div>
                                                <a href="{{ asset('storage/' . $application->id_proof) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    @if($application->qualification_certificate)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                            <div>
                                                <h6 class="font-medium text-gray-800">Qualification Certificate</h6>
                                            </div>
                                            <div>
                                                <a href="{{ asset('storage/' . $application->qualification_certificate) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 border border-blue-500 text-blue-500 rounded hover:bg-blue-500 hover:text-white transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        <p class="text-yellow-700">No documents have been uploaded by the applicant.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Document Verification Form -->
                <div>
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                            <h5 class="text-lg font-semibold text-gray-800">Document Verification</h5>
                        </div>
                        <div class="p-4">
                            <form action="{{ route('admissions.process-document-verification', $application->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="verification_notes" class="block text-sm font-medium text-gray-700 mb-1">Verification Notes</label>
                                    <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                        id="verification_notes" name="verification_notes" rows="3" 
                                        placeholder="Add notes about document verification...">{{ old('verification_notes') }}</textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Additional Documents</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div class="col-span-2">
                                            <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" name="documents[]">
                                            <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 mb-3" 
                                                name="document_types[]">
                                                <option value="id_proof">ID Proof</option>
                                                <option value="qualification_certificate">Qualification Certificate</option>
                                                <option value="passport_photo">Passport Photo</option>
                                                <option value="other">Other Document</option>
                                            </select>
                                        </div>
                                        <div class="col-span-1">
                                            <button type="button" class="w-full h-10 bg-gray-100 hover:bg-gray-200 border border-gray-300 text-gray-700 py-2 px-3 rounded-md transition mb-3" id="addMoreDocuments">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                Add More
                                            </button>
                                        </div>
                                    </div>
                                    <div id="more-documents"></div>
                                </div>
                                
                                <div class="flex items-center mb-4">
                                    <input class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                                        type="checkbox" value="1" id="documents_verified" name="documents_verified">
                                    <label class="ml-2 block text-sm font-medium text-gray-700" for="documents_verified">
                                        Mark documents as verified
                                    </label>
                                </div>
                                
                                <div>
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition">
                                        Save Verification
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addMoreButton = document.getElementById('addMoreDocuments');
        const moreDocumentsContainer = document.getElementById('more-documents');
        
        addMoreButton.addEventListener('click', function() {
            const docRow = document.createElement('div');
            docRow.className = 'grid grid-cols-3 gap-3 mb-3';
            docRow.innerHTML = `
                <div class="col-span-2">
                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" name="documents[]">
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                        name="document_types[]">
                        <option value="id_proof">ID Proof</option>
                        <option value="qualification_certificate">Qualification Certificate</option>
                        <option value="passport_photo">Passport Photo</option>
                        <option value="other">Other Document</option>
                    </select>
                </div>
                <div class="col-span-1">
                    <button type="button" class="w-full h-10 bg-red-100 hover:bg-red-200 border border-red-300 text-red-700 py-2 px-3 rounded-md transition remove-doc">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Remove
                    </button>
                </div>
            `;
            
            moreDocumentsContainer.appendChild(docRow);
            
            // Add event listener to the remove button
            docRow.querySelector('.remove-doc').addEventListener('click', function() {
                docRow.remove();
            });
        });
    });
</script>
@endsection
@endsection 