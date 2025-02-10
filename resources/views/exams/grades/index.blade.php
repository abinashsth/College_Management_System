@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Grade Management</h5>
            @can('create grades')
            <a href="{{ route('grades.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Grade
            </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Grade Name</th>
                            <th>Point</th>
                            <th>Mark From</th>
                            <th>Mark To</th>
                            <th>Comment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $grade)
                        <tr>
                            <td>{{ $grade->name }}</td>
                            <td>{{ $grade->point }}</td>
                            <td>{{ $grade->mark_from }}</td>
                            <td>{{ $grade->mark_to }}</td>
                            <td>{{ $grade->comment }}</td>
                            <td>
                                <div class="btn-group">
                                    @can('edit grades')
                                    <a href="{{ route('grades.edit', $grade) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete grades')
                                    <form action="{{ route('grades.destroy', $grade) }}" method="POST" class="d-inline">
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
                            <td colspan="6" class="text-center">No grades found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
