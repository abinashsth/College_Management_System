<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verify Marks') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold">Verify Marks for {{ $exam->title }}</h3>
                            <p class="text-sm text-gray-600">Subject: {{ $subject->name }} ({{ $subject->code }})</p>
                            <p class="text-sm text-gray-600">Status: Submitted for verification</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('marks.index', ['exam_id' => $exam->id, 'subject_id' => $subject->id]) }}" 
                               class="px-4 py-2 bg-gray-600 text-white rounded-md text-sm hover:bg-gray-700">
                                Back to Marks List
                            </a>
                            <a href="{{ route('marks.export', ['exam_id' => $exam->id, 'subject_id' => $subject->id, 'format' => 'xlsx']) }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">
                                Export to Excel
                            </a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="bg-blue-50 p-4 rounded-md mb-6 border border-blue-200">
                        <h4 class="font-medium text-blue-800">Verification Information</h4>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li><strong>Submitted by:</strong> {{ $submittedBy->name }} on {{ $submittedAt->format('M d, Y h:i A') }}</li>
                            <li><strong>Total Students:</strong> {{ $marks->count() }}</li>
                            <li><strong>Pass rate:</strong> {{ $passRate }}% ({{ $passCount }} students passed)</li>
                            <li><strong>Average Score:</strong> {{ $averageScore }}</li>
                        </ul>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($marks as $mark)
                                    <tr class="{{ $mark->isPassing() ? 'bg-green-50' : 'bg-red-50' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $mark->student->roll_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mark->student->user->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($mark->is_absent)
                                                <span class="text-gray-500">Absent</span>
                                            @else
                                                {{ $mark->marks_obtained }} / {{ $exam->total_marks }} 
                                                ({{ number_format(($mark->marks_obtained / $exam->total_marks) * 100, 1) }}%)
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mark->grade ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if ($mark->is_absent)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Absent</span>
                                            @elseif ($mark->isPassing())
                                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Passed</span>
                                            @else
                                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Failed</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mark->remarks ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No marks found for verification.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end mt-6 space-x-2">
                        <form method="POST" action="{{ route('marks.rejectVerification') }}" class="inline">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                Reject & Return to Teacher
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('marks.verifyAll') }}" class="inline">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                            <input type="hidden" name="subject_id" value="{{ $subject->id }}">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Verify All Marks
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 