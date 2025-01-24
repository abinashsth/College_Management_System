@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-700">Create Student Result</h2>
        <a href="{{ route('results.students') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
            Back to Results
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('results.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Class Selection -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select name="class_id" id="class_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }} - {{ $class->section }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Student Selection -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                    <select name="student_id" id="student_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="">Select Student</option>
                    </select>
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-700 mb-4">Subject Marks</h3>
                
                <!-- Mathematics -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">Mathematics</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="mathematics_theory" class="block text-sm text-gray-700">Theory Marks (Max: 50)</label>
                            <input type="number" name="mathematics_theory" id="mathematics_theory" min="0" max="50" step="0.01"
                                   value="{{ old('mathematics_theory') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="mathematics_practical" class="block text-sm text-gray-700">Practical Marks (Max: 50)</label>
                            <input type="number" name="mathematics_practical" id="mathematics_practical" min="0" max="50" step="0.01"
                                   value="{{ old('mathematics_practical') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- Programming -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">Programming</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="programming_theory" class="block text-sm text-gray-700">Theory Marks (Max: 50)</label>
                            <input type="number" name="programming_theory" id="programming_theory" min="0" max="50" step="0.01"
                                   value="{{ old('programming_theory') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="programming_practical" class="block text-sm text-gray-700">Practical Marks (Max: 50)</label>
                            <input type="number" name="programming_practical" id="programming_practical" min="0" max="50" step="0.01"
                                   value="{{ old('programming_practical') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- OOPS -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">OOPS Concept</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="oops_theory" class="block text-sm text-gray-700">Theory Marks (Max: 50)</label>
                            <input type="number" name="oops_theory" id="oops_theory" min="0" max="50" step="0.01"
                                   value="{{ old('oops_theory') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="oops_practical" class="block text-sm text-gray-700">Practical Marks (Max: 50)</label>
                            <input type="number" name="oops_practical" id="oops_practical" min="0" max="50" step="0.01"
                                   value="{{ old('oops_practical') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- Data Structure -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">Data Structure</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="data_structure_theory" class="block text-sm text-gray-700">Theory Marks (Max: 50)</label>
                            <input type="number" name="data_structure_theory" id="data_structure_theory" min="0" max="50" step="0.01"
                                   value="{{ old('data_structure_theory') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="data_structure_practical" class="block text-sm text-gray-700">Practical Marks (Max: 50)</label>
                            <input type="number" name="data_structure_practical" id="data_structure_practical" min="0" max="50" step="0.01"
                                   value="{{ old('data_structure_practical') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>

                <!-- Organization Behavior -->
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <h4 class="font-medium text-gray-700 mb-3">Organization Behavior</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="organization_behavior_theory" class="block text-sm text-gray-700">Theory Marks (Max: 50)</label>
                            <input type="number" name="organization_behavior_theory" id="organization_behavior_theory" min="0" max="50" step="0.01"
                                   value="{{ old('organization_behavior_theory') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <label for="organization_behavior_practical" class="block text-sm text-gray-700">Practical Marks (Max: 50)</label>
                            <input type="number" name="organization_behavior_practical" id="organization_behavior_practical" min="0" max="50" step="0.01"
                                   value="{{ old('organization_behavior_practical') }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <a href="{{ route('results.students') }}" 
                   class="px-6 py-2 border border-gray-300 rounded text-gray-600 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Create Result
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const classSelect = document.getElementById('class_id');
    const studentSelect = document.getElementById('student_id');

    classSelect.addEventListener('change', function() {
        const classId = this.value;
        if (classId) {
            fetch(`/results/get-students/${classId}`)
                .then(response => response.json())
                .then(students => {
                    studentSelect.innerHTML = '<option value="">Select Student</option>';
                    students.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = student.name;
                        if (student.id == '{{ old('student_id') }}') {
                            option.selected = true;
                        }
                        studentSelect.appendChild(option);
                    });
                });
        } else {
            studentSelect.innerHTML = '<option value="">Select Student</option>';
        }
    });

    // Trigger change event if class is already selected (e.g., on validation error)
    if (classSelect.value) {
        classSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endpush
@endsection
