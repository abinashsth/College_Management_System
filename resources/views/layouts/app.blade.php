<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BAJRA - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
        <aside class="fixed top-0 left-0 h-screen w-64 bg-[#20B2AA] flex flex-col overflow-y-auto custom-scrollbar">
            <!-- Profile Section -->
            <div class="p-4 flex items-center space-x-3">
                <div class="w-12 h-12 rounded-full bg-white overflow-hidden">
                    <img src="/profile-image.jpg" alt="Profile" class="w-full h-full object-cover">
                </div>
                <div class="text-white">
                    <div class="font-bold">BAJRA</div>
                    <div class="text-sm">ADMINISTRATIVE</div>
                    <div class="text-sm">ADMIN</div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 mt-6">
                <a href="/dashboard" class="sidebar-item">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>

                <!-- Students Management -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('studentManagement')">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Students
                    <svg class="ml-auto w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="studentManagement" class="hidden pl-8">
                    <a href="{{route('students.index')}}" class="sidebar-item">All Students</a>
                    <a href="{{route('students.create')}}" class="sidebar-item">Add Student</a>
                </div>

                <!-- Class Management -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('classManagement')">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Class
                    <svg class="ml-auto w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="classManagement" class="hidden pl-8">
                    <a href="{{route('classes.index')}}" class="sidebar-item">All Classes</a>
                    <a href="{{route('classes.create')}}" class="sidebar-item">Add Class</a>
                </div>

                <!-- Exam Management -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('examManagement')">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Exam
                    <svg class="ml-auto w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="examManagement" class="hidden pl-8">
                    <a href="#" class="sidebar-item">All Exams</a>
                    <a href="#" class="sidebar-item">Create Exam</a>
                    <a href="#" class="sidebar-item">Results</a>
                </div>

                <!-- Account Management -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('accountManagement')">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Account
                    <svg class="ml-auto w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="accountManagement" class="hidden pl-8">
                    <a href="#" class="sidebar-item">Fees</a>
                    <a href="#" class="sidebar-item">Payments</a>
                    <a href="#" class="sidebar-item">Expenses</a>
                </div>

                <!-- User Management (Existing) -->
                <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('userManagement')">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    User management
                    <svg class="ml-auto w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>
                <div id="userManagement" class="hidden pl-8">
                    <a href="{{route('permissions.index')}}" class="sidebar-item">Permission</a>
                    <a href="{{route('roles.index')}}" class="sidebar-item">Roles</a>
                    <a href="{{route('users.index')}}" class="sidebar-item">Users</a>
                </div>

                <a href="{{ route('profile') }}" class="sidebar-item">
                    <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>

                <form action="{{ route('logout') }}" method="POST" class="sidebar-item">
                    @csrf
                    <button type="submit" class="flex items-center">
                        <svg class="sidebar-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </nav>
        </aside>

        <div class="ml-64 flex-1 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <header class="bg-white h-16 flex items-center justify-between px-6 shadow fixed top-0 right-0 left-64 z-10">
                <div class="text-xl">DASHBOARD</div>
                <div class="flex items-center space-x-4">
                    <input type="text" placeholder="SEARCH" class="px-4 py-2 border rounded">
                    <button class="p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
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