@extends('layouts.app')

@section('title', 'Add Record Entry')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Add Record Entry: {{ $student->first_name }} {{ $student->last_name }}</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student-records.index') }}">Student Records</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student-records.show', $student) }}">{{ $student->student_id }}</a></li>
        <li class="breadcrumb-item active">Add Record</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-edit me-1"></i>
            New Record Entry Form
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
            
            <form action="{{ route('student-records.store', $student) }}" method="POST">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="record_type" class="form-label">Record Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('record_type') is-invalid @enderror" id="record_type" name="record_type" required>
                                <option value="">Select Record Type</option>
                                <option value="personal" {{ old('record_type') == 'personal' ? 'selected' : '' }}>Personal Information</option>
                                <option value="academic" {{ old('record_type') == 'academic' ? 'selected' : '' }}>Academic Information</option>
                                <option value="enrollment" {{ old('record_type') == 'enrollment' ? 'selected' : '' }}>Enrollment Status</option>
                                <option value="disciplinary" {{ old('record_type') == 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                                <option value="achievement" {{ old('record_type') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                                <option value="medical" {{ old('record_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="note" {{ old('record_type') == 'note' ? 'selected' : '' }}>General Note</option>
                            </select>
                            @error('record_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="change_reason" class="form-label">Reason for Change/Entry <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('change_reason') is-invalid @enderror" id="change_reason" name="change_reason" value="{{ old('change_reason') }}" required>
                            @error('change_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <!-- Personal Information Fields -->
                <div id="personal_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">Personal Information Update</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="record_data[first_name]" value="{{ old('record_data.first_name', $student->first_name) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="record_data[last_name]" value="{{ old('record_data.last_name', $student->last_name) }}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="record_data[email]" value="{{ old('record_data.email', $student->email) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="record_data[phone]" value="{{ old('record_data.phone', $student->phone) }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="record_data[address]" rows="2">{{ old('record_data.address', $student->address) }}</textarea>
                    </div>
                </div>
                
                <!-- Academic Information Fields -->
                <div id="academic_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">Academic Information Update</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="program_id" class="form-label">Program</label>
                                <select class="form-select" id="program_id" name="record_data[program_id]">
                                    <option value="">Select Program</option>
                                    @foreach($programs as $program)
                                        <option value="{{ $program->id }}" {{ old('record_data.program_id', $student->program_id) == $program->id ? 'selected' : '' }}>
                                            {{ $program->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department</label>
                                <select class="form-select" id="department_id" name="record_data[department_id]">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('record_data.department_id', $student->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="academic_session_id" class="form-label">Academic Session</label>
                                <select class="form-select" id="academic_session_id" name="record_data[academic_session_id]">
                                    <option value="">Select Academic Session</option>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}" {{ old('record_data.academic_session_id', $student->academic_session_id) == $session->id ? 'selected' : '' }}>
                                            {{ $session->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="batch_year" class="form-label">Batch Year</label>
                                <input type="number" class="form-control" id="batch_year" name="record_data[batch_year]" value="{{ old('record_data.batch_year', $student->batch_year) }}">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Enrollment Status Fields -->
                <div id="enrollment_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">Enrollment Status Update</h4>
                    <div class="mb-3">
                        <label for="enrollment_status" class="form-label">Enrollment Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="enrollment_status" name="record_data[enrollment_status]">
                            <option value="active" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="graduated" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                            <option value="suspended" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="expelled" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'expelled' ? 'selected' : '' }}>Expelled</option>
                            <option value="transferred" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                            <option value="withdrawn" {{ old('record_data.enrollment_status', $student->enrollment_status) == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                        </select>
                    </div>
                </div>
                
                <!-- Disciplinary Fields -->
                <div id="disciplinary_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">Disciplinary Record</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="incident_date" class="form-label">Incident Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="incident_date" name="record_data[incident_date]" value="{{ old('record_data.incident_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="incident_type" class="form-label">Incident Type <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="incident_type" name="record_data[incident_type]" value="{{ old('record_data.incident_type') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="incident_description" class="form-label">Incident Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="incident_description" name="record_data[incident_description]" rows="2">{{ old('record_data.incident_description') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="action_taken" class="form-label">Action Taken <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="action_taken" name="record_data[action_taken]" rows="2">{{ old('record_data.action_taken') }}</textarea>
                    </div>
                </div>
                
                <!-- Achievement Fields -->
                <div id="achievement_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">Achievement Record</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="achievement_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="achievement_date" name="record_data[date]" value="{{ old('record_data.date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="achievement_title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="achievement_title" name="record_data[title]" value="{{ old('record_data.title') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="achievement_description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="achievement_description" name="record_data[description]" rows="2">{{ old('record_data.description') }}</textarea>
                    </div>
                </div>
                
                <!-- Medical Fields -->
                <div id="medical_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">Medical Record</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="medical_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="medical_date" name="record_data[date]" value="{{ old('record_data.date', date('Y-m-d')) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="medical_condition" class="form-label">Condition/Issue <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="medical_condition" name="record_data[condition]" value="{{ old('record_data.condition') }}">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="medical_details" class="form-label">Details <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="medical_details" name="record_data[details]" rows="2">{{ old('record_data.details') }}</textarea>
                    </div>
                </div>
                
                <!-- General Note Fields -->
                <div id="note_fields" class="record-type-fields d-none">
                    <h4 class="mt-3 mb-3">General Note</h4>
                    <div class="mb-3">
                        <label for="note_subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="note_subject" name="record_data[subject]" value="{{ old('record_data.subject') }}">
                    </div>
                    <div class="mb-3">
                        <label for="note_content" class="form-label">Note Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="note_content" name="record_data[content]" rows="4">{{ old('record_data.content') }}</textarea>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="notes" class="form-label">Additional Notes</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('student-records.show', $student) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Record</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recordType = document.getElementById('record_type');
        const recordTypeFields = document.querySelectorAll('.record-type-fields');
        
        function showFieldsForType(type) {
            // Hide all fields first
            recordTypeFields.forEach(field => {
                field.classList.add('d-none');
            });
            
            // Show the fields for the selected type
            if (type) {
                const fieldsToShow = document.getElementById(type + '_fields');
                if (fieldsToShow) {
                    fieldsToShow.classList.remove('d-none');
                }
            }
        }
        
        // Show fields based on initial selection
        showFieldsForType(recordType.value);
        
        // Add event listener for changes
        recordType.addEventListener('change', function() {
            showFieldsForType(this.value);
        });
    });
</script>
@endsection 