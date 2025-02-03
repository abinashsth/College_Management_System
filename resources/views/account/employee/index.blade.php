@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Employees</h1>
        <a href="{{ route('account.employee.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Add Employee
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <!-- Employees Table -->
    <div class="overflow-x-auto bg-white rounded shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b py-3 px-4">Employee ID</th>
                    <th class="border-b py-3 px-4">Name</th>
                    <th class="border-b py-3 px-4">Email</th>
                    <th class="border-b py-3 px-4">Department</th>
                    <th class="border-b py-3 px-4">Designation</th>
                    <th class="border-b py-3 px-4">Contact</th>
                    <th class="border-b py-3 px-4">Status</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($employees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">{{ $employee->id }}</td>
                    <td class="border-b py-3 px-4">{{ $employee->name ?? 'N/A' }}</td>
                    <td class="border-b py-3 px-4">{{ $employee->email ?? 'N/A' }}</td>
                    <td class="border-b py-3 px-4">{{ $employee->department ?? 'N/A' }}</td>
                    <td class="border-b py-3 px-4">{{ $employee->designation ?? 'N/A' }}</td>
                    <td class="border-b py-3 px-4">{{ $employee->contact ?? 'N/A' }}</td>
                     <td class="border-b py-3 px-4">
                        <span class="px-2 py-1 rounded text-sm {{ $employee->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $employee->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="border-b py-3 px-4 flex space-x-2">
                        <a href="{{ route('account.employee.edit', $employee->id) }}" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                            Edit
                        </a>
                        <form action="{{ route('account.employee.destroy', $employee->id) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this employee?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">No employees found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Links -->
    <div class="pagination">
        {{ $employees->links() }}
    </div>
</div>
@endsection