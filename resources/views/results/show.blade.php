@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Result Details</h1>
        <div class="flex space-x-2">
            <a href="{{ route('results.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to List
            </a>
            @can('export results')
            <a href="{{ route('results.export.pdf', $result) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Export PDF
            </a>
            @endcan
        </div>
    </div>

    <!-- Result Header -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Student Information</h2>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Name:</div>
                    <div class="text-base">{{ $result->student->name ?? 'N/A' }}</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">ID:</div>
                    <div class="text-base">{{ $result->student->student_id ?? 'N/A' }}</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Class & Section:</div>
                    <div class="text-base">
                        {{ $result->student->section->class->class_name ?? 'N/A' }} - 
                        {{ $result->student->section->section_name ?? 'N/A' }}
                    </div>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Exam Information</h2>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Exam:</div>
                    <div class="text-base">{{ $result->exam->name ?? 'N/A' }}</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Subject:</div>
                    <div class="text-base">{{ $result->exam->subject->name ?? 'N/A' }}</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Date:</div>
                    <div class="text-base">{{ $result->exam->exam_date ? $result->exam->exam_date->format('d M, Y') : 'N/A' }}</div>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-700">Result Summary</h2>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Total Marks:</div>
                    <div class="text-base">{{ number_format($result->total_marks, 2) }}</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Percentage:</div>
                    <div class="text-base">{{ number_format($result->percentage, 2) }}%</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">GPA / Grade:</div>
                    <div class="text-base">{{ number_format($result->gpa, 2) }} ({{ $result->grade }})</div>
                </div>
                <div class="mt-2">
                    <div class="text-sm font-medium text-gray-500">Status:</div>
                    <div class="text-base">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $result->is_passed ? 'Passed' : 'Failed' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="text-sm font-medium text-gray-500">Calculated By:</div>
                    <div class="text-base">{{ $result->calculatedBy->name ?? 'N/A' }}</div>
                    <div class="text-xs text-gray-400">{{ $result->created_at ? $result->created_at->format('d M, Y h:i A') : 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Verified By:</div>
                    <div class="text-base">{{ $result->verifiedBy->name ?? 'Not verified yet' }}</div>
                    <div class="text-xs text-gray-400">{{ $result->verified_at ? $result->verified_at->format('d M, Y h:i A') : 'N/A' }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Published:</div>
                    <div class="text-base">{{ $result->published_at ? 'Yes' : 'No' }}</div>
                    <div class="text-xs text-gray-400">{{ $result->published_at ? $result->published_at->format('d M, Y h:i A') : 'N/A' }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Marks -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Subject-wise Results</h2>
        </div>
        
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Subject
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Marks
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Percentage
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Grade
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Grade Point
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Credit Hours
                    </th>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @if(count($resultDetails) > 0)
                    @foreach($resultDetails as $detail)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $detail->subject->name ?? 'N/A' }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $detail->subject->subject_code ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">
                                {{ number_format($detail->marks_obtained, 2) }} / {{ number_format($detail->total_marks, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">
                                {{ number_format(($detail->marks_obtained / $detail->total_marks) * 100, 2) }}%
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">
                                {{ $detail->grade }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">
                                {{ number_format($detail->grade_point, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="text-sm text-gray-900">
                                {{ number_format($detail->credit_hours, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($detail->is_absent)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Absent
                                </span>
                            @elseif($detail->is_passed)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Passed
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Failed
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No subject details found for this result.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    @if($result->remarks)
    <div class="bg-white shadow-md rounded-lg p-6 mt-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Remarks</h2>
        <div class="text-gray-700 whitespace-pre-line">{{ $result->remarks }}</div>
    </div>
    @endif
</div>
@endsection 