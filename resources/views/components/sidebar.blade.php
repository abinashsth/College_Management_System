@php
    $user = Auth::user();
@endphp

<aside class="fixed top-0 left-0 h-screen w-64 bg-[#37a2bc] flex flex-col overflow-y-auto custom-scrollbar">
    <!-- Profile Section -->
    <div class="p-4 flex items-center space-x-3">
        <div class="w-12 h-12 rounded-full bg-white overflow-hidden">
            <img src="{{ $user->avatar ?? asset('images/default-avatar.png') }}" alt="Profile"
                class="w-full h-full object-cover">
        </div>
        <div class="text-white">
            <div class="font-bold">{{ $user->name }}</div>
            <div class="text-sm">{{ strtoupper($user->roles->pluck('name')->first() ?? 'USER') }}</div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 mt-6">
        <a href="{{ route('dashboard') }}" class="sidebar-item">
            <i class="fas fa-home sidebar-icon"></i>
            Dashboard
        </a>

        <!-- Academic Structure Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('manage faculty') || $user->checkPermission('manage departments'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('academicStructure')">
                <i class="fas fa-university sidebar-icon"></i>
                Academic Structure
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="academicStructure" class="hidden pl-8">
                @if($user->hasRole('super-admin') || $user->checkPermission('manage faculty'))
                    <a href="{{ route('faculties.index') }}" class="sidebar-item">Faculties</a>
                @endif
                @if($user->hasRole('super-admin') || $user->checkPermission('manage departments'))
                    <a href="{{ route('departments.index') }}" class="sidebar-item">Departments</a>
                @endif
                @if($user->hasRole('super-admin') || $user->checkPermission('manage departments'))
                    <a href="{{ route('programs.index') }}" class="sidebar-item">Programs/Courses</a>
                @endif
            </div>
        @endif

        <!-- Course Management -->
        <!-- @if($user->hasRole('super-admin') || $user->checkPermission('manage programs'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('courseManagement')">
                <i class="fas fa-book-open sidebar-icon"></i>
                Course Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="courseManagement" class="hidden pl-8">
                <a href="{{ route('courses.index') }}" class="sidebar-item">All Courses</a>
                <a href="{{ route('courses.create') }}" class="sidebar-item">Add New Course</a>
            </div>
        @endif -->

        <!-- Subject Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('view subjects'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('subjectManagement')">
                <i class="fas fa-book sidebar-icon"></i>
                Subject Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="subjectManagement" class="hidden pl-8">
                <a href="{{ route('subjects.index') }}" class="sidebar-item">All Subjects</a>
                <a href="{{ route('subjects.create') }}" class="sidebar-item">Add New Subject</a>
            </div>
        @endif

        <!-- Student Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('view students'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('studentManagement')">
                <i class="fas fa-user-graduate sidebar-icon"></i>
                Student Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="studentManagement" class="hidden pl-8">
                <a href="{{ route('students.index') }}" class="sidebar-item">All Students</a>
                <a href="{{ route('student-records.index') }}" class="sidebar-item">Student Records</a>
            </div>
        @endif
        
        <!-- Admission Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('manage students'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('admissionManagement')">
                <i class="fas fa-user-plus sidebar-icon"></i>
                Admission Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="admissionManagement" class="hidden pl-8">
                <a href="{{ route('admissions.index') }}" class="sidebar-item">All Applications</a>
                <a href="{{ route('admissions.create') }}" class="sidebar-item">New Application</a>
                <a href="{{ route('admissions.apply') }}" class="sidebar-item">Online Application Form</a>
            </div>
        @endif
        
        <!-- Class Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('view classes'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('classManagement')">
                <i class="fas fa-chalkboard sidebar-icon"></i>
                Class Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="classManagement" class="hidden pl-8">
                <a href="{{ route('classes.index') }}" class="sidebar-item">All Classes</a>
                <a href="{{ route('classes.create') }}" class="sidebar-item">Add New Class</a>
            </div>
        @endif

        <!-- Exam Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('view exams'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('examManagement')">
                <i class="fas fa-file-alt sidebar-icon"></i>
                Exam Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="examManagement" class="hidden pl-8">
                <a href="{{ route('exams.index') }}" class="sidebar-item">All Exams</a>
                <a href="{{ route('exams.create') }}" class="sidebar-item">Create New Exam</a>
                <a href="{{ route('exam-schedules.index') }}" class="sidebar-item">Exam Schedules</a>
                <a href="{{ route('exam-materials.index') }}" class="sidebar-item">Exam Materials</a>
                <a href="{{ route('exam-rules.index') }}" class="sidebar-item">Exam Rules</a>
                <a href="{{ route('student.grades') }}" class="sidebar-item">Student Grades</a>
                <a href="{{ route('results.index') }}" class="sidebar-item">Results & Analysis</a>
            </div>
        @endif

        <!-- Marks Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('view marks'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('marksManagement')">
                <i class="fas fa-clipboard-check sidebar-icon"></i>
                Marks Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="marksManagement" class="hidden pl-8">
                <a href="{{ route('marks.index') }}" class="sidebar-item">All Marks</a>
                <a href="{{ route('marks.create') }}" class="sidebar-item">Enter Marks</a>
                <a href="{{ route('marks.report') }}" class="sidebar-item">Marks Report</a>
                <a href="{{ route('student.grades') }}" class="sidebar-item">Student Grades</a>
                <a href="{{ route('results.index') }}" class="sidebar-item">Results & Analysis</a>
            </div>
        @endif

        <!-- Finance Management -->
        @if($user->hasRole('super-admin') || $user->hasRole('admin') || $user->checkPermission('view accounts'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('accountManagement')">
                <i class="fas fa-coins sidebar-icon"></i>
                Finance Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="accountManagement" class="hidden pl-8">
                <a href="{{ route('accounts.index') }}" class="sidebar-item">Fee Management</a>
            </div>
        @endif

        <!-- Reports -->
        @if($user->hasRole('super-admin') || $user->hasRole('admin') || $user->checkPermission('view reports'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('reportManagement')">
                <i class="fas fa-chart-bar sidebar-icon"></i>
                Reports
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="reportManagement" class="hidden pl-8">
                <a href="{{ route('reports.index') }}" class="sidebar-item">All Reports</a>
                <a href="{{ route('reports.my-reports') }}" class="sidebar-item">My Reports</a>
                <a href="{{ route('reports.custom') }}" class="sidebar-item">Custom Reports</a>
            </div>
        @endif

        <!-- User Management -->
        @if($user->hasRole('super-admin') || $user->checkPermission('view users'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('userManagement')">
                <i class="fas fa-users sidebar-icon"></i>
                User Management
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="userManagement" class="hidden pl-8">
                <a href="{{ route('permissions.index') }}" class="sidebar-item">Permission</a>
                <a href="{{ route('roles.index') }}" class="sidebar-item">Roles</a>
                <a href="{{ route('users.index') }}" class="sidebar-item">Users</a>
            </div>
        @endif
        
        <!-- Settings -->
        @if($user->hasRole('super-admin') || $user->checkPermission('manage settings'))
            <div class="sidebar-item cursor-pointer" onclick="toggleDropdown('settingsManagement')">
                <i class="fas fa-cog sidebar-icon"></i>
                Settings
                <i class="fas fa-chevron-down ml-auto transform transition-transform"></i>
            </div>
            <div id="settingsManagement" class="hidden pl-8">
                <a href="{{ route('settings.dashboard') }}" class="sidebar-item">Dashboard</a>
                <a href="{{ route('settings.college') }}" class="sidebar-item">College Profile</a>
                <a href="{{ route('settings.academic-structure.index') }}" class="sidebar-item">Academic Structure</a>
                <a href="{{ route('settings.academic-year.index') }}" class="sidebar-item">Academic Years</a>
                <a href="{{ route('settings.system.index') }}" class="sidebar-item">System Settings</a>
                <a href="{{ route('settings.export') }}" class="sidebar-item">Export Configuration</a>
                <a href="{{ route('settings.import') }}" class="sidebar-item">Import Configuration</a>
            </div>
        @endif
        
        @if($user->hasRole('super-admin') || $user->checkPermission('view activity logs'))
            <a href="{{ route('activity-logs.index') }}" class="sidebar-item">
                <i class="fas fa-history sidebar-icon"></i>
                Activity Logs
            </a>
        @endif

        @if($user->hasRole('super-admin'))
            <a href="{{ route('change.password.form') }}" class="sidebar-item">
                <i class="fas fa-key sidebar-icon"></i>
                Change Password
            </a>
        @endif

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