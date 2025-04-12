<header class="bg-white h-16 flex items-center justify-between px-6 shadow fixed top-0 right-0 left-64 z-10">
    <div class="text-xl">{{ strtoupper($title ?? 'DASHBOARD') }}</div>
    <div class="flex items-center space-x-4">
        <input type="text" placeholder="Search..." class="px-4 py-2 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="p-2 rounded-full hover:bg-gray-100">
                <i class="fas fa-bell text-gray-600"></i>
                <span class="absolute top-0 right-0 h-4 w-4 bg-red-500 rounded-full text-white text-xs flex items-center justify-center">3</span>
            </button>
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl py-2 z-50" style="display: none;">
                <div class="px-4 py-2 border-b text-sm font-semibold">Notifications</div>
                <div class="max-h-64 overflow-y-auto">
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 border-b">
                        <p class="font-medium">New Exam Scheduled</p>
                        <p class="text-xs text-gray-500">Final exams will begin next week</p>
                        <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100 border-b">
                        <p class="font-medium">Student Admission</p>
                        <p class="text-xs text-gray-500">5 new students have been admitted</p>
                        <p class="text-xs text-gray-400 mt-1">Yesterday</p>
                    </a>
                    <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">
                        <p class="font-medium">System Update</p>
                        <p class="text-xs text-gray-500">The system will be down for maintenance</p>
                        <p class="text-xs text-gray-400 mt-1">3 days ago</p>
                    </a>
                </div>
                <div class="px-4 py-2 border-t text-sm font-semibold text-center text-blue-500">
                    <a href="#">View All Notifications</a>
                </div>
            </div>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100">
            <i class="fas fa-cog text-gray-600"></i>
        </button>
    </div>
</header>

@pushonce('scripts')
<script>
    // Include AlpineJS for dropdown functionality if not already included
    if (typeof Alpine === 'undefined') {
        document.addEventListener('DOMContentLoaded', function () {
            const alpineScript = document.createElement('script');
            alpineScript.src = 'https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js';
            alpineScript.defer = true;
            document.head.appendChild(alpineScript);
        });
    }
</script>
@endpushonce 