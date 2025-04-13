<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Teachers for') }}: {{ $subject->code }} - {{ $subject->name }}
            </h2>
            <a href="{{ route('subjects.show', $subject) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded inline-flex items-center">
                <span>{{ __('Back to Subject') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>{{ __('Whoops!') }}</strong> {{ __('There were some problems with your input.') }}<br><br>
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('subjects.teachers.update', $subject) }}">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Currently Assigned Teachers') }}</h3>
                            
                            @if($subject->teachers->count() > 0)
                                <table class="min-w-full bg-white mb-4">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Teacher') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Academic Session') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Role') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Hours/Week') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Date Range') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Coordinator') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subject->teachers as $teacher)
                                            <tr>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $teacher->name }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    @if($teacher->pivot->academic_session_id)
                                                        {{ optional(\App\Models\AcademicSession::find($teacher->pivot->academic_session_id))->name ?? 'N/A' }}
                                                    @else
                                                        {{ __('N/A') }}
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($teacher->pivot->role) }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $teacher->pivot->teaching_hours_per_week ?? 'N/A' }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    @if($teacher->pivot->start_date || $teacher->pivot->end_date)
                                                        {{ $teacher->pivot->start_date ? date('M d, Y', strtotime($teacher->pivot->start_date)) : 'N/A' }}
                                                        {{ __('to') }}
                                                        {{ $teacher->pivot->end_date ? date('M d, Y', strtotime($teacher->pivot->end_date)) : 'N/A' }}
                                                    @else
                                                        {{ __('Not Specified') }}
                                                    @endif
                                                </td>
                                                <td class="py-2 px-4 border-b border-gray-200">
                                                    {{ $teacher->pivot->is_coordinator ? __('Yes') : __('No') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 italic">{{ __('No teachers have been assigned yet.') }}</p>
                            @endif
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Assign Teachers') }}</h3>

                            <!-- Academic Session Selection -->
                            <div class="mb-4">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="academic-session">
                                    {{ __('Select Academic Session for New Assignments') }} *
                                </label>
                                <select id="academic-session" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    <option value="">{{ __('Select Academic Session') }}</option>
                                    @foreach($academicSessions as $session)
                                        <option value="{{ $session->id }}">
                                            {{ $session->name }} ({{ optional($session->academicYear)->name ?? 'No Year' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div id="teachers-container">
                                <!-- Initial teacher assignment row -->
                                <div class="flex flex-wrap -mx-2 mb-4 teacher-row">
                                    <div class="w-full md:w-1/4 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="teacher-0">
                                            {{ __('Teacher') }} *
                                        </label>
                                        <select name="teachers[]" id="teacher-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">{{ __('Select Teacher') }}</option>
                                            @foreach($availableTeachers as $teacher)
                                                <option value="{{ $teacher->id }}">
                                                    {{ $teacher->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <input type="hidden" name="academic_session_id[]" class="session-id" value="">
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role-0">
                                            {{ __('Role') }} *
                                        </label>
                                        <select name="role[]" id="role-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="instructor">{{ __('Instructor') }}</option>
                                            <option value="co-instructor">{{ __('Co-Instructor') }}</option>
                                            <option value="lab instructor">{{ __('Lab Instructor') }}</option>
                                            <option value="tutor">{{ __('Tutor') }}</option>
                                        </select>
                                    </div>
                                    
                                    <div class="w-full md:w-1/12 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="hours-0">
                                            {{ __('Hours/Week') }}
                                        </label>
                                        <input type="number" name="teaching_hours_per_week[]" id="hours-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1">
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="start-date-0">
                                            {{ __('Start Date') }}
                                        </label>
                                        <input type="date" name="start_date[]" id="start-date-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="end-date-0">
                                            {{ __('End Date') }}
                                        </label>
                                        <input type="date" name="end_date[]" id="end-date-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                    </div>
                                    
                                    <div class="w-full md:w-1/12 px-2 mb-2 flex items-center mt-6">
                                        <input type="checkbox" name="is_coordinator[]" id="is-coordinator-0" class="mr-2">
                                        <label for="is-coordinator-0" class="text-gray-700 text-sm font-bold">
                                            {{ __('Coordinator') }}
                                        </label>
                                    </div>
                                
                                    <div class="w-full px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes-0">
                                            {{ __('Notes') }}
                                        </label>
                                        <input type="text" name="notes[]" id="notes-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Additional information about this assignment">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <button type="button" id="add-teacher" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm focus:outline-none focus:shadow-outline">
                                    {{ __('+ Add Another Teacher') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Save Teacher Assignments') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let teacherCount = 1;
            const academicSessionSelect = document.getElementById('academic-session');
            const sessionInputs = document.querySelectorAll('.session-id');
            
            // Update all hidden session ID fields when the academic session changes
            academicSessionSelect.addEventListener('change', function() {
                document.querySelectorAll('.session-id').forEach(input => {
                    input.value = this.value;
                });
            });
            
            document.getElementById('add-teacher').addEventListener('click', function() {
                // Validate that an academic session is selected
                if (!academicSessionSelect.value) {
                    alert('{{ __("Please select an academic session first.") }}');
                    return;
                }
                
                const container = document.getElementById('teachers-container');
                const newRow = document.createElement('div');
                newRow.className = 'flex flex-wrap -mx-2 mb-4 teacher-row';
                
                newRow.innerHTML = `
                    <div class="w-full md:w-1/4 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="teacher-${teacherCount}">
                            {{ __('Teacher') }} *
                        </label>
                        <select name="teachers[]" id="teacher-${teacherCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">{{ __('Select Teacher') }}</option>
                            @foreach($availableTeachers as $teacher)
                                <option value="{{ $teacher->id }}">
                                    {{ $teacher->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <input type="hidden" name="academic_session_id[]" class="session-id" value="${academicSessionSelect.value}">
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="role-${teacherCount}">
                            {{ __('Role') }} *
                        </label>
                        <select name="role[]" id="role-${teacherCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="instructor">{{ __('Instructor') }}</option>
                            <option value="co-instructor">{{ __('Co-Instructor') }}</option>
                            <option value="lab instructor">{{ __('Lab Instructor') }}</option>
                            <option value="tutor">{{ __('Tutor') }}</option>
                        </select>
                    </div>
                    
                    <div class="w-full md:w-1/12 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="hours-${teacherCount}">
                            {{ __('Hours/Week') }}
                        </label>
                        <input type="number" name="teaching_hours_per_week[]" id="hours-${teacherCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" min="1">
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="start-date-${teacherCount}">
                            {{ __('Start Date') }}
                        </label>
                        <input type="date" name="start_date[]" id="start-date-${teacherCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="end-date-${teacherCount}">
                            {{ __('End Date') }}
                        </label>
                        <input type="date" name="end_date[]" id="end-date-${teacherCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    
                    <div class="w-full md:w-1/12 px-2 mb-2 flex items-center mt-6">
                        <input type="checkbox" name="is_coordinator[]" id="is-coordinator-${teacherCount}" class="mr-2">
                        <label for="is-coordinator-${teacherCount}" class="text-gray-700 text-sm font-bold">
                            {{ __('Coordinator') }}
                        </label>
                    </div>
                
                    <div class="w-full md:w-5/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes-${teacherCount}">
                            {{ __('Notes') }}
                        </label>
                        <input type="text" name="notes[]" id="notes-${teacherCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Additional information about this assignment">
                    </div>
                    
                    <div class="w-full md:w-1/12 px-2 mb-2 flex items-center mt-6">
                        <button type="button" class="remove-teacher bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded text-sm focus:outline-none focus:shadow-outline">
                            {{ __('Remove') }}
                        </button>
                    </div>
                `;
                
                container.appendChild(newRow);
                teacherCount++;
                
                // Add event listeners to all remove buttons
                document.querySelectorAll('.remove-teacher').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.teacher-row').remove();
                    });
                });
            });
        });
    </script>
</x-app-layout> 