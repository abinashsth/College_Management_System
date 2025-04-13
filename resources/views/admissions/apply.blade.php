@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Student Admission Application</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admissions.submit') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2">Personal Information</h5>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="dob" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number') }}" required>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="2" required>{{ old('address') }}</textarea>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="profile_photo" class="form-label">Profile Photo</label>
                        <input type="file" class="form-control" id="profile_photo" name="profile_photo">
                        <small class="text-muted">Max size: 2MB. Formats: JPG, PNG</small>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2">Academic Information</h5>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                        <select class="form-control" id="program_id" name="program_id" required>
                            <option value="">Select Program</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>{{ $program->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-control" id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="batch_year" class="form-label">Batch Year <span class="text-danger">*</span></label>
                        <select class="form-control" id="batch_year" name="batch_year" required>
                            <option value="">Select Batch Year</option>
                            @foreach($batchYears as $year)
                                <option value="{{ $year }}" {{ old('batch_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="previous_education" class="form-label">Previous Education <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="previous_education" name="previous_education" rows="2" required>{{ old('previous_education') }}</textarea>
                        <small class="text-muted">Please mention your school, college, grades/percentage, and years of study</small>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2">Emergency Contact Information</h5>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="emergency_contact_name" class="form-label">Contact Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="emergency_contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="emergency_contact_number" name="emergency_contact_number" value="{{ old('emergency_contact_number') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="emergency_contact_relationship" class="form-label">Relationship <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="emergency_contact_relationship" name="emergency_contact_relationship" value="{{ old('emergency_contact_relationship') }}" required>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2">Guardian Information</h5>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="guardian_name" class="form-label">Guardian Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="guardian_relation" class="form-label">Relationship <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="guardian_relation" name="guardian_relation" value="{{ old('guardian_relation') }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="guardian_contact" class="form-label">Contact Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="guardian_contact" name="guardian_contact" value="{{ old('guardian_contact') }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="guardian_address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="guardian_address" name="guardian_address" rows="2" required>{{ old('guardian_address') }}</textarea>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="guardian_occupation" class="form-label">Occupation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="guardian_occupation" name="guardian_occupation" value="{{ old('guardian_occupation') }}" required>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="border-bottom pb-2">Additional Information</h5>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="medical_information" class="form-label">Medical Information</label>
                        <textarea class="form-control" id="medical_information" name="medical_information" rows="2">{{ old('medical_information') }}</textarea>
                        <small class="text-muted">Please mention any medical conditions, allergies, or medications</small>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label for="documents" class="form-label">Supporting Documents</label>
                        <input type="file" class="form-control" id="documents" name="documents[]" multiple>
                        <small class="text-muted">You can upload multiple files. Max size per file: 5MB. Please include transcripts, ID proof, etc.</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                            <label class="form-check-label" for="declaration">
                                I declare that the information provided is true and correct to the best of my knowledge.
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 