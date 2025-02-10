@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Salary Increments</h2>
            <a href="{{ route('account.salary_management.salary_increment.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Increment
            </a>
        </div>

        <div class="mb-6 flex justify-between items-center">
            <div class="flex space-x-4">
                <select class="border rounded px-4 py-2" wire:model="department">
                    <option value="">All Departments</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                </select>
                <select class="border rounded px-4 py-2" wire:model="status">
                    <option value="">All Status</option>
                    <option value="Approved">Approved</option>
                    <option value="Pending">Pending</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
        </div>

        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-6 py-3 font-medium text-gray-500">Employee ID</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Employee Name</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Department</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Current Salary</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Increment Amount</th>
                    <th class="px-6 py-3 font-medium text-gray-500">New Salary</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Effective Date</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($salaryIncrements as $increment)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $increment->employee->employee_id }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm">{{ $increment->employee->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $increment->employee->department }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format($increment->current_salary, 2) }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format($increment->increment_amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format($increment->new_salary, 2) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $increment->effective_date->format('d M Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs font-semibold leading-5 rounded-full 
                            {{ $increment->status == 'Approved' ? 'bg-green-100 text-green-800' : 
                               ($increment->status == 'Rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ $increment->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium flex space-x-3">
                        <a href="{{ route('account.salary_management.salary_increment.edit', $increment->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </a>
                        <form action="{{ route('account.salary_management.salary_increment.destroy', $increment->id) }}" method="POST" 
                            onsubmit="return confirm('Are you sure you want to delete this increment record?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">No salary increment records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $salaryIncrements->links() }}
    </div>
</div>
@endsection
