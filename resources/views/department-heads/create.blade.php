@extends('layouts.app')

@section('title', 'Assign Department Head')

@section('content')
<div class="container mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Assign Department Head</h1>
        <a href="{{ route('departments.show', $department) }}" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i> Back to Department
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden p-6">
        <form action="{{ route('departments.heads.store', $department) }}" method="POST">
            @csrf
            
            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Department Information</h2>
                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                    @if($department->logo)
                        <div class="mr-4 w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                            <img src="{{ asset('storage/' . $department->logo) }}" alt="{{ $department->name }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="mr-4 w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-building text-3xl text-gray-400"></i>
                        </div>
                    @endif
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ $department->name }}</h3>
                        <p class="text-gray-600">{{ $department->code }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Faculty: 
                            @if($department->faculty)
                                {{ $department->faculty->name }}
                            @else
                                Not assigned
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Select Faculty Member</h2>
                
                @if(isset($preselectedUser))
                    <input type="hidden" name="user_id" value="{{ $preselectedUser->id }}">
                    <div class="flex items-center p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="mr-4 w-12 h-12 bg-gray-100 rounded-full overflow-hidden">
                            @if($preselectedUser->profile_photo_path)
                                <img src="{{ asset('storage/' . $preselectedUser->profile_photo_path) }}" alt="{{ $preselectedUser->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-100 text-blue-500">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-md font-medium text-gray-900">{{ $preselectedUser->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $preselectedUser->email }}</p>
                        </div>
                    </div>
                @else
                    <div class="mb-4">
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">
                            Select Department Head <span class="text-red-600">*</span>
                        </label>
                        <select name="user_id" id="user_id" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('user_id') border-red-300 @enderror">
                            <option value="">Select Faculty Member</option>
                            @foreach($departmentTeachers as $teacher)
                                <option value="{{ $teacher->user_id }}" {{ old('user_id') == $teacher->user_id ? 'selected' : '' }}>
                                    {{ $teacher->user->name }} ({{ $teacher->user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        @if(count($departmentTeachers) === 0)
                            <p class="mt-2 text-sm text-amber-600">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                No teachers are currently assigned to this department. 
                                <a href="{{ route('departments.teachers.assign', $department) }}" class="text-blue-600 hover:text-blue-800">Assign teachers first</a>.
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="appointment_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Appointment Date <span class="text-red-600">*</span>
                    </label>
                    <input type="date" name="appointment_date" id="appointment_date" value="{{ old('appointment_date', date('Y-m-d')) }}" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('appointment_date') border-red-300 @enderror">
                    @error('appointment_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        End Date
                    </label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('end_date') border-red-300 @enderror">
                    @error('end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Leave empty if appointment is ongoing</p>
                </div>

                <div>
                    <label for="appointment_reference" class="block text-sm font-medium text-gray-700 mb-1">
                        Appointment Reference
                    </label>
                    <input type="text" name="appointment_reference" id="appointment_reference" value="{{ old('appointment_reference') }}"
                        placeholder="e.g., Order No. ABC-123"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('appointment_reference') border-red-300 @enderror">
                    @error('appointment_reference')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-1">
                        Status
                    </label>
                    <select name="is_active" id="is_active" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('is_active') border-red-300 @enderror">
                        <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label for="job_description" class="block text-sm font-medium text-gray-700 mb-1">
                    Job Description
                </label>
                <textarea name="job_description" id="job_description" rows="4"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50 @error('job_description') border-red-300 @enderror">{{ old('job_description') }}</textarea>
                @error('job_description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if($currentHead)
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-full p-2 text-yellow-600">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Current Department Head Will Be Replaced</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>
                                    {{ $currentHead->user->name }} is currently assigned as the department head. 
                                    Assigning a new head will automatically end the current head's tenure as of today.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                    Assign Department Head
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const appointmentDateField = document.getElementById('appointment_date');
        const endDateField = document.getElementById('end_date');
        
        if (appointmentDateField && endDateField) {
            appointmentDateField.addEventListener('change', function() {
                // If end date is filled and before appointment date, clear it
                if (endDateField.value && endDateField.value < this.value) {
                    endDateField.value = '';
                }
                
                // Set min date for end date field
                endDateField.min = this.value;
            });
            
            // Set initial min date for end date field
            if (appointmentDateField.value) {
                endDateField.min = appointmentDateField.value;
            }
        }
    });
</script>
@endsection 