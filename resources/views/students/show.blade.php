@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h5 class="text-xl font-semibold text-blue-600">Student Details</h5>
                    <div class="flex space-x-3">
                        @can('edit students')
                        <a href="{{ route('students.edit', $student) }}" 
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#37a2bc] rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-edit mr-2"></i>
                            Edit
                        </a>
                        @endcan
                        @can('delete students')
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this student?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-trash mr-2"></i>
                                Delete
                            </button>
                        </form>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase">Basic Information</h6>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Student Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->student_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Father's Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->father_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Mother's Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->mother_name ?: 'Not provided' }}</p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Date of Birth:</label>
                                    <p class="text-gray-600">{{ $student->date_of_birth->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Gender</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ ucfirst($student->gender) }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->address }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Phone</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->phone }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Email</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="space-y-6">
                        <div>
                            <h6 class="text-sm font-medium text-gray-500 uppercase">Academic Information</h6>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Admission Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->admission_number }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Roll Number</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->roll_no }}</p>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Admission Date:</label>
                                    <p class="text-gray-600">{{ $student->admission_date->format('F j, Y') }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Class</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->class->class_name }} - {{ $student->class->course->course_name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Session</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $student->session->name }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                @if($student->examResults->isNotEmpty() || $student->gradesheets->isNotEmpty() || $student->ledgers->isNotEmpty())
                <div class="mt-8 space-y-6">
                    <!-- Exam Results -->
                    @if($student->examResults->isNotEmpty())
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase mb-4">Exam Results</h6>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks Obtained</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($student->examResults as $result)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->exam->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $result->marks_obtained }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $result->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($result->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Gradesheets -->
                    @if($student->gradesheets->isNotEmpty())
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase mb-4">Grade Sheets</h6>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Marks</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obtained Marks</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($student->gradesheets as $gradesheet)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $gradesheet->total_marks }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $gradesheet->obtained_marks }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $gradesheet->percentage }}%</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $gradesheet->grade }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    <!-- Ledgers -->
                    @if($student->ledgers->isNotEmpty())
                    <div>
                        <h6 class="text-sm font-medium text-gray-500 uppercase mb-4">Fee Ledger</h6>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($student->ledgers as $ledger)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ledger->fee_type }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ledger->amount }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ledger->paid_amount }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ledger->due_amount }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $ledger->payment_date }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Back Button -->
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <a href="{{ route('students.index') }}" 
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection