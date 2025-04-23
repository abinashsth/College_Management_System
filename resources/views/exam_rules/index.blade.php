@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Exam Rules Management</h1>
        @if(auth()->user()->checkPermission('manage exam rules'))
        <a href="{{ route('exam-rules.create') }}" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
            <i class="fas fa-plus mr-1"></i> Create New Rule
        </a>
        @endif
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h2 class="text-lg font-medium mb-4">Filters</h2>
        <form action="{{ route('exam-rules.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                <select id="exam_id" name="exam_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Exams</option>
                    @foreach($exams as $exam)
                    <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                        {{ $exam->title }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="category" name="category" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Categories</option>
                    @foreach($categories as $key => $value)
                    <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="is_global" class="block text-sm font-medium text-gray-700 mb-1">Rule Type</label>
                <select id="is_global" name="is_global" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Types</option>
                    <option value="yes" {{ request('is_global') == 'yes' ? 'selected' : '' }}>Global Rules</option>
                    <option value="no" {{ request('is_global') == 'no' ? 'selected' : '' }}>Exam-Specific Rules</option>
                </select>
            </div>
            
            <div>
                <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="is_active" name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Status</option>
                    <option value="yes" {{ request('is_active') == 'yes' ? 'selected' : '' }}>Active</option>
                    <option value="no" {{ request('is_active') == 'no' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>
                <a href="{{ route('exam-rules.index') }}" class="ml-2 bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Rules List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Exam
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Category
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($rules as $rule)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $rule->title }}</div>
                        <div class="text-sm text-gray-500">Order: {{ $rule->display_order }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($rule->is_global)
                                <span class="italic">Global Rule</span>
                            @else
                                {{ $rule->exam->title ?? 'N/A' }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $categories[$rule->category] ?? 'Other' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->is_mandatory ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $rule->is_mandatory ? 'Mandatory' : 'Advisory' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $rule->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('exam-rules.show', ['exam_rule' => $rule]) }}" class="text-[#37a2bc] hover:text-[#2c8ca3] mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        @if(auth()->user()->checkPermission('manage exam rules'))
                        <a href="{{ route('exam-rules.edit', ['exam_rule' => $rule]) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <form action="{{ route('exam-rules.toggle-status', ['exam_rule' => $rule]) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="{{ $rule->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }} mr-3" title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas {{ $rule->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                        
                        <form action="{{ route('exam-rules.destroy', ['exam_rule' => $rule]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        No exam rules found. 
                        @if(auth()->user()->checkPermission('manage exam rules'))
                        <a href="{{ route('exam-rules.create') }}" class="text-[#37a2bc] hover:text-[#2c8ca3]">Create a new rule</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $rules->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any needed functionality
    });
</script>
@endpush