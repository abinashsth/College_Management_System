<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Assignments') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Assignment Status Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700">Total</h3>
                        <p class="text-3xl font-bold text-indigo-600">{{ $studentAssignments->count() }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
                        <p class="text-3xl font-bold text-yellow-600">
                            {{ $studentAssignments->where('status', 'assigned')->count() }}
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700">Submitted</h3>
                        <p class="text-3xl font-bold text-green-600">
                            {{ $studentAssignments->whereIn('status', ['submitted', 'late'])->count() }}
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-semibold text-gray-700">Graded</h3>
                        <p class="text-3xl font-bold text-blue-600">
                            {{ $studentAssignments->where('status', 'graded')->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Assignments Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Filter Assignments</h3>
                    <form action="{{ route('student-assignments.index') }}" method="GET" class="flex flex-col sm:flex-row sm:space-x-4 space-y-4 sm:space-y-0">
                        <div class="w-full sm:w-1/3">
                            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Statuses</option>
                                <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Not Submitted</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Submitted Late</option>
                                <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Graded</option>
                                <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned for Revision</option>
                            </select>
                        </div>
                        <div class="w-full sm:w-1/3">
                            <label for="subject_id" class="block text-sm font-medium text-gray-700">Subject</label>
                            <select id="subject_id" name="subject_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                <option value="">All Subjects</option>
                                @php
                                    $subjects = $studentAssignments->map(function($sa) {
                                        return ['id' => $sa->assignment->subject->id ?? null, 'name' => $sa->assignment->subject->name ?? 'N/A'];
                                    })->unique('id')->filter(function($subject) {
                                        return $subject['id'] !== null;
                                    })->sortBy('name');
                                @endphp
                                
                                @foreach($subjects as $subject)
                                <option value="{{ $subject['id'] }}" {{ request('subject_id') == $subject['id'] ? 'selected' : '' }}>
                                    {{ $subject['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="self-end">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        @if($studentAssignments->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assignment</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($studentAssignments as $submission)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $submission->assignment->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $submission->assignment->subject->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 {{ $submission->assignment->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                            {{ $submission->assignment->due_date->format('M d, Y g:i A') }}
                                            @if($submission->assignment->isOverdue() && !$submission->submitted_at)
                                            <span class="text-xs text-red-600 font-semibold">(Overdue)</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $submission->status == 'graded' ? 'bg-green-100 text-green-800' : 
                                               ($submission->status == 'submitted' ? 'bg-blue-100 text-blue-800' : 
                                                ($submission->status == 'late' ? 'bg-yellow-100 text-yellow-800' : 
                                                 ($submission->status == 'returned' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'))) 
                                            }}">
                                            @if($submission->status == 'assigned')
                                                Not Submitted
                                            @else
                                                {{ ucfirst($submission->status) }}
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($submission->score !== null)
                                                {{ $submission->score }} / {{ $submission->assignment->max_score }}
                                                ({{ number_format($submission->getPercentageScore(), 1) }}%)
                                            @else
                                                <span class="text-gray-500">Not graded</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            @if(!$submission->submitted_at)
                                                <a href="{{ route('student-assignments.show', $submission) }}" class="text-blue-600 hover:text-blue-900">Submit</a>
                                            @else
                                                <a href="{{ route('student-assignments.show', $submission) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                
                                                @if(!$submission->graded_at)
                                                <a href="{{ route('student-assignments.edit', $submission) }}" class="text-green-600 hover:text-green-900">Edit</a>
                                                @endif
                                            @endif
                                            
                                            @if($submission->assignment->attachment_path)
                                            <a href="{{ route('assignments.download-attachment', $submission->assignment) }}" class="text-gray-600 hover:text-gray-900">
                                                Download Task
                                            </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <div class="mt-4">
                            {{ $studentAssignments->links() }}
                        </div>
                        @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">You don't have any assignments yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 