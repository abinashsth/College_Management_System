@extends('layouts.master')
@section('content')

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Student Fee Structure</h4>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('student-fee.create') }}" class="btn btn-primary">Add New Fee</a>
                            </div>
                        </div>

                        <div class="card-body">
                            <table id="fee-table" class="table table-bordered dt-responsive nowrap">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Fee Type</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($studentFees as $fee)
                                    <tr>
                                        <td>{{ $fee->student->student_id }}</td>
                                        <td>{{ $fee->student->name }}</td>
                                        <td>{{ $fee->fee_type }}</td>
                                        <td>${{ number_format($fee->amount, 2) }}</td>
                                        <td>{{ $fee->due_date->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge {{ $fee->status === 'Paid' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $fee->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('student-fee.edit', $fee->id) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('student-fee.show', $fee->id) }}" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <form action="{{ route('student-fee.destroy', $fee->id) }}" 
                                                      method="POST" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-sm btn-danger" 
                                                            onclick="return confirm('Are you sure?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('#fee-table').DataTable({
            responsive: true,
            order: [[4, 'asc']], // Sort by due date by default
            pageLength: 10
        });
    });
</script>
@endpush

@endsection
