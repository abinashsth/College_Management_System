<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'College Management System') }} - {{ $title ?? 'Dashboard' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
    @stack('styles')
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="flex min-h-screen">
        <!-- Sidebar Component -->
        @include('components.sidebar')

        <div class="ml-64 flex-1 flex flex-col min-h-screen">
            <!-- Header Component -->
            @include('components.header', ['title' => $title ?? 'Dashboard'])

            <!-- Main Content -->
            <main class="flex-1 p-6 mt-16 bg-gray-100">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                        <p>{{ session('error') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer Component -->
            @include('components.footer')
        </div>
    </div>

    <script>
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            dropdown.classList.toggle('hidden');

            // Rotate arrow icon
            const arrow = document.querySelector(`[onclick="toggleDropdown('${id}')"] i.fa-chevron-down`);
            if (arrow) {
                if (dropdown.classList.contains('hidden')) {
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        }
    </script>
    @stack('scripts')
</body>

</html>