@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Student Result</h1>
        <div class="flex space-x-2">
            <a href="{{ route('results.index', ['exam_id' => $exam->id]) }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Back to Results
            </a>
            @can('export results')
            <a href="{{ route('results.export.pdf', $result) }}" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                Export PDF
            </a>
            @endcan
        </div>
    </div>

    <!-- Student Information -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Student Information</h2>
                <div class="grid grid-cols-1 gap-2">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Name:</div>
                        <div class="text-base">{{ $student->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">ID:</div>
                        <div class="text-base">{{ $student->student_id }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Class & Section:</div>
                        <div class="text-base">
                            {{ $student->section->class->class_name ?? 'N/A' }} - 
                            {{ $student->section->section_name ?? 'N/A' }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Enrollment Status:</div>
                        <div class="text-base">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $student->enrollment_status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Exam Information</h2>
                <div class="grid grid-cols-1 gap-2">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Exam:</div>
                        <div class="text-base">{{ $exam->name }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Subject:</div>
                        <div class="text-base">{{ $exam->subject->name ?? 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Date:</div>
                        <div class="text-base">{{ $exam->exam_date ? $exam->exam_date->format('d M, Y') : 'N/A' }}</div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Status:</div>
                        <div class="text-base">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $exam->status }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Summary -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Result Summary</h2>
        
        <div class="border-b border-gray-200 pb-6 mb-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Marks:</div>
                    <div class="text-xl font-semibold">{{ number_format($result->total_marks, 2) }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Percentage:</div>
                    <div class="text-xl font-semibold">{{ number_format($result->percentage, 2) }}%</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">GPA:</div>
                    <div class="text-xl font-semibold">{{ number_format($result->gpa, 2) }}</div>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Grade:</div>
                    <div class="text-xl font-semibold">{{ $result->grade }}</div>
                </div>
            </div>
        </div>
        
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500">Status:</div>
                <div class="text-lg">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $result->is_passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $result->is_passed ? 'Passed' : 'Failed' }}
                    </span>
                </div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500">Published:</div>
                <div class="text-lg">
                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $result->isPublished() ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                        {{ $result->isPublished() ? 'Yes' : 'No' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Subject Details -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-700">Subject Details</h2>
        </div>
        
        <div class="overflow-x-auto">
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
    </div>

    @if($result->remarks)
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Remarks</h2>
        <div class="text-gray-700 whitespace-pre-line">{{ $result->remarks }}</div>
    </div>
    @endif

    <!-- Result Meta -->
    <div class="bg-white shadow-md rounded-lg p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Result Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-sm font-medium text-gray-500">Calculated By:</div>
                <div class="text-base">{{ $result->calculatedBy->name ?? 'N/A' }}</div>
                <div class="text-xs text-gray-500">{{ $result->created_at ? $result->created_at->format('d M, Y h:i A') : 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500">Verified By:</div>
                <div class="text-base">{{ $result->verifiedBy->name ?? 'Not verified yet' }}</div>
                <div class="text-xs text-gray-500">{{ $result->verified_at ? $result->verified_at->format('d M, Y h:i A') : 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm font-medium text-gray-500">Published On:</div>
                <div class="text-base">{{ $result->published_at ? $result->published_at->format('d M, Y h:i A') : 'Not published yet' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection 