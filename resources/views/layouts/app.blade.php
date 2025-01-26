<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BAJRA - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .sidebar-item {
            padding: 10px 20px;
            display: flex;
            align-items: center;
            color: white;
            transition: all 0.3s;
        }

        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-icon {
            width: 20px;
            height: 20px;
            margin-right: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex">
        <!-- Fixed Sidebar -->
        <aside class="fixed top-0 left-0 h-screen w-64 bg-[#37a2bc] flex flex-col overflow-y-auto custom-scrollbar">
            <!-- Profile Section -->
            <div class="p-4 flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-white overflow-hidden">
                    <img src="{{ Auth::user()->avatar ?? asset('images/default-avatar.png') }}" alt="Profile"
                        class="w-full h-full object-cover">
                </div>
                <div class="text-white">
                    <div class="font-bold">{{ Auth::user()->name }}</div>
                    <div class="text-sm">ADMINISTRATIVE</div>
                    <div class="text-sm">{{ Auth::user()->roles->pluck('name')->first() }}</div>
                </div>
            </div>

            <!-- Navigation -->
            @php
                $user = Auth::user();
            @endphp

            <nav class="flex-1 mt-6">
                <a href="{{ route('dashboard') }}" class="sidebar-item">
                    <i class="fas fa-home sidebar-icon"></i>
                    Dashboard
                </a>

                @if($user->hasRole('super-admin') || $user->checkPermission('view students'))
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('studentManagement')">
                        <i class="fas fa-user-graduate sidebar-icon"></i>
                        Students
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="studentManagement" class="hidden pl-8">
                        <a href="{{ route('students.index') }}" class="sidebar-item">All Students</a>
                        <a href="{{ route('students.create') }}" class="sidebar-item">Add Student</a>
                    </div>
                @endif

                @if($user->hasRole('super-admin') || $user->checkPermission('view classes'))
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('classManagement')">
                        <i class="fas fa-chalkboard sidebar-icon"></i>
                        Class
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="classManagement" class="hidden pl-8">
                        <a href="{{ route('classes.index') }}" class="sidebar-item">All Classes</a>
                        <a href="{{ route('classes.create') }}" class="sidebar-item">Add Class</a>
                    </div>
                @endif

                @if($user->hasRole('super-admin') || $user->hasRole('admin') || $user->checkPermission('view exams'))
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('examManagement')">
                        <i class="fas fa-file-alt sidebar-icon"></i>
                        Exam Management
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="examManagement" class="hidden pl-8">
                        @if($user->hasRole('examiner') || $user->hasRole('super-admin') || $user->hasRole('admin'))
                            <a href="{{ route('subjects.index') }}" class="sidebar-item">Subjects</a>
                            <a href="{{ route('class.add-subjects.form') }}" class="sidebar-item">Add Subjects to Class</a>
                            <a href="{{ route('marks.add.form') }}" class="sidebar-item">Add Marks</a>
                        @endif
                    </div>
                @endif

                @if($user->hasRole('super-admin') || $user->hasRole('admin') || $user->hasRole('examiner') || $user->checkPermission('view results'))
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('resultManagement')">
                        <i class="fas fa-chart-bar sidebar-icon"></i>
                        Result Management
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="resultManagement" class="hidden pl-8">
                        @if($user->hasRole('super-admin') || $user->hasRole('admin'))
                            <a href="{{ route('marksheet.class', ['classId' => 'all']) }}" class="sidebar-item">All Class Results</a>
                        @endif
                        
                        @if($user->hasRole('examiner'))
                            <a href="{{ route('marksheet.class', ['classId' => Auth::user()->class_id]) }}" class="sidebar-item">My Class Results</a>
                        @endif
                        
                        @if($user->hasRole('student'))
                            <a href="{{ route('marksheet.student', ['studentId' => Auth::id()]) }}" class="sidebar-item">View My Results</a>
                        @endif
                    </div>
                @endif

                @if($user->hasRole('super-admin', 'admin') || $user->checkPermission('view accounts'))
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('accountManagement')">
                        <i class="fas fa-coins sidebar-icon"></i>
                        Account
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="accountManagement" class="hidden pl-8">
                        {{-- <a href="{{ route('account.index') }}" class="sidebar-item">Students</a> --}}
                        <a href="{{ route('account.employee.index') }}" class="sidebar-item">Employee</a>
                        <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('payrollManagement')">
                            <i class="fas fa-coins sidebar-icon"></i>
                            Payroll Management
                            <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                        </div>
                        <div id="payrollManagement" class="hidden pl-8">
                            <a href="{{ route('account.employee.index') }}" class="sidebar-item">Salary Management</a>
                            <a href="{{ route('account.employee.index') }}" class="sidebar-item">Fee management</a>
                        </div>

                        
                    </div>

                   

                @endif

                @if($user->hasRole('super-admin') || $user->checkPermission('view users'))
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('userManagement')">
                        <i class="fas fa-users sidebar-icon"></i>
                        User management
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="userManagement" class="hidden pl-8">
                        <a href="{{ route('permissions.index') }}" class="sidebar-item">Permission</a>
                        <a href="{{ route('roles.index') }}" class="sidebar-item">Roles</a>
                        <a href="{{ route('users.index') }}" class="sidebar-item">Users</a>
                    </div>
                @endif

                @role('super-admin')
                <a href="{{ route('change.password.form') }}" class="sidebar-item">
                    <i class="fas fa-key sidebar-icon"></i>
                    Change Password
                </a>
                @endrole

                <a href="{{ route('profile.edit') }}" class="sidebar-item">
                    <i class="fas fa-user sidebar-icon"></i>
                    Profile
                </a>

                <form action="{{ route('logout') }}" method="POST" class="sidebar-item">
                    @csrf
                    <button type="submit" class="flex items-center w-full">
                        <i class="fas fa-sign-out-alt sidebar-icon"></i>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <div class="ml-64 flex-1 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <header
                class="bg-white h-16 flex items-center justify-between px-6 shadow fixed top-0 right-0 left-64 z-10">
                <div class="text-xl">{{ strtoupper($title ?? 'DASHBOARD') }}</div>
                <div class="flex items-center space-x-4">
                    <input type="text" placeholder="SEARCH" class="px-4 py-2 border rounded">
                    <button class="p-2">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-6 mt-16 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');

            // Rotate arrow icon
            const arrow = document.querySelector(`[onclick="toggleDropdown('${id}')"] svg:last-child`);
            if (dropdown.classList.contains('hidden')) {
                arrow.style.transform = 'rotate(0deg)';
            } else {
                arrow.style.transform = 'rotate(180deg)';
            }
        }
    </script>
</body>

</html>