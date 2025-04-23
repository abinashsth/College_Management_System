@extends('layouts.app')

@section('title', 'View Record: ' . $record->title)

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Student Record Details</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student-records.index') }}">Student Records</a></li>
        <li class="breadcrumb-item"><a href="{{ route('student-records.show', $record->student) }}">{{ $record->student->first_name }} {{ $record->student->last_name }}</a></li>
        <li class="breadcrumb-item active">{{ $record->title }}</li>
    </ol>
    
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="row">
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-user-graduate me-1"></i>
                        Student Information
                    </div>
                    <div>
                        <a href="{{ route('students.show', $record->student) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-user me-1"></i> Full Profile
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong>
                        <div>{{ $record->student->first_name }} {{ $record->student->last_name }}</div>
                    </div>
                    <div class="mb-3">
                        <strong>ID:</strong>
                        <div>{{ $record->student->student_id ?? $record->student->admission_number }}</div>
                    </div>
                    @if($record->student->email)
                    <div class="mb-3">
                        <strong>Email:</strong>
                        <div>{{ $record->student->email }}</div>
                    </div>
                    @endif
                    @if($record->student->phone)
                    <div class="mb-3">
                        <strong>Phone:</strong>
                        <div>{{ $record->student->phone }}</div>
                    </div>
                    @endif
                    @if($record->student->program)
                    <div class="mb-3">
                        <strong>Program:</strong>
                        <div>{{ $record->student->program->name ?? 'Not Assigned' }}</div>
                    </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('student-records.show', $record->student) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to All Records
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-clipboard me-1"></i>
                    Record Details: {{ $record->title }}
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <h5 class="card-title">{{ $record->title }}</h5>
                            <span class="badge bg-{{ $record->record_type == 'disciplinary' ? 'danger' : ($record->record_type == 'achievement' ? 'success' : 'primary') }}">
                                {{ ucfirst($record->record_type) }}
                            </span>
                        </div>
                        <small class="text-muted">Created: {{ $record->created_at->format('M d, Y g:i A') }} by {{ $record->createdBy->name }}</small>
                        @if($record->created_at->ne($record->updated_at))
                            <small class="text-muted d-block">Updated: {{ $record->updated_at->format('M d, Y g:i A') }} by {{ $record->updatedBy->name }}</small>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <h6 class="fw-bold">Description:</h6>
                        <div class="card-text p-3 bg-light rounded">
                            {!! nl2br(e($record->description)) !!}
                        </div>
                    </div>
                    
                    @if($record->record_data && count($record->record_data) > 0)
                    <div class="mb-4">
                        <h6 class="fw-bold">Additional Information:</h6>
                        <div class="card p-3">
                            <table class="table table-bordered table-striped mb-0">
                                <tbody>
                                    @foreach($record->record_data as $key => $value)
                                    <tr>
                                        <th width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                        <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    
                    @if($record->attachment_path)
                    <div class="mb-4">
                        <h6 class="fw-bold">Attachment:</h6>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-paperclip me-2"></i>
                            <a href="{{ Storage::url($record->attachment_path) }}" class="btn btn-outline-primary" target="_blank">
                                {{ $record->attachment_name ?? 'Download Attachment' }}
                            </a>
                        </div>
                    </div>
                    @endif
                    
                    <div class="d-flex justify-content-between mt-4">
                        <div>
                            <a href="{{ route('student-records.show', $record->student) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Back to All Records
                            </a>
                        </div>
                        <div>
                            @can('update', $record)
                            <a href="{{ route('student-records.edit', $record) }}" class="btn btn-warning">
                                <i class="fas fa-edit me-1"></i> Edit Record
                            </a>
                            @endcan
                            
                            @can('delete', $record)
                            <form action="{{ route('student-records.destroy', $record) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record? This action cannot be undone.')">
                                    <i class="fas fa-trash me-1"></i> Delete Record
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 