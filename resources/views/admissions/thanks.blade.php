@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">Application Submitted Successfully</h4>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="text-center my-5">
                <i class="fas fa-check-circle text-success" style="font-size: 5rem;"></i>
                <h2 class="mt-4">Thank You for Your Application!</h2>
                <p class="lead">Your application has been successfully submitted and is now being processed.</p>
                <p>Our admissions team will review your application and contact you soon with further information.</p>
                <p>Please check your email regularly for updates regarding your application status.</p>
            </div>
            
            <div class="text-center mt-5">
                <a href="{{ url('/') }}" class="btn btn-primary">Back to Home</a>
            </div>
        </div>
    </div>
</div>
@endsection