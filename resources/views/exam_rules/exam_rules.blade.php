@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold">Rules for {{ $exam->title }}</h1>
            <p class="text-gray-600 mt-1">
                {{ $exam->class->name ?? 'N/A' }} | 
                {{ $exam->subject->name ?? 'N/A' }} | 
                {{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'TBD' }}
            </p>
        </div>
        <div class="flex space-x-2">
            @if(auth()->user()->checkPermission('manage exam rules'))
            <a href="{{ route('exam-rules.create', ['exam_id' => $exam->id]) }}" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
                <i class="fas fa-plus mr-1"></i> Add Rule
            </a>
            @endif
            <a href="{{ route('exams.show', ['exam' => $exam]) }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to Exam
            </a>
        </div>
    </div>

    <!-- Rules List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="flex p-4 bg-gray-50 border-b">
            <div class="flex-1">
                <h2 class="text-lg font-medium">Exam Rules</h2>
                <p class="text-sm text-gray-500">Showing both exam-specific and global rules</p>
            </div>
            <div>
                <form action="{{ route('exam.rules', ['exam' => $exam]) }}" method="GET" class="flex items-center space-x-2">
                    <select name="category" class="rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50 text-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $key => $value)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                        @endforeach
                    </select>
                    <button type="submit" class="bg-gray-200 text-gray-700 py-1 px-3 rounded text-sm hover:bg-gray-300 transition-colors">
                        Filter
                    </button>
                    @if(request()->has('category'))
                    <a href="{{ route('exam.rules', ['exam' => $exam]) }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </form>
            </div>
        </div>

        @if($rules->count() > 0)
            <div class="divide-y divide-gray-200" id="rules-container">
                @foreach($rules as $rule)
                <div class="p-5 hover:bg-gray-50 transition-colors{{ $rule->is_global ? ' bg-purple-50' : '' }}" data-rule-id="{{ $rule->id }}" data-order="{{ $rule->display_order }}">
                    <div class="flex items-start">
                        @if(auth()->user()->checkPermission('manage exam rules'))
                        <div class="pr-4 cursor-move handle">
                            <i class="fas fa-grip-vertical text-gray-400"></i>
                        </div>
                        @endif
                        
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                                    {{ $rule->title }}
                                    @if($rule->is_global)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Global</span>
                                    @endif
                                    @if($rule->is_mandatory)
                                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Mandatory</span>
                                    @endif
                                </h3>
                                <div class="flex items-center space-x-2 text-sm">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $categories[$rule->category] ?? 'Other' }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-2 text-sm text-gray-700">
                                {!! nl2br(e($rule->description)) !!}
                            </div>
                            
                            @if($rule->penalty_for_violation)
                            <div class="mt-2 p-2 bg-red-50 rounded text-sm text-red-800">
                                <strong>Penalty for Violation:</strong> {!! nl2br(e($rule->penalty_for_violation)) !!}
                            </div>
                            @endif
                            
                            <div class="mt-2 flex justify-between items-center">
                                <div class="text-xs text-gray-500">
                                    Created by: {{ $rule->creator->name ?? 'Unknown' }} | Order: {{ $rule->display_order }}
                                </div>
                                <div class="flex space-x-1">
                                    @if(auth()->user()->checkPermission('manage exam rules'))
                                    <a href="{{ route('exam-rules.edit', ['exam_rule' => $rule]) }}" class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('exam-rules.toggle-status', ['exam_rule' => $rule]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="{{ $rule->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas {{ $rule->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                        </button>
                                    </form>
                                    
                                    @if(!$rule->is_global)
                                    <form action="{{ route('exam-rules.destroy', ['exam_rule' => $rule]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center text-gray-500">
                No rules found for this exam. 
                @if(auth()->user()->checkPermission('manage exam rules'))
                <a href="{{ route('exam-rules.create', ['exam_id' => $exam->id]) }}" class="text-[#37a2bc] hover:text-[#2c8ca3]">Create a new rule</a>
                @endif
            </div>
        @endif
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $rules->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if(auth()->user()->checkPermission('manage exam rules'))
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('rules-container');
        
        if (container) {
            // Initialize sortable
            const sortable = new Sortable(container, {
                handle: '.handle',
                animation: 150,
                onEnd: function(evt) {
                    updateRuleOrder();
                },
            });
            
            function updateRuleOrder() {
                const rules = [];
                document.querySelectorAll('#rules-container > div').forEach((el, index) => {
                    const ruleId = el.getAttribute('data-rule-id');
                    if (ruleId) {
                        rules.push({
                            id: ruleId,
                            order: index + 1
                        });
                        
                        // Update display order text
                        const orderEl = el.querySelector('.text-xs.text-gray-500');
                        if (orderEl) {
                            const text = orderEl.textContent;
                            orderEl.textContent = text.replace(/Order: \d+/, `Order: ${index + 1}`);
                        }
                    }
                });
                
                if (rules.length > 0) {
                    fetch('{{ route("exam-rules.update-order") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ rules: rules })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Order updated successfully');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating order:', error);
                    });
                }
            }
        }
    });
</script>
@endif
@endpush