<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Manage Prerequisites for') }}: {{ $subject->code }} - {{ $subject->name }}
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

                    <form method="POST" action="{{ route('subjects.prerequisites.update', $subject) }}">
                        @csrf
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Current Prerequisites') }}</h3>
                            
                            @if($subject->prerequisites->count() > 0)
                                <table class="min-w-full bg-white mb-4">
                                    <thead>
                                        <tr>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Code') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Name') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Type') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Min Grade') }}</th>
                                            <th class="py-2 px-4 border-b border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('Description') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($subject->prerequisites as $prerequisite)
                                            <tr>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $prerequisite->code }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $prerequisite->name }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ ucfirst($prerequisite->pivot->type) }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $prerequisite->pivot->min_grade ?? 'N/A' }}</td>
                                                <td class="py-2 px-4 border-b border-gray-200">{{ $prerequisite->pivot->description ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-gray-500 italic">{{ __('No prerequisites have been assigned yet.') }}</p>
                            @endif
                        </div>
                        
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ __('Assign Prerequisites') }}</h3>
                            
                            <div id="prerequisites-container">
                                <!-- Initial prerequisite row -->
                                <div class="flex flex-wrap -mx-2 mb-2 prerequisite-row">
                                    <div class="w-full md:w-1/3 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="prerequisite-0">
                                            {{ __('Prerequisite Subject') }} *
                                        </label>
                                        <select name="prerequisites[]" id="prerequisite-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="">{{ __('Select Subject') }}</option>
                                            @foreach($availableSubjects as $availableSubject)
                                                <option value="{{ $availableSubject->id }}">
                                                    {{ $availableSubject->code }} - {{ $availableSubject->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="type-0">
                                            {{ __('Type') }} *
                                        </label>
                                        <select name="type[]" id="type-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                            <option value="required">{{ __('Required') }}</option>
                                            <option value="recommended">{{ __('Recommended') }}</option>
                                            <option value="optional">{{ __('Optional') }}</option>
                                        </select>
                                    </div>
                                    
                                    <div class="w-full md:w-1/6 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="min-grade-0">
                                            {{ __('Min Grade') }}
                                        </label>
                                        <input type="text" name="min_grade[]" id="min-grade-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" maxlength="2" placeholder="e.g. C+">
                                    </div>
                                    
                                    <div class="w-full md:w-1/3 px-2 mb-2">
                                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description-0">
                                            {{ __('Description') }}
                                        </label>
                                        <input type="text" name="description[]" id="description-0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Why is this a prerequisite?">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <button type="button" id="add-prerequisite" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded text-sm focus:outline-none focus:shadow-outline">
                                    {{ __('+ Add Another Prerequisite') }}
                                </button>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Save Prerequisites') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let prerequisiteCount = 1;
            
            document.getElementById('add-prerequisite').addEventListener('click', function() {
                const container = document.getElementById('prerequisites-container');
                const newRow = document.createElement('div');
                newRow.className = 'flex flex-wrap -mx-2 mb-2 prerequisite-row';
                
                newRow.innerHTML = `
                    <div class="w-full md:w-1/3 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="prerequisite-${prerequisiteCount}">
                            {{ __('Prerequisite Subject') }} *
                        </label>
                        <select name="prerequisites[]" id="prerequisite-${prerequisiteCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">{{ __('Select Subject') }}</option>
                            @foreach($availableSubjects as $availableSubject)
                                <option value="{{ $availableSubject->id }}">
                                    {{ $availableSubject->code }} - {{ $availableSubject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="type-${prerequisiteCount}">
                            {{ __('Type') }} *
                        </label>
                        <select name="type[]" id="type-${prerequisiteCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="required">{{ __('Required') }}</option>
                            <option value="recommended">{{ __('Recommended') }}</option>
                            <option value="optional">{{ __('Optional') }}</option>
                        </select>
                    </div>
                    
                    <div class="w-full md:w-1/6 px-2 mb-2">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="min-grade-${prerequisiteCount}">
                            {{ __('Min Grade') }}
                        </label>
                        <input type="text" name="min_grade[]" id="min-grade-${prerequisiteCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" maxlength="2" placeholder="e.g. C+">
                    </div>
                    
                    <div class="w-full md:w-1/3 px-2 mb-2 flex">
                        <div class="flex-grow">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="description-${prerequisiteCount}">
                                {{ __('Description') }}
                            </label>
                            <input type="text" name="description[]" id="description-${prerequisiteCount}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Why is this a prerequisite?">
                        </div>
                        <div class="ml-2 self-end mb-1">
                            <button type="button" class="remove-prerequisite bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline">
                                &times;
                            </button>
                        </div>
                    </div>
                `;
                
                container.appendChild(newRow);
                prerequisiteCount++;
                
                // Add event listeners to all remove buttons
                document.querySelectorAll('.remove-prerequisite').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.prerequisite-row').remove();
                    });
                });
            });
        });
    </script>
</x-app-layout> 