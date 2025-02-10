@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Batch Fix</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('batch.fix.process') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="current_batch" class="form-label">Current Batch</label>
                        <select name="current_batch" id="current_batch" class="form-select" required>
                            <option value="">Select Current Batch</option>
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="new_batch" class="form-label">New Batch</label>
                        <select name="new_batch" id="new_batch" class="form-select" required>
                            <option value="">Select New Batch</option>
                            @foreach($batches as $batch)
                            <option value="{{ $batch->id }}">{{ $batch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sync"></i> Process Batch Fix
                    </button>
                </div>
            </form>

            @if(session('success'))
            <div class="alert alert-success mt-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-danger mt-4">
                {{ session('error') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
