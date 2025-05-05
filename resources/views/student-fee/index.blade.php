@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Fee Structure</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Student Fee Structure</h4>
                        <a href="{{ route('student-fee.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-2"></i>Add New Fee
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="fee-table" class="table table-striped table-hover align-middle">
                                <thead class="table-light">
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
                                            <span class="badge rounded-pill {{ $fee->status === 'Paid' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $fee->status }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('student-fee.edit', $fee->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('student-fee.show', $fee->id) }}" 
                                                   class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger delete-fee" 
                                                        data-id="{{ $fee->id }}" data-bs-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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

    {{-- Delete Confirmation Modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this fee record?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

    @push('scripts')
    <script>
        $(document).ready(function () {
            // Bootstrap tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // DataTable initialization
            const feeTable = $('#fee-table').DataTable({
                responsive: true,
                order: [[4, 'asc']],
                pageLength: 10,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records..."
                },
                dom: "<'row'<'col-md-6'l><'col-md-6'f>>" +
                     "<'row'<'col-12'tr>>" +
                     "<'row'<'col-md-5'i><'col-md-7'p>>",
                buttons: ['copy', 'excel', 'pdf']
            });

            // Handle delete button click
            let deleteFeeId = null;
            $(document).on('click', '.delete-fee', function () {
                deleteFeeId = $(this).data('id');
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });

            // Confirm deletion
            $('#confirmDelete').on('click', function () {
                if (!deleteFeeId) return;

                $.ajax({
                    url: `/student-fee/${deleteFeeId}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        $('#deleteModal').modal('hide');
                        showAlert('success', 'Record deleted successfully.');
                        feeTable.row($(`button[data-id="${deleteFeeId}"]`).closest('tr')).remove().draw();
                        deleteFeeId = null;
                    },
                    error: function () {
                        showAlert('danger', 'Failed to delete record.');
                    }
                });
            });

            // Reusable alert function
            function showAlert(type, message) {
                const alertHtml = `
                    <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                $('.card-body').prepend(alertHtml);
                setTimeout(() => {
                    $('.alert').alert('close');
                }, 3000);
            }

            // Add loading spinner to buttons
            $('.btn').on('click', function () {
                const $btn = $(this);
                if (!$btn.hasClass('disabled')) {
                    const $spinner = $('<span class="spinner-border spinner-border-sm me-2"></span>');
                    $btn.prepend($spinner).addClass('disabled');
                    setTimeout(() => {
                        $spinner.remove();
                        $btn.removeClass('disabled');
                    }, 1000);
                }
            });
        });
    </script>
    @endpush
</body>
</html>
@endsection
