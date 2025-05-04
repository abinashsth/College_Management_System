@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Fee Structure Management</h1>
        <a href="{{ route('fee-structures.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Add New Fee Structure
        </a>
    </div>

    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Tuition Fee</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Development Fee</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Other Charges</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                        <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($feeStructures as $feeStructure)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $feeStructure->course->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $feeStructure->semester }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₹{{ number_format($feeStructure->tuition_fee, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₹{{ number_format($feeStructure->development_fee, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">₹{{ number_format($feeStructure->other_charges, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap font-medium">₹{{ number_format($feeStructure->total_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="{{ route('feestructures.edit', $feeStructure->id) }}" 
                               class="text-yellow-600 hover:text-yellow-900">Edit</a>
                            <form action="{{ route('feestructures.destroy', $feeStructure->id) }}" 
                                  method="POST" 
                                  class="inline-block"
                                  onsubmit="return confirm('Are you sure you want to delete this fee structure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No fee structures found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(method_exists($feeStructures, 'links'))
    <div class="mt-4">
        {{ $feeStructures->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide success message
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.remove();
        }, 3000);
    }

    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseover', e => {
            const tip = document.createElement('div');
            tip.className = 'absolute bg-gray-800 text-white px-2 py-1 rounded text-sm -mt-8';
            tip.textContent = e.target.dataset.tooltip;
            e.target.appendChild(tip);
        });
        
        tooltip.addEventListener('mouseout', e => {
            const tip = e.target.querySelector('div');
            if (tip) tip.remove();
        });
    });
});
</script>
@endpush

<style>
.pagination {
    @apply flex justify-center space-x-2 mt-4;
}

.pagination > * {
    @apply px-3 py-1 rounded;
}

.pagination .active {
    @apply bg-blue-600 text-white;
}

.pagination a {
    @apply text-blue-600 hover:bg-blue-100;
}

@media (max-width: 640px) {
    .table-responsive {
        @apply block w-full overflow-x-auto;
    }
}
</style>
@endsection