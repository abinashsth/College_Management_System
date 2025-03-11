<!-- resources/views/employees/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h2>Employee Profile</h2>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="row">
                <!-- Left Panel (Profile Summary) -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                @if($employee->profile_picture)
                                    <img src="{{ Storage::url($employee->profile_picture) }}" alt="{{ $employee->name }}" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                        <h1>{{ substr($employee->name, 0, 1) }}</h1>
                                    </div>
                                @endif
                            </div>
                            
                            <h4>{{ $employee->name }}</h4>
                            <p class="text-muted">{{ $employee->designation }}</p>
                            
                            <ul class="list-group list-group-flush text-start mt-4">
                                <li class="list-group-item"><strong>Department:</strong> {{ $employee->department }}</li>
                                <li class="list-group-item"><strong>Type:</strong> {{ $employee->employee_type }}</li>
                                <li class="list-group-item"><strong>Phone:</strong> {{ $employee->contact_number ?? 'N/A' }}</li>
                                <li class="list-group-item"><strong>Email:</strong> {{ $employee->email }}</li>
                            </ul>
                            
                            <div class="mt-3">
                                <a href="{{ route('account.employees.edit', $employee) }}" class="btn btn-warning">Edit Profile</a>
                                <a href="{{ route('salaries.history', $employee) }}" class="btn btn-info">Salary History</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Panel (Detailed Information) -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h4>Personal Details</h4>
                                    <hr>
                                    <table class="table">
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $employee->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth</th>
                                            <td>{{ $employee->date_of_birth ? $employee->date_of_birth->format('d/m/Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td>{{ $employee->gender ? ucfirst($employee->gender) : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $employee->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Contact Number</th>
                                            <td>{{ $employee->contact_number ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $employee->email }}</td>
                                        </tr>
                                    </table>
                                </div>
                                
                                <div class="col-md-6">
                                    <h4>Job Details</h4>
                                    <hr>
                                    <table class="table">
                                        <tr>
                                            <th>Employee ID</th>
                                            <td>{{ $employee->employee_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Designation</th>
                                            <td>{{ $employee->designation }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $employee->department }}</td>
                                        </tr>
                                        <tr>
                                            <th>Joining Date</th>
                                            <td>{{ $employee->joining_date->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Current Salary</th>
                                            <td>
                                                @if($employee->currentSalary)
                                                    ${{ number_format($employee->currentSalary->amount, 2) }}
                                                    <a href="{{ route('salaries.create', $employee) }}" class="btn btn-sm btn-primary">Update</a>
                                                @else
                                                    Not set
                                                    <a href="{{ route('salaries.create', $employee) }}" class="btn btn-sm btn-primary">Add</a>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Employee Type</th>
                                            <td>{{ $employee->employee_type }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="{{ route('account.employees.index') }}" class="btn btn-secondary">Back to List</a>
            </div>
        </div>
    </div>
</div>
@endsection