@extends('layouts.app')

@section('title', isset($record) ? 'Edit Student Record' : 'Add Student Record')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">{{ isset($record) ? 'Edit Student Record' : 'Add Student Record' }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student-records.index') }}">Student Records</a></li>
        @if(isset($student))
            <li class="breadcrumb-item"><a href="{{ route('student-records.show', $student) }}">{{ $student->first_name }} {{ $student->last_name }}</a></li>
        @endif
        <li class="breadcrumb-item active">{{ isset($record) ? 'Edit Record' : 'Add Record' }}</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            {{ isset($record) ? 'Edit Record Information' : 'Add New Record' }}
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ isset($record) ? route('student-records.update', $record) : route('student-records.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($record))
                    @method('PUT')
                @endif
                
                @if(!isset($student) && !isset($record))
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Student</label>
                        <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ (old('student_id') == $student->id || (isset($record) && $record->student_id == $student->id)) ? 'selected' : '' }}>
                                    {{ $student->student_id }} - {{ $student->first_name }} {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @else
                    <input type="hidden" name="student_id" value="{{ $student->id ?? $record->student_id }}">
                    <div class="mb-3">
                        <label class="form-label">Student</label>
                        <input type="text" class="form-control" value="{{ isset($student) ? $student->student_id . ' - ' . $student->first_name . ' ' . $student->last_name : $record->student->student_id . ' - ' . $record->student->first_name . ' ' . $record->student->last_name }}" disabled>
                    </div>
                @endif
                
                <div class="mb-3">
                    <label for="record_type" class="form-label">Record Type</label>
                    <select class="form-select @error('record_type') is-invalid @enderror" id="record_type" name="record_type" required>
                        <option value="">Select Record Type</option>
                        <option value="personal" {{ old('record_type', isset($record) ? $record->record_type : '') == 'personal' ? 'selected' : '' }}>Personal Information</option>
                        <option value="academic" {{ old('record_type', isset($record) ? $record->record_type : '') == 'academic' ? 'selected' : '' }}>Academic Information</option>
                        <option value="enrollment" {{ old('record_type', isset($record) ? $record->record_type : '') == 'enrollment' ? 'selected' : '' }}>Enrollment Status</option>
                        <option value="disciplinary" {{ old('record_type', isset($record) ? $record->record_type : '') == 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                        <option value="achievement" {{ old('record_type', isset($record) ? $record->record_type : '') == 'achievement' ? 'selected' : '' }}>Achievements</option>
                        <option value="medical" {{ old('record_type', isset($record) ? $record->record_type : '') == 'medical' ? 'selected' : '' }}>Medical</option>
                        <option value="notes" {{ old('record_type', isset($record) ? $record->record_type : '') == 'notes' ? 'selected' : '' }}>General Notes</option>
                    </select>
                    @error('record_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', isset($record) ? $record->title : '') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', isset($record) ? $record->description : '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <!-- Dynamic form fields based on record type -->
                <div class="record-fields-container mt-4">
                    <h4>Additional Information</h4>
                    <hr>
                    
                    <!-- Personal Information Fields -->
                    <div class="record-type-fields" id="personal-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control record-data" id="address" name="record_data[address]" value="{{ old('record_data.address', isset($record) && $record->record_data ? json_decode($record->record_data, true)['address'] ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control record-data" id="phone" name="record_data[phone]" value="{{ old('record_data.phone', isset($record) && $record->record_data ? json_decode($record->record_data, true)['phone'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control record-data" id="email" name="record_data[email]" value="{{ old('record_data.email', isset($record) && $record->record_data ? json_decode($record->record_data, true)['email'] ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                    <input type="text" class="form-control record-data" id="emergency_contact" name="record_data[emergency_contact]" value="{{ old('record_data.emergency_contact', isset($record) && $record->record_data ? json_decode($record->record_data, true)['emergency_contact'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Academic Information Fields -->
                    <div class="record-type-fields" id="academic-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="semester" class="form-label">Semester</label>
                                    <input type="text" class="form-control record-data" id="semester" name="record_data[semester]" value="{{ old('record_data.semester', isset($record) && $record->record_data ? json_decode($record->record_data, true)['semester'] ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gpa" class="form-label">GPA</label>
                                    <input type="text" class="form-control record-data" id="gpa" name="record_data[gpa]" value="{{ old('record_data.gpa', isset($record) && $record->record_data ? json_decode($record->record_data, true)['gpa'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="academic_standing" class="form-label">Academic Standing</label>
                                    <select class="form-select record-data" id="academic_standing" name="record_data[academic_standing]">
                                        <option value="">Select Standing</option>
                                        <option value="good" {{ (old('record_data.academic_standing', isset($record) && $record->record_data ? json_decode($record->record_data, true)['academic_standing'] ?? '' : '') == 'good') ? 'selected' : '' }}>Good Standing</option>
                                        <option value="probation" {{ (old('record_data.academic_standing', isset($record) && $record->record_data ? json_decode($record->record_data, true)['academic_standing'] ?? '' : '') == 'probation') ? 'selected' : '' }}>Probation</option>
                                        <option value="warning" {{ (old('record_data.academic_standing', isset($record) && $record->record_data ? json_decode($record->record_data, true)['academic_standing'] ?? '' : '') == 'warning') ? 'selected' : '' }}>Warning</option>
                                        <option value="dismissed" {{ (old('record_data.academic_standing', isset($record) && $record->record_data ? json_decode($record->record_data, true)['academic_standing'] ?? '' : '') == 'dismissed') ? 'selected' : '' }}>Dismissed</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="credits_earned" class="form-label">Credits Earned</label>
                                    <input type="number" class="form-control record-data" id="credits_earned" name="record_data[credits_earned]" value="{{ old('record_data.credits_earned', isset($record) && $record->record_data ? json_decode($record->record_data, true)['credits_earned'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Enrollment Status Fields -->
                    <div class="record-type-fields" id="enrollment-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select record-data" id="status" name="record_data[status]">
                                        <option value="">Select Status</option>
                                        <option value="active" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'active') ? 'selected' : '' }}>Active</option>
                                        <option value="leave" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'leave') ? 'selected' : '' }}>Leave of Absence</option>
                                        <option value="withdrawn" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'withdrawn') ? 'selected' : '' }}>Withdrawn</option>
                                        <option value="transferred" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'transferred') ? 'selected' : '' }}>Transferred</option>
                                        <option value="graduated" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'graduated') ? 'selected' : '' }}>Graduated</option>
                                        <option value="suspended" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'suspended') ? 'selected' : '' }}>Suspended</option>
                                        <option value="expelled" {{ (old('record_data.status', isset($record) && $record->record_data ? json_decode($record->record_data, true)['status'] ?? '' : '') == 'expelled') ? 'selected' : '' }}>Expelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="effective_date" class="form-label">Effective Date</label>
                                    <input type="date" class="form-control record-data" id="effective_date" name="record_data[effective_date]" value="{{ old('record_data.effective_date', isset($record) && $record->record_data ? json_decode($record->record_data, true)['effective_date'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="enrollment_reason" class="form-label">Reason for Change</label>
                            <textarea class="form-control record-data" id="enrollment_reason" name="record_data[reason]" rows="2">{{ old('record_data.reason', isset($record) && $record->record_data ? json_decode($record->record_data, true)['reason'] ?? '' : '') }}</textarea>
                        </div>
                    </div>
                    
                    <!-- Disciplinary Fields -->
                    <div class="record-type-fields" id="disciplinary-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="incident_date" class="form-label">Incident Date</label>
                                    <input type="date" class="form-control record-data" id="incident_date" name="record_data[incident_date]" value="{{ old('record_data.incident_date', isset($record) && $record->record_data ? json_decode($record->record_data, true)['incident_date'] ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="severity" class="form-label">Severity</label>
                                    <select class="form-select record-data" id="severity" name="record_data[severity]">
                                        <option value="">Select Severity</option>
                                        <option value="minor" {{ (old('record_data.severity', isset($record) && $record->record_data ? json_decode($record->record_data, true)['severity'] ?? '' : '') == 'minor') ? 'selected' : '' }}>Minor</option>
                                        <option value="moderate" {{ (old('record_data.severity', isset($record) && $record->record_data ? json_decode($record->record_data, true)['severity'] ?? '' : '') == 'moderate') ? 'selected' : '' }}>Moderate</option>
                                        <option value="major" {{ (old('record_data.severity', isset($record) && $record->record_data ? json_decode($record->record_data, true)['severity'] ?? '' : '') == 'major') ? 'selected' : '' }}>Major</option>
                                        <option value="severe" {{ (old('record_data.severity', isset($record) && $record->record_data ? json_decode($record->record_data, true)['severity'] ?? '' : '') == 'severe') ? 'selected' : '' }}>Severe</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="sanction" class="form-label">Sanction/Action Taken</label>
                            <input type="text" class="form-control record-data" id="sanction" name="record_data[sanction]" value="{{ old('record_data.sanction', isset($record) && $record->record_data ? json_decode($record->record_data, true)['sanction'] ?? '' : '') }}">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control record-data" id="start_date" name="record_data[start_date]" value="{{ old('record_data.start_date', isset($record) && $record->record_data ? json_decode($record->record_data, true)['start_date'] ?? '' : '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control record-data" id="end_date" name="record_data[end_date]" value="{{ old('record_data.end_date', isset($record) && $record->record_data ? json_decode($record->record_data, true)['end_date'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Achievement Fields -->
                    <div class="record-type-fields" id="achievement-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="achievement_type" class="form-label">Type of Achievement</label>
                                    <select class="form-select record-data" id="achievement_type" name="record_data[achievement_type]">
                                        <option value="">Select Type</option>
                                        <option value="academic" {{ (old('record_data.achievement_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['achievement_type'] ?? '' : '') == 'academic') ? 'selected' : '' }}>Academic</option>
                                        <option value="sports" {{ (old('record_data.achievement_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['achievement_type'] ?? '' : '') == 'sports') ? 'selected' : '' }}>Sports</option>
                                        <option value="extracurricular" {{ (old('record_data.achievement_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['achievement_type'] ?? '' : '') == 'extracurricular') ? 'selected' : '' }}>Extracurricular</option>
                                        <option value="leadership" {{ (old('record_data.achievement_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['achievement_type'] ?? '' : '') == 'leadership') ? 'selected' : '' }}>Leadership</option>
                                        <option value="community" {{ (old('record_data.achievement_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['achievement_type'] ?? '' : '') == 'community') ? 'selected' : '' }}>Community Service</option>
                                        <option value="research" {{ (old('record_data.achievement_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['achievement_type'] ?? '' : '') == 'research') ? 'selected' : '' }}>Research</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="achievement_date" class="form-label">Date of Achievement</label>
                                    <input type="date" class="form-control record-data" id="achievement_date" name="record_data[date]" value="{{ old('record_data.date', isset($record) && $record->record_data ? json_decode($record->record_data, true)['date'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="issuing_organization" class="form-label">Issuing Organization/Authority</label>
                            <input type="text" class="form-control record-data" id="issuing_organization" name="record_data[issuing_organization]" value="{{ old('record_data.issuing_organization', isset($record) && $record->record_data ? json_decode($record->record_data, true)['issuing_organization'] ?? '' : '') }}">
                        </div>
                    </div>
                    
                    <!-- Medical Fields -->
                    <div class="record-type-fields" id="medical-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medical_type" class="form-label">Type of Medical Record</label>
                                    <select class="form-select record-data" id="medical_type" name="record_data[medical_type]">
                                        <option value="">Select Type</option>
                                        <option value="condition" {{ (old('record_data.medical_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['medical_type'] ?? '' : '') == 'condition') ? 'selected' : '' }}>Medical Condition</option>
                                        <option value="accommodation" {{ (old('record_data.medical_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['medical_type'] ?? '' : '') == 'accommodation') ? 'selected' : '' }}>Accommodation</option>
                                        <option value="absence" {{ (old('record_data.medical_type', isset($record) && $record->record_data ? json_decode($record->record_data, true)['medical_type'] ?? '' : '') == 'absence') ? 'selected' : '' }}>Medical Absence</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medical_date" class="form-label">Date</label>
                                    <input type="date" class="form-control record-data" id="medical_date" name="record_data[date]" value="{{ old('record_data.date', isset($record) && $record->record_data ? json_decode($record->record_data, true)['date'] ?? '' : '') }}">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="medical_details" class="form-label">Medical Details</label>
                            <textarea class="form-control record-data" id="medical_details" name="record_data[details]" rows="2">{{ old('record_data.details', isset($record) && $record->record_data ? json_decode($record->record_data, true)['details'] ?? '' : '') }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="doctor_contact" class="form-label">Doctor/Medical Provider</label>
                            <input type="text" class="form-control record-data" id="doctor_contact" name="record_data[doctor_contact]" value="{{ old('record_data.doctor_contact', isset($record) && $record->record_data ? json_decode($record->record_data, true)['doctor_contact'] ?? '' : '') }}">
                        </div>
                    </div>
                    
                    <!-- General Notes Fields -->
                    <div class="record-type-fields" id="notes-fields" style="display: none;">
                        <div class="mb-3">
                            <label for="notes_details" class="form-label">Additional Notes</label>
                            <textarea class="form-control record-data" id="notes_details" name="record_data[details]" rows="5">{{ old('record_data.details', isset($record) && $record->record_data ? json_decode($record->record_data, true)['details'] ?? '' : '') }}</textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Attachments -->
                <div class="mb-3 mt-4">
                    <label for="attachments" class="form-label">Attachments (Optional)</label>
                    <input type="file" class="form-control @error('attachments') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                    <small class="text-muted">You can upload multiple files. Maximum size: 10MB per file.</small>
                    @error('attachments')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    
                    @if(isset($record) && $record->attachments)
                        <div class="mt-2">
                            <strong>Current Attachments:</strong>
                            <ul class="list-group mt-2">
                                @foreach(json_decode($record->attachments, true) as $attachment)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ Storage::url($attachment['path']) }}" target="_blank">
                                            {{ $attachment['original_name'] }}
                                        </a>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="delete_attachments[]" value="{{ $attachment['path'] }}" id="delete-{{ $loop->index }}">
                                            <label class="form-check-label text-danger" for="delete-{{ $loop->index }}">
                                                Delete
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="notify_student" name="notify_student" {{ old('notify_student') ? 'checked' : '' }}>
                    <label class="form-check-label" for="notify_student">Notify student via email</label>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ isset($student) ? route('student-records.show', $student) : route('student-records.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        {{ isset($record) ? 'Update Record' : 'Save Record' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recordTypeSelect = document.getElementById('record_type');
        const recordTypeFields = document.querySelectorAll('.record-type-fields');
        
        // Function to show fields based on selected record type
        function toggleRecordFields() {
            const selectedType = recordTypeSelect.value;
            
            // Hide all fields first
            recordTypeFields.forEach(field => {
                field.style.display = 'none';
            });
            
            // Show the selected type's fields
            if (selectedType) {
                const fieldsToShow = document.getElementById(selectedType + '-fields');
                if (fieldsToShow) {
                    fieldsToShow.style.display = 'block';
                }
            }
        }
        
        // Show fields on initial load
        toggleRecordFields();
        
        // Show fields when selection changes
        recordTypeSelect.addEventListener('change', toggleRecordFields);
    });
</script>
@endsection 