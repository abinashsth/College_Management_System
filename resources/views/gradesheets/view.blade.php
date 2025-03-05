<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gradesheet') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8 text-center border-b pb-4">
                        <div class="flex justify-center items-center mb-4">
                            @if($school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="{{ $school->name }} Logo" class="h-20 w-auto mr-4">
                            @endif
                            <div>
                                <h2 class="text-2xl font-bold">{{ $school->name }}</h2>
                                <p class="text-sm">{{ $school->address }}</p>
                                <p class="text-sm">Phone: {{ $school->phone }} | Email: {{ $school->email }}</p>
                            </div>
                        </div>
                        <h3 class="text-xl font-semibold">GRADESHEET</h3>
                        <p class="text-sm">Academic Session: {{ $session->session_name }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Student Information</h4>
                            <div class="mt-2">
                                <p><span class="font-semibold">Name:</span> {{ $student->student_name }}</p>
                                <p><span class="font-semibold">Roll Number:</span> {{ $student->roll_no }}</p>
                                <p><span class="font-semibold">Class:</span> {{ $student->class->class_name }}</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Gradesheet Details</h4>
                            <div class="mt-2">
                                <p><span class="font-semibold">Date:</span> {{ $gradesheet->created_at->format('F d, Y') }}</p>
                                <p><span class="font-semibold">Gradesheet ID:</span> {{ $gradesheet->id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Max Marks</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Marks Obtained</th>
                                    <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examResults as $result)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ $result->subject_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ $result->exam_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ $result->max_marks }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ $result->marks_obtained }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            {{ number_format(($result->marks_obtained / $result->max_marks) * 100, 2) }}%
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50">
                                    <td colspan="2" class="px-6 py-4 font-semibold text-right border-b border-gray-200">Total</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ $gradesheet->total_marks }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ $gradesheet->obtained_marks }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">{{ number_format($gradesheet->percentage, 2) }}%</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Grade</h4>
                            <div class="mt-2">
                                <span class="px-4 py-2 inline-flex text-lg leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $gradesheet->grade }}
                                </span>
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <h4 class="text-sm font-medium text-gray-500">Remarks</h4>
                            <div class="mt-2">
                                <p>{{ $gradesheet->remarks }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-4 border-t flex justify-between">
                        <div>
                            <p class="text-sm font-medium">Student Signature</p>
                            <div class="mt-8 border-t border-gray-300 w-40"></div>
                        </div>
                        <div>
                            <p class="text-sm font-medium">Principal Signature</p>
                            <div class="mt-8 border-t border-gray-300 w-40"></div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-center">
                        <button onclick="window.print()" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            Print Gradesheet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white, .bg-white * {
                visibility: visible;
            }
            .bg-white {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            button {
                display: none;
            }
        }
    </style>
</x-app-layout> 