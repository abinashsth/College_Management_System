@extends('layouts.app')

@section('title', 'Student Records')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mt-4">Student Records</h1>
        @can('create', App\Models\StudentRecord::class)
        <a href="{{ route('student-records.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Add Record
        </a>
        @endcan
    </div>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Student Records</li>
    </ol>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Records
        </div>
        <div class="card-body">
            <form action="{{ route('student-records.index') }}" method="GET" id="filter-form">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="student" class="form-label">Student</label>
                        <select class="form-select" id="student" name="student_id">
                            <option value="">All Students</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ request('student_id') == $student->id ? 'selected' : '' }}>
                                    {{ $student->student_id }} - {{ $student->first_name }} {{ $student->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="record_type" class="form-label">Record Type</label>
                        <select class="form-select" id="record_type" name="record_type">
                            <option value="">All Types</option>
                            <option value="personal" {{ request('record_type') == 'personal' ? 'selected' : '' }}>Personal</option>
                            <option value="academic" {{ request('record_type') == 'academic' ? 'selected' : '' }}>Academic</option>
                            <option value="enrollment" {{ request('record_type') == 'enrollment' ? 'selected' : '' }}>Enrollment</option>
                            <option value="disciplinary" {{ request('record_type') == 'disciplinary' ? 'selected' : '' }}>Disciplinary</option>
                            <option value="achievement" {{ request('record_type') == 'achievement' ? 'selected' : '' }}>Achievement</option>
                            <option value="medical" {{ request('record_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                            <option value="note" {{ request('record_type') == 'note' ? 'selected' : '' }}>Note</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="created_by" class="form-label">Created By</label>
                        <select class="form-select" id="created_by" name="created_by">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_range" class="form-label">Date Range</label>
                        <select class="form-select" id="date_range" name="date_range">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                            <option value="this_year" {{ request('date_range') == 'this_year' ? 'selected' : '' }}>This Year</option>
                            <option value="custom" {{ request('date_range') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                </div>
                
                <div class="row date-range-inputs" style="{{ request('date_range') == 'custom' ? '' : 'display: none;' }}">
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">From</label>
                        <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">To</label>
                        <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" placeholder="Search by title, description..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end mb-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('student-records.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Reset
                        </a>
                        @can('export', App\Models\StudentRecord::class)
                        <button type="submit" form="export-form" class="btn btn-success">
                            <i class="fas fa-file-export me-1"></i> Export
                        </button>
                        @endcan
                    </div>
                </div>
            </form>
            
            <!-- Hidden export form that copies filter parameters -->
            <form id="export-form" action="{{ route('student-records.export') }}" method="GET">
                <!-- Hidden fields will be populated via JavaScript -->
            </form>
        </div>
    </div>
    
    <!-- Records List -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-1"></i>
                Student Records
            </div>
            <div>
                <select class="form-select form-select-sm d-inline-block w-auto" id="records-per-page">
                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Created</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $record)
                            <tr>
                                <td>
                                    <a href="{{ route('student-records.show', $record->student) }}">
                                        {{ $record->student->student_id }} - {{ $record->student->first_name }} {{ $record->student->last_name }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $record->record_type_color }}">
                                        {{ $record->record_type_label }}
                                    </span>
                                </td>
                                <td>{{ $record->title }}</td>
                                <td>{{ $record->created_at->format('M d, Y g:i A') }}</td>
                                <td>{{ $record->createdBy->name }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('student-records.show', [$record->student, 'highlight' => $record->id]) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @can('update', $record)
                                        <a href="{{ route('student-records.edit', $record) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete', $record)
                                        <button type="button" class="btn btn-sm btn-danger delete-record" 
                                                data-bs-toggle="modal" data-bs-target="#deleteModal"
                                                data-record-id="{{ $record->id }}"
                                                data-record-title="{{ $record->title }}"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No records found</h5>
                                        <p class="text-muted">Try adjusting your search or filter criteria</p>
                                        @can('create', App\Models\StudentRecord::class)
                                        <a href="{{ route('student-records.create') }}" class="btn btn-primary mt-2">
                                            <i class="fas fa-plus me-1"></i> Add Record
                                        </a>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $records->firstItem() ?? 0 }} to {{ $records->lastItem() ?? 0 }} of {{ $records->total() }} records
                </div>
                <div>
                    {{ $records->appends(request()->except('page'))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the record "<span id="delete-record-title"></span>"?</p>
                <p class="text-danger">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle date range selection
        const dateRangeSelect = document.getElementById('date_range');
        const dateRangeInputs = document.querySelector('.date-range-inputs');
        
        dateRangeSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                dateRangeInputs.style.display = 'flex';
            } else {
                dateRangeInputs.style.display = 'none';
            }
        });
        
        // Handle per page dropdown
        const perPageSelect = document.getElementById('records-per-page');
        perPageSelect.addEventListener('change', function() {
            const url = new URL(window.location.href);
            url.searchParams.set('per_page', this.value);
            window.location.href = url.toString();
        });
        
        // Handle delete modal
        const deleteButtons = document.querySelectorAll('.delete-record');
        const deleteForm = document.getElementById('delete-form');
        const deleteRecordTitle = document.getElementById('delete-record-title');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const recordId = this.getAttribute('data-record-id');
                const recordTitle = this.getAttribute('data-record-title');
                
                deleteForm.action = `/student-records/${recordId}`;
                deleteRecordTitle.textContent = recordTitle;
            });
        });
        
        // Copy filter parameters to export form
        const filterForm = document.getElementById('filter-form');
        const exportForm = document.getElementById('export-form');
        
        document.getElementById('export-form').addEventListener('submit', function(e) {
            // Get all filter form inputs
            const filterInputs = filterForm.querySelectorAll('input, select');
            
            // Clear existing hidden fields
            while (exportForm.firstChild) {
                exportForm.removeChild(exportForm.firstChild);
            }
            
            // Copy values to export form
            filterInputs.forEach(input => {
                if (input.name && input.value) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = input.name;
                    hiddenField.value = input.value;
                    exportForm.appendChild(hiddenField);
                }
            });
        });
    });
</script>
@endsection 