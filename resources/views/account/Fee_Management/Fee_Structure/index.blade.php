@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Fee Structure Management</h1>
        <a href="{{ route('account.fee_management.fee_structure.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Add New Fee Structure
        </a>
    </div>

    <!-- Fee Structure Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="p-4 flex justify-between items-center">
            <input type="text" placeholder="Search fee structures..." class="border rounded px-4 py-2 w-64" wire:model="search">
            <div class="flex space-x-4">
                <select class="border rounded px-4 py-2" wire:model="class">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
                <select class="border rounded px-4 py-2" wire:model="academic_year">
                    <option value="">All Academic Years</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-6 py-3 font-medium text-gray-500">ID</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Student</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Class</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Academic Year</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Tuition Fee</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Admission Fee</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Exam Fee</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Total Fee</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                    <th class="px-6 py-3 font-medium text-gray-500">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($feeStructures as $feeStructure)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm">{{ $feeStructure->id }}</td>
                    <td class="px-6 py-4 text-sm">{{ $feeStructure->student->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $feeStructure->class->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ $feeStructure->academicYear->year }}</td>
                    <td class="px-6 py-4 text-sm">₹{{ number_format($feeStructure->tuition_fee, 2) }}</td>
                    <td class="px-6 py-4 text-sm">₹{{ number_format($feeStructure->admission_fee, 2) }}</td>
                    <td class="px-6 py-4 text-sm">₹{{ number_format($feeStructure->exam_fee, 2) }}</td>
                 
                    <td class="px-6 py-4 text-sm font-semibold">₹{{ number_format(
                        $feeStructure->tuition_fee + 
                        $feeStructure->admission_fee + 
                        $feeStructure->exam_fee 
                        , 2) }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 inline-flex text-xs font-semibold leading-5 rounded-full {{ $feeStructure->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($feeStructure->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium flex space-x-3">
                        <a href="{{ route('account.fee_management.fee_structure.edit', $feeStructure->id) }}" 
                           class="text-indigo-600 hover:text-indigo-900">
                            Edit
                        </a>

                        <form action="{{ route('account.fee_management.fee_structure.destroy', $feeStructure->id) }}" 
                              method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this fee structure?');" 
                              class="inline">
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
                    <td colspan="15" class="px-6 py-4 text-center text-gray-500">No fee structures found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-4 py-3 border-t border-gray-200">
        {{ $feeStructures->links() }}
    </div>
</div>
@endsection
