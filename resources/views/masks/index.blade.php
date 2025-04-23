<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Subject Masks') }}
            </h2>
            <a href="{{ route('masks.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-plus mr-2"></i> {{ __('Add New Mask') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Filters Section -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium mb-3">{{ __('Filter Masks') }}</h3>
                        <form action="{{ route('masks.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700">{{ __('Subject') }}</label>
                                <select id="subject_id" name="subject_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('All Subjects') }}</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label for="exam_id" class="block text-sm font-medium text-gray-700">{{ __('Exam') }}</label>
                                <select id="exam_id" name="exam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('All Exams') }}</option>
                                    @foreach($exams as $exam)
                                        <option value="{{ $exam->id }}" {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                            {{ $exam->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="flex items-end">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <i class="fas fa-filter mr-2"></i> {{ __('Filter') }}
                                </button>
                                
                                @if(request('subject_id') || request('exam_id'))
                                    <a href="{{ route('masks.index') }}" class="ml-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                        {{ __('Clear') }}
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    
                    <!-- Masks List Table -->
                    <div class="overflow-x-auto relative">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3 px-6">{{ __('ID') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Subject') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Exam') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Mask Value') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Status') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Created By') }}</th>
                                    <th scope="col" class="py-3 px-6">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($masks as $mask)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="py-4 px-6">{{ $mask->id }}</td>
                                        <td class="py-4 px-6">{{ $mask->subject->name }}</td>
                                        <td class="py-4 px-6">{{ $mask->exam->title }}</td>
                                        <td class="py-4 px-6">{{ $mask->mask_value }}</td>
                                        <td class="py-4 px-6">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mask->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $mask->is_active ? __('Active') : __('Inactive') }}
                                            </span>
                                        </td>
                                        <td class="py-4 px-6">{{ $mask->creator->name ?? 'N/A' }}</td>
                                        <td class="py-4 px-6 flex">
                                            <a href="{{ route('masks.show', $mask) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('masks.edit', $mask) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('masks.destroy', $mask) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this mask?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-white border-b">
                                        <td colspan="7" class="py-4 px-6 text-center text-gray-500">
                                            {{ __('No masks found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $masks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 