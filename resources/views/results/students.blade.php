@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <h2 class="text-2xl font-semibold text-gray-700 mb-6">Student Results</h2>

    <div class="bg-white shadow-md rounded my-6">
        <div class="p-4">
            <input type="text" 
                   id="studentSearch" 
                   placeholder="Search student..." 
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-3 px-4 text-left">Student Name</th>
                        <th class="py-3 px-4 text-left">Class</th>
                        <th class="py-3 px-4 text-left">Total Exams</th>
                        <th class="py-3 px-4 text-left">Average Grade</th>
                        <th class="py-3 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600">
                    @foreach($students as $student)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-3 px-4">{{ $student->name }}</td>
                            <td class="py-3 px-4">{{ $student->class->class_name }} - {{ $student->class->section }}</td>
                            <td class="py-3 px-4">{{ $student->exams->count() }}</td>
                            <td class="py-3 px-4">
                                @if($student->exams->count() > 0)
                                    {{ number_format($student->exams->avg('pivot.grade'), 2) }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <button onclick="showResults({{ $student->id }})" 
                                        class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                    View Details
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal for detailed results -->
<div id="resultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-xl font-semibold text-gray-700" id="modalTitle">Student Results</h3>
            <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="modalContent"></div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function showResults(studentId) {
    // Fetch and display student results in modal
    fetch(`/api/results/student/${studentId}`)
        .then(response => response.json())
        .then(data => {
            const modalContent = document.getElementById('modalContent');
            let html = `<div class="space-y-4">`;
            
            data.results.forEach(result => {
                html += `
                    <div class="border-b pb-4">
                        <h4 class="font-semibold">${result.exam}</h4>
                        <div class="grid grid-cols-2 gap-4 mt-2">
                            <div>Subject: ${result.subject}</div>
                            <div>Date: ${result.date}</div>
                            <div>Grade: ${result.grade}/${result.total_marks}</div>
                            <div>Status: <span class="${result.status === 'Pass' ? 'text-green-600' : 'text-red-600'}">${result.status}</span></div>
                        </div>
                        <div class="mt-2">
                            <p class="text-sm text-gray-600">Remarks: ${result.remarks || 'No remarks'}</p>
                        </div>
                    </div>
                `;
            });
            
            html += `</div>`;
            modalContent.innerHTML = html;
            document.getElementById('resultsModal').classList.remove('hidden');
        });
}

function closeModal() {
    document.getElementById('resultsModal').classList.add('hidden');
}

// Search functionality
document.getElementById('studentSearch').addEventListener('keyup', function(e) {
    const searchText = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const studentName = row.cells[0].textContent.toLowerCase();
        const className = row.cells[1].textContent.toLowerCase();
        row.style.display = studentName.includes(searchText) || className.includes(searchText) ? '' : 'none';
    });
});
</script>
@endpush
