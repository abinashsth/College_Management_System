@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Exam List</h5>
            @can('create exams')
            <a href="{{ route('exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Exam
            </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Exam Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                        <tr>
                            <td>{{ $exam->name }}</td>
                            <td>{{ $exam->start_date->format('d M, Y') }}</td>
                            <td>{{ $exam->end_date->format('d M, Y') }}</td>
                            <td>
                                <span class="badge bg-{{ $exam->status === 'active' ? 'success' : 'warning' }}">
                                    {{ ucfirst($exam->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('exams.show', $exam) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit exams')
                                    <a href="{{ route('exams.edit', $exam) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete exams')
                                    <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No exams found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
