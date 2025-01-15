
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
        /* Add custom scrollbar styling */
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
        }
    </script>
</body>
</html>
