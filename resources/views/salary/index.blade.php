@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Salary Management</h1>
        <a href="{{ route('salary.create') }}" class="bg-teal-600 text-white px-4 py-2 rounded-md hover:bg-teal-700">
            Add New Salary Record
        </a>
    </div>

    <!-- Salary Table -->
    <div class="overflow-x-auto bg-white rounded shadow-md">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border-b py-3 px-4">Employee Name</th>
                    <th class="border-b py-3 px-4">Basic Salary</th>
                    <th class="border-b py-3 px-4">Allowances</th>
                    <th class="border-b py-3 px-4">Deductions</th>
                    <th class="border-b py-3 px-4">Net Salary</th>
                    <th class="border-b py-3 px-4">Payment Date</th>
                    <th class="border-b py-3 px-4">Payment Status</th>
                    <th class="border-b py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($salaries as $salary)
                <tr class="hover:bg-gray-50">
                    <td class="border-b py-3 px-4">{{ $salary->employee->name }}</td>
                    <td class="border-b py-3 px-4">{{ number_format($salary->basic_salary, 2) }}</td>
                    <td class="border-b py-3 px-4">{{ number_format($salary->allowances, 2) }}</td>
                    <td class="border-b py-3 px-4">{{ number_format($salary->deductions, 2) }}</td>
                    <td class="border-b py-3 px-4">{{ number_format($salary->net_salary, 2) }}</td>
                    <td class="border-b py-3 px-4">{{ $salary->payment_date }}</td>
                    <td class="border-b py-3 px-4">
                        @if ($salary->is_paid)
                            <span class="bg-green-500 text-white px-2 py-1 rounded">Paid</span>
                        @else
                            <span class="bg-yellow-500 text-white px-2 py-1 rounded">Pending</span>
                        @endif
                    </td>
                    <td class="border-b py-3 px-4 flex space-x-2">
                        <a href="{{ route('salary.show', $salary->id) }}" 
                           class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                            View
                        </a>
                        <a href="{{ route('salary.edit', $salary->id) }}" 
                           class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600">
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-gray-500">No salary records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection