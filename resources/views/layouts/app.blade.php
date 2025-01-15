<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div id="app">
        <!-- Sidebar -->
        <aside class="w-64 bg-teal-600 text-white fixed h-full">
            <div class="p-4 flex items-center space-x-4">
                <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                <div>
                    <p class="font-semibold">Dashboard</p>
                   
                </div>
            </div>
            <nav class="mt-6">
                <ul>
                    <li>
                        <a href="/dashboard" class="block px-4 py-2 hover:bg-teal-700">Dashboard</a>
                    </li>
                    <li>
                        <div class="px-4 py-2 hover:bg-teal-700 cursor-pointer">User Management</div>
                        <ul class="pl-4">
                            <li><a href="{{ route('permissions.index') }}"
                                    class="block px-4 py-2 hover:bg-teal-700">Permission</a></li>
                            <li><a href="{{ route('roles.index') }}"
                                    class="block px-4 py-2 hover:bg-teal-700">Roles</a></li>
                            <li><a href="{{ route('users.index') }}"
                                    class="block px-4 py-2 hover:bg-teal-700">Users</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#" class="block px-4 py-2 hover:bg-teal-700">Account</a>
                    </li>
                    <li>
                        <a href="#" class="block px-4 py-2 hover:bg-teal-700">Exam</a>
                    </li>
                    <li>
                        <a href="{{ route('profile')}}" class="block px-4 py-2 hover:bg-teal-700">Profile</a>
                    </li>
                </ul>
            </nav>
            <div class="p-4">
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="block text-center px-4 py-2 bg-red-500 hover:bg-red-600 rounded">Logout</button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="ml-64 fixed inset-0 h-full">
            <div class="flex flex-col h-full overflow-y-auto">
                <!-- Header -->
                <header class="bg-white shadow p-4 flex items-center justify-between">
                    <h1 class="text-lg font-semibold text-gray-700">BAJRA</h1>
                    <div class="flex items-center space-x-4">
                        <button class="bg-gray-200 p-2 rounded hover:bg-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M3 6h18M3 14h18" />
                            </svg>
                        </button>
                        <button class="bg-gray-200 p-2 rounded hover:bg-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6h18M3 12h18m-6 6h6" />
                            </svg>
                        </button>
                    </div>
                </header>

                <!-- Blade Content Section -->
                <main class="py-4 px-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>
</body>

</html>
