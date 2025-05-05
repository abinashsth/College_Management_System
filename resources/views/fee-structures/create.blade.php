@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold text-gray-800">Create Fee Structure</h1>
        <a href="{{ route('fee-structures.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to List
        </a>
    </div>

    @if (session('success'))
    <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
        <button type="button" class="float-right text-green-700" onclick="this.parentElement.remove();">&times;</button>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
        <div class="p-6">
            <form action="{{ route('fee-structures.store') }}" method="POST" id="feeStructureForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course</label>
                        <select name="course_id" id="course_id" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('course_id') border-red-500 @enderror" 
                                required>
                            <option value="">Select Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="semester" class="block text-sm font-medium text-gray-700 mb-2">Semester</label>
                        <input type="number" name="semester" id="semester" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('semester') border-red-500 @enderror"
                               value="{{ old('semester') }}" required min="1">
                        @error('semester')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label for="tuition_fee" class="block text-sm font-medium text-gray-700 mb-2">Tuition Fee</label>
                        <input type="number" name="tuition_fee" id="tuition_fee" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 fee-input @error('tuition_fee') border-red-500 @enderror"
                               value="{{ old('tuition_fee') }}" required step="0.01" min="0">
                        @error('tuition_fee')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="development_fee" class="block text-sm font-medium text-gray-700 mb-2">Development Fee</label>
                        <input type="number" name="development_fee" id="development_fee" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 fee-input @error('development_fee') border-red-500 @enderror"
                               value="{{ old('development_fee') }}" required step="0.01" min="0">
                        @error('development_fee')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="other_charges" class="block text-sm font-medium text-gray-700 mb-2">Other Charges</label>
                        <input type="number" name="other_charges" id="other_charges" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 fee-input @error('other_charges') border-red-500 @enderror"
                               value="{{ old('other_charges') }}" required step="0.01" min="0">
                        @error('other_charges')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label for="total_fee" class="block text-sm font-medium text-gray-700 mb-2">Total Fee</label>
                    <input type="text" id="total_fee" 
                           class="w-full rounded-md border-gray-300 bg-gray-50 shadow-sm" 
                           readonly>
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" 
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 @error('description') border-red-500 @enderror"
                              rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300">
                        Create Fee Structure
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const feeInputs = document.querySelectorAll('.fee-input');
    const totalFeeInput = document.getElementById('total_fee');
    const form = document.getElementById('feeStructureForm');

    function calculateTotal() {
        let total = 0;
        feeInputs.forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalFeeInput.value = '₹' + total.toFixed(2);
    }

    feeInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
        
        // Add animation on focus
        input.addEventListener('focus', function() {
            this.classList.add('scale-105');
            this.style.transition = 'all 0.2s ease';
        });
        
        input.addEventListener('blur', function() {
            this.classList.remove('scale-105');
        });
    });

    form.addEventListener('submit', function(e) {
        let isValid = true;
        feeInputs.forEach(input => {
            if (parseFloat(input.value) < 0) {
                isValid = false;
                input.classList.add('border-red-500', 'shake');
                setTimeout(() => input.classList.remove('shake'), 500);
            } else {
                input.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('Please ensure all fee amounts are non-negative.');
        }
    });

    // Initialize total calculation
    calculateTotal();

    // Auto-hide success message
    const successAlert = document.getElementById('success-alert');
    if (successAlert) {
        setTimeout(() => {
            successAlert.classList.add('opacity-0');
            setTimeout(() => successAlert.remove(), 300);
        }, 3000);
    }
});
</script>
@endpush

<style>
/* Animations */
.shake {
    animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
}

@keyframes shake {
    10%, 90% { transform: translate3d(-1px, 0, 0); }
    20%, 80% { transform: translate3d(2px, 0, 0); }
    30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
    40%, 60% { transform: translate3d(4px, 0, 0); }
}

/* Transitions */
.transition-all {
    transition: all 0.3s ease;
}

/* Form Styles */
input:focus, select:focus, textarea:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Responsive Design */
@media (max-width: 640px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection