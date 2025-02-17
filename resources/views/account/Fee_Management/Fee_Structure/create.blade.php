 @extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Fee Structure</h1>
        <a href="{{ route('account.fee_management.fee_structure.index') }}" 
           class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition duration-300">
            Back to List
        </a>
    </div>

    <div class="bg-white shadow-md rounded-lg p-6">
        <form action="{{ route('account.fee_management.fee_structure.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Student Selection --}}
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                    <select id="student_id" name="student_id" required 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->name }} (ID: {{ $student->student_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Class Selection --}}
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="class_id" name="class_id" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->class_name }}
                    </option>
                    @endforeach
                </select>
                    @error('class_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Academic Year Selection --}}
                <div>
                    <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                   <input type="text" name="academic_year" id="academic_year" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Academic Year">   

                   
                </div>

                <div id="fee-container" class="md:col-span-2">
                    <div class="fee-row flex items-center gap-4 mb-4">
                        <select name="fee_heads[]" 
                                class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300" 
                                required>
                            <option value="">Select Fee Category</option>
                            @foreach($feeCategories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        
                        <input type="number" 
                               name="amounts[]" 
                               placeholder="Amount" 
                               class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300" 
                               required>
                               
                        <button type="button" 
                                class="remove-btn bg-red-500 hover:bg-red-700 text-white px-3 py-2 rounded-md transition duration-300"
                                onclick="removeFee(this)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-center">
                    <button type="button" 
                            id="addFeeBtn"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-md transition duration-300 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Add New Fee
                    </button>
                </div>

                {{-- Status Selection --}}
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" required 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Amount Display --}}
                <div class="md:col-span-2">
                    <div class="border-t pt-4 mt-4">
                        <p class="text-lg font-semibold text-gray-700">Total Amount: 
                            <span id="totalAmount" class="text-blue-600">₹0.00</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-4">
                
                <button type="reset" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition duration-300 flex items-center hover:scale-105">
                    <span class="mr-2">Reset</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                </button>

                <a href="{{ route('account.fee_management.fee_structure.index') }}"
                <button type="submit" 
                        id="submitBtn"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition duration-300 flex items-center hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        
                    <span class="mr-2">Create Fee Structure</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function handleSubmit(event) {
    event.preventDefault();
    
    // Disable submit button to prevent double submission
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    // submitBtn.classList.add('opacity-50', 'cursor-not-allowed');

    // Validate required fields
    const requiredFields = [
        'student_id',
        'class_name', 
        'academic_year',
        'tuition_fee',
        'admission_fee',
        'exam_fee',
        'status'
    ];

    let isValid = true;
    requiredFields.forEach(field => {
        const input = document.getElementById(field);
        if (!input.value) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });

    // Validate numeric fields are positive
    const numericFields = ['tuition_fee', 'admission_fee', 'exam_fee'];
    numericFields.forEach(field => {
        const input = document.getElementById(field);
            if (parseFloat(input.value) < 0) {
            input.classList.add('border-red-500');
            isValid = false;
        }
    });

    if (!isValid) {
        submitBtn.disabled = false;
        // submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return false;
    }

    // Submit the form
    document.querySelector('form').submit();
    return true;
}

</script>



<style>
.fee-row {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.remove-btn:hover {
    transform: scale(1.1);
}

#addFeeBtn:hover {
    transform: scale(1.05);
}

.form-control:focus {
    box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2);
}

</style>


<script>

document.addEventListener('DOMContentLoaded', function() {
    const feeContainer = document.getElementById('fee-container');
    const totalAmountDisplay = document.getElementById('totalAmount');

    function calculateTotal() {
        const amounts = document.querySelectorAll('input[name="amounts[]"]');
        let total = Array.from(amounts).reduce((sum, input) => {
            return sum + (parseFloat(input.value) || 0);
        }, 0);
        totalAmountDisplay.textContent = '₹' + total.toFixed(2);
        
        // Animate total change
        totalAmountDisplay.classList.add('text-green-600');
        setTimeout(() => {
            totalAmountDisplay.classList.remove('text-green-600');
        }, 300);
    }

    function addFee() {
        const newRow = document.createElement('div');
        newRow.classList.add('fee-row', 'flex', 'items-center', 'gap-4', 'mb-4');
        newRow.innerHTML = `
            <select name="fee_heads[]" 
                    class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300"
                    required>
                <option value="">Select Fee Category</option>
                @foreach($feeCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            <input type="number" 
                   name="amounts[]" 
                   placeholder="Amount" 
                   class="flex-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300"
                   required>
            <button type="button" 
                    class="remove-btn bg-red-500 hover:bg-red-700 text-white px-3 py-2 rounded-md transition duration-300"
                    onclick="removeFee(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        `;
        feeContainer.appendChild(newRow);
        
        // Add event listener to new amount input
        newRow.querySelector('input[name="amounts[]"]').addEventListener('input', calculateTotal);
    }

    function removeFee(button) {
        const row = button.closest('.fee-row');
        row.style.opacity = '0';
        row.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            row.remove();
            calculateTotal();
        }, 300);
    }

    // Add event listeners
    document.getElementById('addFeeBtn').addEventListener('click', addFee);
    document.querySelectorAll('input[name="amounts[]"]').forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Initialize total
    calculateTotal();
    
    // Make removeFee function globally available
    window.removeFee = removeFee;
});
</script>    


@endsection
