@extends('layouts.app')

@section('title', 'Manage Subjects')

@section('content')
<div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Manage Subjects</h1>
        <a href="{{ route('subjects.create') }}" class="mt-4 md:mt-0 px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            <i class="fas fa-plus mr-1"></i> Add New Subject
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded relative" role="alert">
            {{ session('success') }}
            <button type="button" class="absolute top-0 right-0 mt-4 mr-4" onclick="this.parentElement.remove()">
                <span class="text-green-700">&times;</span>
            </button>
        </div>
    @endif

    <!-- Search and Filters -->
    <div class="bg-white shadow-md rounded-lg mb-6 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <h6 class="font-bold text-blue-600">Search & Filter</h6>
        </div>
        <div class="p-6">
            <form action="{{ route('subjects.index') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="mb-4">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                               id="search" name="search" placeholder="Name or description" value="{{ request('search') }}">
                    </div>
                    <div class="mb-4">
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Subject Code</label>
                        <input type="text" class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" 
                               id="code" name="code" placeholder="Subject code" value="{{ request('code') }}">
                    </div>
                    <div class="mb-4">
                        <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="level" name="level">
                            <option value="">All Levels</option>
                            <option value="school" {{ request('level') == 'school' ? 'selected' : '' }}>School</option>
                            <option value="college" {{ request('level') == 'college' ? 'selected' : '' }}>College (+2)</option>
                            <option value="bachelor" {{ request('level') == 'bachelor' ? 'selected' : '' }}>Bachelor</option>
                            <option value="master" {{ request('level') == 'master' ? 'selected' : '' }}>Master</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="duration_type" class="block text-sm font-medium text-gray-700 mb-1">Duration Type</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="duration_type" name="duration_type">
                            <option value="">All Types</option>
                            <option value="semester" {{ request('duration_type') == 'semester' ? 'selected' : '' }}>Semester Based</option>
                            <option value="year" {{ request('duration_type') == 'year' ? 'selected' : '' }}>Year Based</option>
                        </select>
                    </div>
                    <div class="mb-4" id="semester-container" {{ request('duration_type') == 'year' ? 'style=display:none' : '' }}>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="semester" name="semester">
                            <option value="">All Semesters</option>
                            <option value="first" {{ request('semester') == 'first' ? 'selected' : '' }}>First</option>
                            <option value="second" {{ request('semester') == 'second' ? 'selected' : '' }}>Second</option>
                            <option value="third" {{ request('semester') == 'third' ? 'selected' : '' }}>Third</option>
                            <option value="fourth" {{ request('semester') == 'fourth' ? 'selected' : '' }}>Fourth</option>
                            <option value="fifth" {{ request('semester') == 'fifth' ? 'selected' : '' }}>Fifth</option>
                            <option value="sixth" {{ request('semester') == 'sixth' ? 'selected' : '' }}>Sixth</option>
                            <option value="seventh" {{ request('semester') == 'seventh' ? 'selected' : '' }}>Seventh</option>
                            <option value="eighth" {{ request('semester') == 'eighth' ? 'selected' : '' }}>Eighth</option>
                            <option value="all" {{ request('semester') == 'all' ? 'selected' : '' }}>All Semesters</option>
                        </select>
                    </div>
                    <div class="mb-4" id="year-container" {{ request('duration_type') != 'year' ? 'style=display:none' : '' }}>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="year" name="year">
                            <option value="">All Years</option>
                            <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>Year 1</option>
                            <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>Year 2</option>
                            <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>Year 3</option>
                            <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>Year 4</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="type" name="type">
                            <option value="">All Types</option>
                            <option value="0" {{ request('type') === '0' ? 'selected' : '' }}>Core</option>
                            <option value="1" {{ request('type') === '1' ? 'selected' : '' }}>Elective</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="program" class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="program" name="program">
                            <option value="">All Programs</option>
                            @foreach($programs as $program)
                                <option value="{{ $program->id }}" {{ request('program') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="credit_hours" class="block text-sm font-medium text-gray-700 mb-1">Credit Hours</label>
                        <select class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" id="credit_hours" name="credit_hours">
                            <option value="">Any</option>
                            @foreach([1, 2, 3, 4, 5, 6] as $creditOption)
                                <option value="{{ $creditOption }}" {{ request('credit_hours') == $creditOption ? 'selected' : '' }}>
                                    {{ $creditOption }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 lg:col-span-4 flex items-end justify-end">
                        <div class="flex space-x-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                <i class="fas fa-search mr-1"></i> Search
                            </button>
                            <a href="{{ route('subjects.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded shadow hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-400 transition">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subjects Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h6 class="font-bold text-blue-600">All Subjects</h6>
            <div class="relative">
                <button onclick="toggleMenu()" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div id="actionMenu" class="hidden absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-xl z-20">
                    <div class="px-4 py-2 text-xs text-gray-500">Subject Actions:</div>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ route('subjects.create') }}">Add New Subject</a>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ route('subjects.import') }}">Import Subjects</a>
                    <a class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" href="{{ route('subjects.export') }}">Export Subjects</a>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level/Semester</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Hours</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($subjects as $subject)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $subject->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $subject->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $subject->department->name ?? 'Not Assigned' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $subject->level ? $subject->level . ' Level' : 'N/A' }}
                                {{ $subject->semester ? '(' . ucfirst($subject->semester) . ' Semester)' : '' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">{{ $subject->credit_hours }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($subject->elective)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Elective</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">Core</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($subject->status == 'active')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('subjects.show', $subject) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('subjects.edit', $subject) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete" 
                                                onclick="return confirm('Are you sure you want to delete this subject?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">No subjects found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $subjects->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleMenu() {
        const menu = document.getElementById('actionMenu');
        menu.classList.toggle('hidden');
    }

    // Hide menu when clicking elsewhere
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('actionMenu');
        const button = event.target.closest('button[onclick="toggleMenu()"]');
        
        if (!button && !menu.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // Toggle between semester and year inputs
    document.addEventListener('DOMContentLoaded', function() {
        const durationType = document.getElementById('duration_type');
        const semesterContainer = document.getElementById('semester-container');
        const yearContainer = document.getElementById('year-container');

        if (durationType) {
            durationType.addEventListener('change', function() {
                if (this.value === 'year') {
                    semesterContainer.style.display = 'none';
                    yearContainer.style.display = 'block';
                } else {
                    semesterContainer.style.display = 'block';
                    yearContainer.style.display = 'none';
                }
            });
        }
    });
</script>
@endpush 