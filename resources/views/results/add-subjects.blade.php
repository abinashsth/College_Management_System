@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold mb-6">Add Subjects to Class</h1>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                {{ session('error') }}
            </div>
        @endif

        <form id="addSubjectsForm" method="POST">
            @csrf
            
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Class</label>
                <select id="class_select" class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('class_id') border-red-500 @enderror" required>
                    <option value="">Select Class</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }} {{ $class->section }}</option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Subjects</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($subjects as $subject)
                        <div class="flex items-center">
                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="mr-2 @error('subject_ids') border-red-500 @enderror">
                            <label>{{ $subject->name }} ({{ $subject->code }})</label>
                        </div>
                    @endforeach
                </div>
                @error('subject_ids')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Add Subjects
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addSubjectsForm');
    const classSelect = document.getElementById('class_select');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const classId = classSelect.value;
        if (!classId) {
            alert('Please select a class');
            return;
        }
        
        // Update form action with selected class ID using the route helper
        form.action = "{{ url('class') }}/" + classId + "/add-subjects";
        form.submit();
    });
});
</script>
@endpush
@endsection
