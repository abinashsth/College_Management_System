@extends('layouts.app')

@section('title', 'Student Record History')

@section('styles')
<style>
    .timeline {
        position: relative;
        padding: 20px 0;
    }
    .timeline::before {
        content: '';
        position: absolute;
        width: 4px;
        background-color: #e0e0e0;
        top: 0;
        bottom: 0;
        left: 8px;
        margin-left: -1px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 25px;
        margin-left: 20px;
    }
    .timeline-icon {
        position: absolute;
        top: 0;
        left: -28px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        z-index: 10;
    }
    .timeline-content {
        padding: 15px;
        border-radius: 5px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    }
    .timeline-date {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .type-personal { background-color: #d1e7dd; }
    .type-academic { background-color: #cfe2ff; }
    .type-enrollment { background-color: #fff3cd; }
    .type-disciplinary { background-color: #f8d7da; }
    .type-achievement { background-color: #e2e3e5; }
    .type-medical { background-color: #d1e7dd; }
    .type-note { background-color: #e2e3e5; }
    .record-card {
        border-left-width: 4px;
        border-left-style: solid;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Student Record History</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student-records.index') }}">Student Records</a></li>
        <li class="breadcrumb-item active">{{ $student->first_name }} {{ $student->last_name }}</li>
    </ol>
    
    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-graduate me-1"></i>
                        Student Information
                    </div>
                    <div>
                        <a href="{{ route('students.show', $student) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user me-1"></i> Full Profile
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">ID:</div>
                        <div class="col-sm-8">{{ $student->student_id }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Name:</div>
                        <div class="col-sm-8">{{ $student->first_name }} {{ $student->last_name }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Program:</div>
                        <div class="col-sm-8">{{ $student->program->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Batch:</div>
                        <div class="col-sm-8">{{ $student->batch_year ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Department:</div>
                        <div class="col-sm-8">{{ $student->department->name ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Status:</div>
                        <div class="col-sm-8">
                            @if($student->enrollment_status == 'active')
                                <span class="badge bg-success">Active</span>
                            @elseif($student->enrollment_status == 'graduated')
                                <span class="badge bg-primary">Graduated</span>
                            @elseif($student->enrollment_status == 'suspended')
                                <span class="badge bg-warning">Suspended</span>
                            @elseif($student->enrollment_status == 'expelled')
                                <span class="badge bg-danger">Expelled</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($student->enrollment_status) }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Email:</div>
                        <div class="col-sm-8">{{ $student->email ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 fw-bold">Phone:</div>
                        <div class="col-sm-8">{{ $student->phone ?? 'N/A' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 fw-bold">Enrollment:</div>
                        <div class="col-sm-8">{{ $student->enrollment_date ? date('M d, Y', strtotime($student->enrollment_date)) : 'N/A' }}</div>
                    </div>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('student-records.create', $student) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus me-1"></i> Add New Record
                        </a>
                        <div class="btn-group mt-2" role="group">
                            <a href="{{ route('student-records.export-pdf', $student->id) }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i> Export PDF
                            </a>
                            <a href="{{ route('student-records.export-excel', $student->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-history me-1"></i>
                    Record History
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <form action="{{ route('student-records.show', $student) }}" method="GET" class="row g-3">
                                <div class="col-md-5">
                                    <select class="form-select form-select-sm" name="record_type">
                                        <option value="">All Record Types</option>
                                        <option value="personal" {{ request('record_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                                        <option value="academic" {{ request('record_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                                        <option value="enrollment" {{ request('record_type') == 'enrollment' ? 'selected' : '' }}>Enrollment</option>
                                        <option value="disciplinary" {{ request('record_type') == 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                                        <option value="achievement" {{ request('record_type') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                                        <option value="medical" {{ request('record_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                        <option value="note" {{ request('record_type') == 'note' ? 'selected' : '' }}>Note</option>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Search records..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    @if(count($records) > 0)
                        <div class="timeline-container">
                            @foreach($records as $record)
                                <div class="timeline-item">
                                    <div class="timeline-item-content">
                                        <div class="record-header">
                                            <span class="timeline-date">{{ $record->created_at->format('M d, Y') }}</span>
                                            <span class="badge bg-{{ 
                                                $record->record_type == 'personal' ? 'info' : 
                                                ($record->record_type == 'academic' ? 'primary' : 
                                                ($record->record_type == 'enrollment' ? 'secondary' : 
                                                ($record->record_type == 'disciplinary' ? 'danger' : 
                                                ($record->record_type == 'achievement' ? 'success' : 
                                                ($record->record_type == 'medical' ? 'danger' : 'dark'))))))
                                            }}">
                                                {{ ucfirst($record->record_type) }}
                                            </span>
                                        </div>
                                        
                                        <h5 class="mt-2">{{ $record->title }}</h5>
                                        
                                        <div class="record-details mt-2">
                                            <p>{{ $record->description }}</p>
                                            
                                            @if($record->record_data)
                                                <div class="record-data mt-3">
                                                    @foreach(json_decode($record->record_data, true) as $key => $value)
                                                        @if(!empty($value))
                                                            <div class="row mb-1">
                                                                <div class="col-sm-4 fw-bold">{{ ucwords(str_replace('_', ' ', $key)) }}:</div>
                                                                <div class="col-sm-8">{{ $value }}</div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="record-footer mt-3 text-muted">
                                            <small>Added by: {{ $record->created_by_user->name ?? 'System' }} at {{ $record->created_at->format('h:i A') }}</small>
                                        </div>
                                        
                                        <div class="record-actions mt-2">
                                            @can('edit', $record)
                                                <a href="{{ route('student-records.edit', $record->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            @endcan
                                            
                                            @can('delete', $record)
                                                <form action="{{ route('student-records.destroy', $record->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this record?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $records->links() }}
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            No records found for this student.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline-container {
        position: relative;
        padding: 10px 0;
    }
    
    .timeline-container::after {
        content: '';
        position: absolute;
        width: 2px;
        background-color: #e9ecef;
        top: 0;
        bottom: 0;
        left: 20px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 45px;
    }
    
    .timeline-item::before {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background-color: #0d6efd;
        border: 2px solid #fff;
        left: 12px;
        top: 15px;
        z-index: 1;
    }
    
    .timeline-item-content {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        position: relative;
        border-left: 3px solid #0d6efd;
    }
    
    .timeline-date {
        font-weight: bold;
        margin-right: 10px;
    }
    
    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
    }
    
    .record-details {
        color: #495057;
    }
    
    .record-footer {
        border-top: 1px solid #dee2e6;
        padding-top: 10px;
    }
</style>
@endsection 