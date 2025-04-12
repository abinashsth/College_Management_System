@extends('layouts.app')

@section('title', $department->name . ' - Teachers')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">{{ $department->name }} Teachers</h1>
        <div class="flex space-x-3">
            <a href="{{ route('departments.teachers.assign', $department) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                <i class="fas fa-user-plus mr-2"></i> Assign Teachers
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

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Department Faculty Members</h2>
            <div class="flex items-center">
                <form method="GET" action="{{ route('departments.teachers.index', $department) }}" class="flex items-center">
                    <input type="text" name="search" placeholder="Search teachers..." value="{{ request('search') }}"
                        class="border-gray-300 rounded-l-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-md">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            @if(count($teachers) > 0)
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Position
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined On
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($teachers as $teacher)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-gray-100 rounded-full overflow-hidden">
                                            @if($teacher->user->profile_photo_path)
                                                <img class="h-10 w-10 rounded-full" src="{{ asset('storage/' . $teacher->user->profile_photo_path) }}" alt="{{ $teacher->user->name }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full flex items-center justify-center bg-blue-100 text-blue-500">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $teacher->user->name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $teacher->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $teacher->position ?? 'Teacher' }}</div>
                                    @if($department->head && $department->head->user_id === $teacher->user_id)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Department Head
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $teacher->user->phone_number ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $teacher->user->secondary_email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $teacher->start_date ? \Carbon\Carbon::parse($teacher->start_date)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $teacher->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('users.show', $teacher->user_id) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(!($department->head && $department->head->user_id === $teacher->user_id))
                                            <form action="{{ route('departments.teachers.remove', [$department, $teacher]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to remove this teacher from the department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(!$department->head || $department->head->user_id !== $teacher->user_id)
                                            <a href="{{ route('departments.heads.create', ['department' => $department, 'user_id' => $teacher->user_id]) }}" class="text-green-600 hover:text-green-900" title="Assign as Department Head">
                                                <i class="fas fa-user-tie"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="py-8 text-center">
                    <p class="text-gray-500 mb-4">No teachers are currently assigned to this department.</p>
                    <a href="{{ route('departments.teachers.assign', $department) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                        <i class="fas fa-user-plus mr-2"></i> Assign Teachers
                    </a>
                </div>
            @endif
        </div>
        
        @if(count($teachers) > 0)
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
    
    <div class="mt-6 bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h2 class="text-lg font-medium text-gray-900">Teacher Statistics</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-blue-800 text-sm font-medium">Total Teachers</p>
                        <p class="text-blue-900 text-2xl font-bold">{{ $statistics['total'] }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-green-800 text-sm font-medium">Active Teachers</p>
                        <p class="text-green-900 text-2xl font-bold">{{ $statistics['active'] }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-user-check text-green-500 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-purple-800 text-sm font-medium">Avg. Tenure</p>
                        <p class="text-purple-900 text-2xl font-bold">{{ $statistics['avg_tenure'] ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-calendar-alt text-purple-500 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 