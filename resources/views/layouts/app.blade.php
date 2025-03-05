<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BAJRA - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
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

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.sidebar-open {
                margin-left: 16rem;
            }
        }
    </style>
    @stack('styles')
</head>

<body class="bg-gray-100">
    <div x-data="{ sidebarOpen: false }" class="flex">
        <!-- Mobile Sidebar Overlay -->
        <div 
            x-show="sidebarOpen" 
            @click="sidebarOpen = false" 
            class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity lg:hidden">
        </div>

        <!-- Sidebar -->
        <aside 
            :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
            class="fixed top-0 left-0 h-screen w-64 bg-[#37a2bc] flex flex-col overflow-y-auto custom-scrollbar transform lg:translate-x-0 transition-transform duration-200 ease-in-out z-30">
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

                <!-- Academic Management System -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('academicManagement')">
                    <i class="fas fa-graduation-cap sidebar-icon"></i>
                    Academic Management
                    <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                </div>
                <div id="academicManagement" class="hidden pl-8">
                    <a href="{{ route('sessions.index') }}" class="sidebar-item">
                        <i class="fas fa-calendar-alt sidebar-icon"></i>
                        Sessions
                    </a>
                    <a href="{{ route('courses.index') }}" class="sidebar-item">
                        <i class="fas fa-book sidebar-icon"></i>
                        Courses
                    </a>
                    <a href="{{ route('faculties.index') }}" class="sidebar-item">
                        <i class="fas fa-chalkboard-teacher sidebar-icon"></i>
                        Faculty
                    </a>
                    <a href="{{ route('classes.index') }}" class="sidebar-item">
                        <i class="fas fa-chalkboard sidebar-icon"></i>
                        Classes
                    </a>
                </div>

                <!-- Student Management -->
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('studentManagement')">
                        <i class="fas fa-user-graduate sidebar-icon"></i>
                    Student Management
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="studentManagement" class="hidden pl-8">
                    <a href="{{ route('students.index') }}" class="sidebar-item">
                        <i class="fas fa-users sidebar-icon"></i>
                        Students
                    </a>
                    <a href="{{ route('students.create') }}" class="sidebar-item">
                        <i class="fas fa-user-plus sidebar-icon"></i>
                        Add Student
                    </a>
                    </div>

                <!-- Subject Management -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('subjectManagement')">
                    <i class="fas fa-book-open sidebar-icon"></i>
                    Subject Management
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                <div id="subjectManagement" class="hidden pl-8">
                    <a href="{{ route('subjects.index') }}" class="sidebar-item">
                        <i class="fas fa-list sidebar-icon"></i>
                        All Subjects
                    </a>
                    <a href="{{ route('subjects.create') }}" class="sidebar-item">
                        <i class="fas fa-plus sidebar-icon"></i>
                        Add Subject
                    </a>
                </div>

                <!-- Exam Management -->
                    <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('examManagement')">
                        <i class="fas fa-file-alt sidebar-icon"></i>
                        Exam Management
                    <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                    <div id="examManagement" class="hidden pl-8">
                    <a href="{{ route('exams.index') }}" class="sidebar-item">
                        <i class="fas fa-clipboard-list sidebar-icon"></i>
                        Exams
                    </a>
                    </div>

                <!-- Finance Management -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('financeManagement')">
                    <i class="fas fa-money-bill-wave sidebar-icon"></i>
                    Finance Management
                        <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                    </div>
                <div id="financeManagement" class="hidden pl-8">
                    <a href="{{ route('ledgers.index') }}" class="sidebar-item">
                        <i class="fas fa-file-invoice-dollar sidebar-icon"></i>
                        Ledgers
                    </a>
                </div>

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
                            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('salaryManagement')">
                                <i class="fas fa-coins sidebar-icon"></i>
                                Salary Management
                                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                            </div>
                            <div id="salaryManagement" class="hidden pl-8">
                                <a href="{{ route('account.salary_management.employee_salary.index') }}" class="sidebar-item">Employee Salary</a>
                                <a href="{{ route('account.salary_management.salary_increment.index') }}" class="sidebar-item">Salary Increment</a>
                                <a href="{{ route('account.salary_management.salary_component.index') }}" class="sidebar-item">Salary Component</a>
                                <a href="{{ route('account.salary_management.generate_salary.index') }}" class="sidebar-item">Generate Salary Sheet</a>   
                            </div>
                           
                            
                            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('feeManagement')">
                                <i class="fas fa-coins sidebar-icon"></i>
                                Fee Management
                                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
                            </div>
                            <div id="feeManagement" class="hidden pl-8">
                                <a href="{{ route('account.fee_management.fee_structure.index') }}" class="sidebar-item">Fee Structure</a>
                                <a href="{{ route('account.fee_management.fee_category.index') }}" class="sidebar-item">Fee Category</a>
                               
                            </div>


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

        <!-- Main Content -->
        <div 
            :class="{'ml-0': !sidebarOpen, 'ml-64': sidebarOpen}"
            class="flex-1 flex flex-col min-h-screen transition-all duration-200 ease-in-out lg:ml-64">
            <!-- Top Bar -->
            <header class="bg-white h-16 flex items-center justify-between px-4 sm:px-6 shadow fixed top-0 right-0 left-0 lg:left-64 z-10">
                <div class="flex items-center">
                    <!-- Mobile menu button -->
                    <button 
                        @click="sidebarOpen = !sidebarOpen"
                        class="text-gray-500 hover:text-gray-600 lg:hidden">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    <div class="text-xl ml-4">{{ strtoupper($title ?? 'DASHBOARD') }}</div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="relative hidden sm:block">
                        <input type="text" placeholder="SEARCH" 
                            class="w-full px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-search absolute right-3 top-2.5 text-gray-400"></i>
                    </div>
                    <button class="p-2 sm:hidden">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 p-4 sm:p-6 mt-16 bg-gray-100">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');

            // Rotate arrow icon
            const arrow = document.querySelector(`[onclick="toggleDropdown('${id}')"] .fa-chevron-down`);
            if (dropdown.classList.contains('hidden')) {
                arrow.style.transform = 'rotate(0deg)';
            } else {
                arrow.style.transform = 'rotate(180deg)';
            }
        }

        // Setup CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            // Set up Axios CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        });
    </script>
    @stack('scripts')
</body>

</html>