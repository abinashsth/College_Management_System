@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold mb-2">Class Marksheet</h1>
            <p class="text-gray-600"><span class="font-semibold">Class:</span> {{ $classMarksheet['class_name'] }}</p>
        </div>

        <div class="overflow-x-auto">
            @foreach($classMarksheet['students'] as $student)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold mb-3">
                        {{ $student['student_name'] }} (Roll No: {{ $student['roll_number'] }})
                    </h3>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-3 px-4 border-b text-left">Subject</th>
                                <th class="py-3 px-4 border-b text-center">Marks Obtained</th>
                                <th class="py-3 px-4 border-b text-center">Total Marks</th>
                                <th class="py-3 px-4 border-b text-center">Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalMarks = 0;
                                $totalObtained = 0;
                            @endphp

                            @foreach($student['subjects'] as $subject)
                                @php
                                    $totalMarks += $subject['total_marks'];
                                    $totalObtained += $subject['marks'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="py-2 px-4 border-b">{{ $subject['subject_name'] }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $subject['marks'] }}</td>
                                    <td class="py-2 px-4 border-b text-center">{{ $subject['total_marks'] }}</td>
                                    <td class="py-2 px-4 border-b text-center">
                                        {{ number_format($subject['percentage'], 2) }}%
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="bg-gray-100 font-semibold">
                                <td class="py-3 px-4 border-b">Total</td>
                                <td class="py-3 px-4 border-b text-center">{{ $totalObtained }}</td>
                                <td class="py-3 px-4 border-b text-center">{{ $totalMarks }}</td>
                                <td class="py-3 px-4 border-b text-center">
                                    {{ $totalMarks > 0 ? number_format(($totalObtained / $totalMarks) * 100, 2) : 0 }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm text-gray-600">Date: {{ now()->format('d/m/Y') }}</p>
                </div>
                <div class="text-right">
                    <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Print Marksheet
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
        }
        button {
            display: none;
        }
    }
</style>
@endpush
@endsection
