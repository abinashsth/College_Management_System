@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-semibold">Exam Management</h1>
        @if(auth()->user()->checkPermission('create exams'))
        <a href="{{ route('exams.create') }}" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
            <i class="fas fa-plus mr-1"></i> Create New Exam
        </a>
        @endif
    </div>

    <!-- Filters Section -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <h2 class="text-lg font-medium mb-4">Filters</h2>
        <form action="{{ route('exams.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                <select id="class_id" name="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <select id="subject_id" name="subject_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Subjects</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="academic_session_id" class="block text-sm font-medium text-gray-700 mb-1">Academic Session</label>
                <select id="academic_session_id" name="academic_session_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Sessions</option>
                    @foreach($academicSessions as $session)
                    <option value="{{ $session->id }}" {{ request('academic_session_id') == $session->id ? 'selected' : '' }}>
                        {{ $session->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="exam_type" class="block text-sm font-medium text-gray-700 mb-1">Exam Type</label>
                <select id="exam_type" name="exam_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Types</option>
                    @foreach($examTypes as $key => $value)
                    <option value="{{ $key }}" {{ request('exam_type') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-[#37a2bc] focus:ring focus:ring-[#37a2bc] focus:ring-opacity-50">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-[#37a2bc] text-white py-2 px-4 rounded hover:bg-[#2c8ca3] transition-colors">
                    <i class="fas fa-filter mr-1"></i> Apply Filters
                </button>
                <a href="{{ route('exams.index') }}" class="ml-2 bg-gray-200 text-gray-700 py-2 px-4 rounded hover:bg-gray-300 transition-colors">
                    <i class="fas fa-times mr-1"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Exams List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Title
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Class & Subject
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Date & Time
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
                @forelse($exams as $exam)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $exam->title }}</div>
                        <div class="text-sm text-gray-500">{{ $exam->academicSession->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $exam->class->name ?? 'N/A' }}</div>
                        <div class="text-sm text-gray-500">{{ $exam->subject->name ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $exam->exam_date ? $exam->exam_date->format('M d, Y') : 'N/A' }}</div>
                        <div class="text-sm text-gray-500">
                            {{ $exam->start_time ? $exam->start_time->format('h:i A') : 'N/A' }} - 
                            {{ $exam->end_time ? $exam->end_time->format('h:i A') : 'N/A' }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $examTypes[$exam->exam_type] ?? 'Other' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $exam->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $exam->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $exam->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $exam->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('exams.show', $exam) }}" class="text-[#37a2bc] hover:text-[#2c8ca3] mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        
                        @if(auth()->user()->checkPermission('edit exams'))
                        <a href="{{ route('exams.edit', $exam) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </a>
                        @endif
                        
                        @if(auth()->user()->checkPermission('manage exam schedules'))
                        <a href="{{ route('exam.schedules', $exam) }}" class="text-green-600 hover:text-green-900 mr-3" title="Manage Schedules">
                            <i class="fas fa-calendar-alt"></i>
                        </a>
                        @endif
                        
                        @if(auth()->user()->checkPermission('manage exam rules'))
                        <a href="{{ route('exam.rules', $exam) }}" class="text-yellow-600 hover:text-yellow-900 mr-3" title="Manage Rules">
                            <i class="fas fa-gavel"></i>
                        </a>
                        @endif
                        
                        @if(auth()->user()->checkPermission('manage exam materials'))
                        <a href="{{ route('exam.materials', $exam) }}" class="text-purple-600 hover:text-purple-900 mr-3" title="Manage Materials">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                        @endif
                        
                        @if(auth()->user()->checkPermission('edit exams'))
                        <form action="{{ route('exams.toggle-status', $exam) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="{{ $exam->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }} mr-3" title="{{ $exam->is_active ? 'Deactivate' : 'Activate' }}">
                                <i class="fas {{ $exam->is_active ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                            </button>
                        </form>
                        
                        <form action="{{ route('exams.toggle-published', $exam) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="{{ $exam->is_published ? 'text-gray-600 hover:text-gray-900' : 'text-blue-600 hover:text-blue-900' }}" title="{{ $exam->is_published ? 'Unpublish' : 'Publish' }}">
                                <i class="fas {{ $exam->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                        No exams found. 
                        @if(auth()->user()->checkPermission('create exams'))
                        <a href="{{ route('exams.create') }}" class="text-[#37a2bc] hover:text-[#2c8ca3]">Create a new exam</a>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $exams->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Any JavaScript needed for the exam index page
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize any needed functionality
    });
</script>
@endpush 