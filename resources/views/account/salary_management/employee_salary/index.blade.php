@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Employee Salary Management</h2>
        <a href="{{ route('account.salary_management.employee_salary.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Salary Record
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-500">Total Employees</div>
            <div class="text-2xl font-bold">{{ $employees->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-500">Total Salary Paid</div>
            <div class="text-2xl font-bold">&#8377;{{ number_format($totalSalary ?? 0, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-500">Average Salary</div>
            <div class="text-2xl font-bold">&#8377;{{ number_format($averageSalary ?? 0, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center">
            <div class="text-gray-500">Pending Payments</div>
            <div class="text-2xl font-bold">{{ $pendingPayments ?? 0 }}</div>
        </div>
    </div>

    <!-- Salary Records Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 flex justify-between items-center">
            <input type="text" placeholder="Search employees..." class="border rounded px-4 py-2 w-64" wire:model="search">
            <div class="flex space-x-4">
                <select class="border rounded px-4 py-2" wire:model="department">
                    <option value="">All Departments</option>
                    <option value="IT">IT</option>
                    <option value="HR">HR</option>
                    <option value="Finance">Finance</option>
                </select>
                <select class="border rounded px-4 py-2" wire:model="status">
                    <option value="">All Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select>
            </div>
        </div>

        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-6 py-3 font-medium text-gray-500">Employee ID</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Employee Name</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Department</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Basic Salary</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Allowances</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Deductions</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Net Salary</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($employeeSalaries as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $employee->employee->employee_id }}</div>
                       
                      
                    </td>
                    <td class="px-6 py-4 text-sm">{{  $employee->employee->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $employee->employee->department }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format($employee->basic_salary ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format($employee->allowances ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format($employee->deductions ?? 0, 2) }}</td>
                    <td class="px-6 py-4 text-sm">&#8377;{{ number_format(($employee->basic_salary + $employee->allowances - $employee->deductions) ?? 0, 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs font-semibold leading-5 rounded-full {{ $employee->status == 'Paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $employee->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium flex space-x-3">
                        <a href="{{ route('account.salary_management.employee_salary.edit', $employee->id) }}" class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </a>
                      

                        <form action="{{ route('account.salary_management.employee_salary.destroy', $employee->id) }}" method="POST" 
                            onsubmit="return confirm('Are you sure you want to delete this employee?');" class="inline">
                          @csrf
                          @method('DELETE')
                          <button type="submit" 
                                  class="text-red-600 hover:text-red-900">
                              Delete
                          </button>
                      </form>


                    </td>
                </tr>


                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">No salary records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $employeeSalaries->links() }}
    </div>
</div>
@endsection
