<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Select Exam and Subject') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-4 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Select Exam and Subject for Mark Entry</h3>
                        <div>
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm">Back to Dashboard</a>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('marks.create') }}" class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-label for="exam_id" :value="__('Select Exam')" />
                                <select id="exam_id" name="exam_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Select Exam --</option>
                                    @foreach ($exams as $exam)
                                        <option value="{{ $exam->id }}">
                                            {{ $exam->title }} ({{ $exam->exam_type }}) - {{ $exam->academicSession->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <x-label for="subject_id" :value="__('Select Subject')" />
                                <select id="subject_id" name="subject_id" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">-- Select Subject --</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}">
                                            {{ $subject->name }} ({{ $subject->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="flex items-center justify-center mt-6">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                {{ __('Proceed to Mark Entry') }}
                            </button>
                        </div>
                    </form>

                    <div class="mt-8">
                        <h4 class="text-lg font-medium mb-4">Other Options</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <a href="{{ route('marks.import') }}" class="block p-4 bg-green-50 border border-green-200 rounded-md hover:bg-green-100">
                                <div class="font-medium text-green-800">Bulk Import Marks</div>
                                <p class="text-sm text-green-600 mt-1">Upload Excel/CSV file to import marks in bulk</p>
                            </a>
                            
                            <a href="{{ route('marks.index') }}" class="block p-4 bg-blue-50 border border-blue-200 rounded-md hover:bg-blue-100">
                                <div class="font-medium text-blue-800">View Existing Marks</div>
                                <p class="text-sm text-blue-600 mt-1">Browse and manage existing mark entries</p>
                            </a>
                            
                            <a href="#" class="block p-4 bg-purple-50 border border-purple-200 rounded-md hover:bg-purple-100">
                                <div class="font-medium text-purple-800">Verification Queue</div>
                                <p class="text-sm text-purple-600 mt-1">View marks pending verification</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 