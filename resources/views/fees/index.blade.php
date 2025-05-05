@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-semibold">Account Management</h1>
    <p class="mt-4">Manage your financial records, fees, and payments here.</p>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
        <!-- Fee Structure Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Fee Structure</h2>
            <a href="{{ route('fee-structures.index') }}" class="text-blue-600 hover:text-blue-800">
                Manage Fee Structures →
            </a>
        </div>

        <!-- Student Fees Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Student Fees</h2>
            <a href="{{ route('student-fee.index') }}" class="text-blue-600 hover:text-blue-800">
                Manage Student Fees →
            </a>
        </div>

        <!-- Payment History Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Payment History</h2>
            <a href="{{ route('fees.history') }}" class="text-blue-600 hover:text-blue-800">
                View Payment History →
            </a>
        </div>
    </div>

    @can('manage fees')
    <div class="mt-8">
        <h2 class="text-2xl font-semibold mb-4">Quick Actions</h2>
        <div class="flex space-x-4">
            <a href="{{ route('fee-structures.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Fee Structure
            </a>
            <a href="{{ route('fees.create') }}" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Record Fee Payment
            </a>
        </div>
    </div>
    @endcan
</div>
@endsection