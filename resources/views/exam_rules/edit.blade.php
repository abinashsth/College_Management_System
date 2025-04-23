@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold">Edit Exam Rule</h1>
            <p class="text-gray-600 mt-1">Modifying rule: {{ $exam_rule->title }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('exam-rules.show', $exam_rule) }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition-colors">
                <i class="fas fa-eye mr-1"></i> View Details
            </a>
            <a href="{{ route('exam-rules.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <form action="{{ route('exam-rules.update', $exam_rule) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Rule Title <span class="text-red-600">*</span></label>
                    <input type="text" id="title" name="title" value="{{ old('title', $exam_rule->title) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_global" name="is_global" value="1" {{ old('is_global', $exam_rule->is_global) ? 'checked' : '' }} class="rounded border-gray-300 text-[#37a2bc] focus:ring-[#37a2bc]">
                        <label for="is_global" class="ml-2 text-sm font-medium text-gray-700">Global Rule (applies to all exams)</label>
                    </div>
                    @error('is_global')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div id="exam_selection">
                    <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Applicable Exam <span class="text-red-600">*</span></label>
                    <select id="exam_id" name="exam_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                        <option value="">Select Exam</option>
                        @foreach($exams as $examOption)
                        <option value="{{ $examOption->id }}" {{ old('exam_id', $exam_rule->exam_id) == $examOption->id ? 'selected' : '' }}>
                            {{ $examOption->title }} ({{ $examOption->class->name ?? 'N/A' }} - {{ $examOption->subject->name ?? 'N/A' }})
                        </option>
                        @endforeach
                    </select>
                    @error('exam_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-600">*</span></label>
                    <select id="category" name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $key => $value)
                        <option value="{{ $key }}" {{ old('category', $exam_rule->category) == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="display_order" class="block text-sm font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" id="display_order" name="display_order" value="{{ old('display_order', $exam_rule->display_order) }}" min="1" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    @error('display_order')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_mandatory" name="is_mandatory" value="1" {{ old('is_mandatory', $exam_rule->is_mandatory) ? 'checked' : '' }} class="rounded border-gray-300 text-[#37a2bc] focus:ring-[#37a2bc]">
                        <label for="is_mandatory" class="ml-2 text-sm font-medium text-gray-700">Mandatory Rule</label>
                    </div>
                    <p class="text-gray-500 text-xs">Mandatory rules must be followed; violations may result in penalties</p>
                    @error('is_mandatory')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $exam_rule->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-[#37a2bc] focus:ring-[#37a2bc]">
                        <label for="is_active" class="ml-2 text-sm font-medium text-gray-700">Active</label>
                    </div>
                    @error('is_active')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-600">*</span></label>
                    <textarea id="description" name="description" rows="5" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50" required>{{ old('description', $exam_rule->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-2">
                    <label for="penalty_for_violation" class="block text-sm font-medium text-gray-700 mb-1">Penalty for Violation</label>
                    <textarea id="penalty_for_violation" name="penalty_for_violation" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">{{ old('penalty_for_violation', $exam_rule->penalty_for_violation) }}</textarea>
                    <p class="text-gray-500 text-xs mt-1">Describe the consequences for violating this rule (required for mandatory rules)</p>
                    @error('penalty_for_violation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-[#37a2bc] text-white py-2 px-6 rounded hover:bg-[#2c8ca3] transition-colors">
                    <i class="fas fa-save mr-1"></i> Update Rule
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const globalCheckbox = document.getElementById('is_global');
        const examSelection = document.getElementById('exam_selection');
        const examIdField = document.getElementById('exam_id');
        
        function toggleExamField() {
            if (globalCheckbox.checked) {
                examSelection.classList.add('opacity-50', 'pointer-events-none');
                examIdField.removeAttribute('required');
            } else {
                examSelection.classList.remove('opacity-50', 'pointer-events-none');
                examIdField.setAttribute('required', 'required');
            }
        }
        
        // Initial state
        toggleExamField();
        
        // On change
        globalCheckbox.addEventListener('change', toggleExamField);
        
        // For mandatory rules, require penalty
        const mandatoryCheckbox = document.getElementById('is_mandatory');
        const penaltyField = document.getElementById('penalty_for_violation');
        
        function togglePenaltyField() {
            if (mandatoryCheckbox.checked) {
                penaltyField.setAttribute('required', 'required');
            } else {
                penaltyField.removeAttribute('required');
            }
        }
        
        // Initial state
        togglePenaltyField();
        
        // On change
        mandatoryCheckbox.addEventListener('change', togglePenaltyField);
    });
</script>
@endpush