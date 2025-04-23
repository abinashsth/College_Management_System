@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-semibold">Exam Rule Details</h1>
            <p class="text-gray-600 mt-1">Viewing details for the exam rule</p>
        </div>
        <div class="flex space-x-2">
            @if(auth()->user()->checkPermission('manage exam rules'))
            <a href="{{ route('exam-rules.edit', ['exam_rule' => $exam_rule]) }}" class="bg-indigo-600 text-white py-2 px-4 rounded hover:bg-indigo-700 transition-colors">
                <i class="fas fa-edit mr-1"></i> Edit Rule
            </a>
            <form action="{{ route('exam-rules.destroy', ['exam_rule' => $exam_rule]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </form>
            @endif
            <a href="{{ route('exam-rules.index') }}" class="bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Rule Details Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <div class="flex flex-wrap mb-6">
                <div class="w-full lg:w-1/2 mb-4 lg:mb-0">
                    <h2 class="text-2xl font-semibold text-gray-800">{{ $exam_rule->title }}</h2>
                    <div class="mt-2 flex flex-wrap gap-2">
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $exam_rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $exam_rule->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $exam_rule->is_global ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ $exam_rule->is_global ? 'Global Rule' : 'Exam-Specific Rule' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium {{ $exam_rule->is_mandatory ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $exam_rule->is_mandatory ? 'Mandatory' : 'Advisory' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            Category: {{ $categories[$exam_rule->category] ?? 'Other' }}
                        </span>
                    </div>
                </div>
                <div class="w-full lg:w-1/2 lg:text-right">
                    <div class="text-sm text-gray-600">
                        <p><strong>Display Order:</strong> {{ $exam_rule->display_order }}</p>
                        <p><strong>Created By:</strong> {{ $exam_rule->creator->name ?? 'Unknown' }}</p>
                        <p><strong>Created At:</strong> {{ $exam_rule->created_at->format('M d, Y h:i A') }}</p>
                        <p><strong>Last Updated:</strong> {{ $exam_rule->updated_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>
            </div>

            @if(!$exam_rule->is_global)
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-medium text-gray-700 mb-2">Applicable Exam</h3>
                <div class="text-gray-800">
                    <p><strong>Exam:</strong> {{ $exam_rule->exam->title ?? 'N/A' }}</p>
                    @if($exam_rule->exam)
                    <p><strong>Details:</strong> 
                        {{ $exam_rule->exam->class->name ?? 'N/A' }} / 
                        {{ $exam_rule->exam->subject->name ?? 'N/A' }} / 
                        {{ $exam_rule->exam->exam_date ? $exam_rule->exam->exam_date->format('M d, Y') : 'TBD' }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <div class="mb-6">
                <h3 class="font-medium text-gray-700 mb-2">Description</h3>
                <div class="bg-gray-50 p-4 rounded-lg prose max-w-none">
                    {!! nl2br(e($exam_rule->description)) !!}
                </div>
            </div>

            @if($exam_rule->penalty_for_violation)
            <div class="mb-6">
                <h3 class="font-medium text-gray-700 mb-2">Penalty for Violation</h3>
                <div class="bg-red-50 p-4 rounded-lg text-red-800">
                    {!! nl2br(e($exam_rule->penalty_for_violation)) !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection