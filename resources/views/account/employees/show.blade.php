<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Employee Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('account.employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('account.employees.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                    <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="space-y-6">
                            <div class="flex items-center space-x-4">
                                <div class="h-20 w-20 rounded-full bg-gray-100 flex items-center justify-center overflow-hidden">
                                    @if($employee->avatar)
                                        <img src="{{ Storage::url($employee->avatar) }}" alt="{{ $employee->name }}" class="h-full w-full object-cover">
                                    @else
                                        <span class="text-2xl font-medium text-gray-500">{{ substr($employee->name, 0, 2) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-xl font-medium text-gray-900">{{ $employee->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $employee->employee_id }}</p>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h4>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->email }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->phone }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Employment Details -->
                        <div class="space-y-6">
                            <div class="border-t border-gray-200 pt-4 md:border-t-0 md:pt-0">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Employment Details</h4>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Department</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->department->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Designation</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->designation }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Joining Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->joining_date->format('F d, Y') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <div class="space-y-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Salary Information</h4>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Basic Salary</dt>
                                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($employee->basic_salary, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Allowances</dt>
                                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($employee->allowances, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Deductions</dt>
                                        <dd class="mt-1 text-sm text-gray-900">₹{{ number_format($employee->deductions, 2) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Net Salary</dt>
                                        <dd class="mt-1 text-sm font-medium text-gray-900">₹{{ number_format($employee->net_salary, 2) }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        {{-- <!-- Address Information -->
                        <div class="space-y-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Address Information</h4>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Address</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->address }}</dd>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">City</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->city }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">State</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->state }}</dd>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Postal Code</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->postal_code }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Country</dt>
                                            <dd class="mt-1 text-sm text-gray-900">{{ $employee->country }}</dd>
                                        </div>
                                    </div>
                                </dl>
                            </div>
                        </div> --}}

                        <!-- Emergency Contact -->
                        {{-- <div class="space-y-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="text-lg font-medium text-gray-900 mb-4">Emergency Contact</h4>
                                <dl class="grid grid-cols-1 gap-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergency_contact_phone }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>