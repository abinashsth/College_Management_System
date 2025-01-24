@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Class Results</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($classes as $class)
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-700">{{ $class->class_name }}</h3>
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                        Section {{ $class->section }}
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Students:</span>
                        <span class="font-medium">{{ $class->students->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Exams:</span>
                        <span class="font-medium">{{ $class->exams->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Average Performance:</span>
                        <span class="font-medium">
                            @php
                                $avgPerformance = $class->students->flatMap->exams->avg('pivot.grade');
                            @endphp
                            {{ number_format($avgPerformance, 2) }}%
                        </span>
                    </div>
                </div>

                <div class="mt-6">
                    <button onclick="showClassDetails({{ $class->id }})"
                            class="w-full bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-200">
                        View Detailed Report
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Modal for class details -->
<div id="classModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-xl font-semibold text-gray-700" id="modalTitle">Class Performance Report</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalContent" class="mt-4"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showClassDetails(classId) {
    fetch(`/api/results/class/${classId}`)
        .then(response => response.json())
        .then(data => {
            const modalContent = document.getElementById('modalContent');
            let html = `
                <div class="mb-6">
                    <h4 class="text-lg font-semibold mb-4">${data.class} - Section ${data.section}</h4>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    ${data.results[0]?.exams.map(exam => `
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            ${exam.exam}<br>
                                            <span class="text-gray-400">${exam.subject}</span>
                                        </th>
                                    `).join('')}
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                ${data.results.map(student => `
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">${student.student_name}</td>
                                        ${student.exams.map(exam => `
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="${exam.status === 'Pass' ? 'text-green-600' : 'text-red-600'}">
                                                    ${exam.grade}
                                                </span>
                                            </td>
                                        `).join('')}
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            modalContent.innerHTML = html;
            document.getElementById('classModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('classModal').classList.add('hidden');
}
</script>
@endpush
