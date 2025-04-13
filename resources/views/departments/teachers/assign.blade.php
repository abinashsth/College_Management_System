@extends('layouts.app')

@section('title', 'Assign Teachers to ' . $department->name)

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Assign Teachers to {{ $department->name }}</h1>
        <div class="flex space-x-3">
            <a href="{{ route('departments.teachers.index', $department) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-users mr-2"></i> View Department Teachers
            </a>
            <a href="{{ route('departments.show', $department) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i> Back to Department
            </a>
        </div>
    </div>

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

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <div class="mb-6">
            <h2 class="text-lg font-medium text-gray-900 mb-2">Search for Teachers</h2>
            <form method="GET" action="{{ route('departments.teachers.assign', $department) }}" class="flex flex-wrap md:flex-nowrap gap-4">
                <div class="w-full md:flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search by name or email</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <div class="w-full md:w-48">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by role</label>
                    <select name="role" id="role" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">All Roles</option>
                        <option value="teacher" {{ request('role') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    </select>
                </div>
                <div class="w-full md:w-auto flex items-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <form action="{{ route('departments.teachers.store', $department) }}" method="POST" id="assignTeachersForm">
            @csrf
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-2">Available Teachers</h2>
                <p class="text-gray-600 mb-4">Select teachers to assign to this department. Teachers already assigned to this department are not shown.</p>

                @if(count($availableTeachers) > 0)
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div class="flex items-center">
                                            <input type="checkbox" id="select-all" class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            <label for="select-all" class="ml-2 block text-sm font-medium text-gray-700">Select All</label>
                                        </div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($availableTeachers as $teacher)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="teacher-{{ $teacher->id }}" name="teachers[]" value="{{ $teacher->id }}" 
                                                    class="teacher-checkbox h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full overflow-hidden">
                                                    @if($teacher->profile_photo_path)
                                                        <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $teacher->profile_photo_path) }}" alt="{{ $teacher->name }}">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full flex items-center justify-center bg-blue-100 text-blue-500">
                                                            <i class="fas fa-user"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4">
                                                    <label for="teacher-{{ $teacher->id }}" class="text-sm font-medium text-gray-900 cursor-pointer hover:text-blue-600">
                                                        {{ $teacher->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm text-gray-500">{{ $teacher->email }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($teacher->roles as $role)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $teacher->id }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $availableTeachers->links() }}
                    </div>

                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h3 class="text-md font-medium text-gray-900 mb-2">Assignment Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                <input type="text" name="position" id="position" placeholder="e.g., Professor, Assistant Professor"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-opacity-50">
                                <p class="mt-1 text-xs text-gray-500">Optional: Specify the position of the assigned teachers</p>
                            </div>
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                <input type="date" name="start_date" id="start_date" value="{{ date('Y-m-d') }}"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-opacity-50">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded" id="assignButton" disabled>
                            <i class="fas fa-user-plus mr-2"></i> Assign Selected Teachers
                        </button>
                    </div>
                @else
                    <div class="bg-gray-50 p-6 text-center rounded-lg">
                        <p class="text-gray-500">No available teachers found. All teachers might already be assigned to this department or your search did not match any results.</p>
                        <a href="{{ route('departments.teachers.assign', $department) }}" class="mt-4 inline-block text-blue-600 hover:text-blue-800">
                            <i class="fas fa-sync-alt mr-1"></i> Reset search filters
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all');
        const teacherCheckboxes = document.querySelectorAll('.teacher-checkbox');
        const assignButton = document.getElementById('assignButton');

        // Select all functionality
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                teacherCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
                updateAssignButtonState();
            });
        }

        // Individual checkbox event listeners
        teacherCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateAssignButtonState();
                
                // Update "select all" checkbox
                if (!this.checked) {
                    selectAllCheckbox.checked = false;
                } else {
                    const allChecked = Array.from(teacherCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                }
            });
        });

        // Enable/disable assign button based on selection
        function updateAssignButtonState() {
            const anyChecked = Array.from(teacherCheckboxes).some(checkbox => checkbox.checked);
            assignButton.disabled = !anyChecked;
            
            if (anyChecked) {
                assignButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                assignButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    });
</script>
@endsection 