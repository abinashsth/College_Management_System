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
                    <select id="class_id" name="class_id" required 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
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
                    <select id="academic_year" name="academic_year" required 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300">
                        <option value="">Select Academic Year</option>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year') == $year->id ? 'selected' : '' }}>
                                {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                    @error('academic_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Fee Inputs --}}
                @php
                    $feeTypes = [
                        'tuition_fee' => 'Tuition Fee',
                        'admission_fee' => 'Admission Fee', 
                        'exam_fee' => 'Exam Fee',
                      
                    ];
                @endphp

                @foreach($feeTypes as $key => $label)
                    <div>
                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">₹</span>
                            <input type="number" 
                                   step="0.01" 
                                   id="{{ $key }}" 
                                   name="{{ $key }}" 
                                   value="{{ old($key) }}"
                                   required 
                                   min="0"
                                   placeholder="Enter amount"
                                   class="w-full pl-8 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-300">
                        </div>
                        @error($key)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach

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

                 

                    <div id="fee-container">
                        <div class="fee-row">
                            <input type="text" name="fee_heads[]" placeholder="Fee Head" class="form-control" required>
                            <input type="number" name="amounts[]" placeholder="Amount" class="form-control" required>
                            <button type="button" class="remove-btn" onclick="removeFee(this)">X</button>
                        </div>
                    </div>

                    <button type="button" id="addFeeBtn" class="btn btn-primary">Add New</button>
                    <br><br>

                    <button type="submit" class="btn btn-success">Submit</button>
                    <a href="{{ route('account.fee_management.fee_structure.index') }}" class="btn btn-danger">Cancel</a>
              


            </div>

            <div class="mt-6 flex justify-end space-x-4">
                <button type="reset" 
                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded transition duration-300 flex items-center">
                    <span class="mr-2">Reset</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition duration-300 flex items-center">
                    <span class="mr-2">Create Fee Structure</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const feeInputs = document.querySelectorAll('input[type="number"]');
    const totalAmountDisplay = document.getElementById('totalAmount');

    function calculateTotal() {
        let total = 0;
        feeInputs.forEach(input => {
            total += parseFloat(input.value || 0);
        });
        totalAmountDisplay.textContent = '₹' + total.toFixed(2);
    }

    feeInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Calculate initial total
    calculateTotal();
});
</script>




<script>
    function addFee() {
        const container = document.getElementById("fee-container");
        const newRow = document.createElement("div");
        newRow.classList.add("fee-row");
        newRow.innerHTML = `
            <input type="text" name="fee_heads[]" placeholder="Fee Head" class="form-control" required>
            <input type="number" name="amounts[]" placeholder="Amount" class="form-control" required>
            <button type="button" class="remove-btn" onclick="removeFee(this)">X</button>
        `;
        container.appendChild(newRow);
    }

    function removeFee(button) {
        button.parentElement.remove();
    }

    document.getElementById("addFeeBtn").addEventListener("click", addFee);
</script>

@endsection
