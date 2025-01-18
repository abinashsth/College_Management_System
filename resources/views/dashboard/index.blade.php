@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-2 sidebar">
            <div class="position-sticky">
                <div class="user-profile mb-4 text-center">
                    <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" 
                         class="rounded-circle mb-2" alt="Profile" style="width: 60px; height: 60px;">
                    <h6 class="mb-0 text-white">{{ Auth::user()->name }}</h6>
                    <small class="text-white-50">{{ Auth::user()->roles->pluck('name')->first() }}</small>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" 
                           href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-home me-2"></i> Dashboard
                        </a>
                    </li>

                    @can('view users')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('exam*') ? 'active' : '' }}" 
                           href="#examSubmenu" data-bs-toggle="collapse">
                            <i class="fas fa-file-alt me-2"></i> Exam Management
                        </a>
                        <div class="collapse {{ request()->is('exam*') ? 'show' : '' }}" id="examSubmenu">
                            <ul class="nav flex-column ms-3">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('exams.index') }}">
                                        <i class="fas fa-clipboard-list me-2"></i> Subject
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('exams.index') }}">
                                        <i class="fas fa-pen me-2"></i> Exams
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('exams.index') }}">
                                        <i class="fas fa-chart-bar me-2"></i> Grades
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endcan

                    @role('super-admin')
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('change.password.form') }}">
                            <i class="fas fa-lock me-2"></i> Change Password
                        </a>
                    </li>
                    @endrole

                    <li class="nav-item">
                        <a class="nav-link" href="#" 
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-10 ms-sm-auto px-4">
            <div class="content-header d-flex justify-content-between align-items-center py-3">
                <h4 class="m-0">EXAM MANAGEMENT</h4>
                <div>
                    <button class="btn btn-link text-dark">
                        <i class="fas fa-bars"></i>
                    </button>
                    <button class="btn btn-link text-dark">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area bg-white rounded shadow-sm p-4 mt-3">
                @if(count($stats) === 0)
                    <div class="text-center py-5">
                        <h4 class="text-muted mb-3">No Account at this time</h4>
                        <p class="text-muted">Account will appear here after they enroll in your account.</p>
                    </div>
                @else
                    <!-- Your existing content here -->
                @endif
            </div>
        </main>
    </div>
</div>

<style>
.sidebar {
    min-height: 100vh;
    background-color: #37a2bc !important;
    padding: 1.5rem 1rem;
}

.sidebar .nav-link {
    color: white;
    padding: 0.5rem 1rem;
    margin: 0.2rem 0;
    border-radius: 4px;
    font-size: 0.9rem;
}

.sidebar .nav-link:hover {
    background-color: rgba(255,255,255,0.1);
}

.sidebar .nav-link.active {
    background-color: rgba(255,255,255,0.2);
}

.content-area {
    min-height: calc(100vh - 100px);
}

.content-header {
    border-bottom: 1px solid #eee;
}

/* Submenu styles */
.sidebar .collapse .nav-link {
    padding-left: 2.5rem;
    font-size: 0.85rem;
}

/* Remove default collapse animation */
.collapse {
    transition: none;
}

.collapse.show {
    display: block;
}
</style>

<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endsection
