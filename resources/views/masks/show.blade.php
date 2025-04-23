<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Subject Mask Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('masks.edit', $mask) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 focus:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-edit mr-2"></i> {{ __('Edit') }}
                </a>
                <a href="{{ route('masks.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <i class="fas fa-arrow-left mr-2"></i> {{ __('Back to Masks') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="flex flex-col md:flex-row md:space-x-8">
                        <!-- Main Information -->
                        <div class="md:w-1/2">
                            <h3 class="text-lg font-medium mb-4">{{ __('Mask Information') }}</h3>
                            
                            <div class="bg-white rounded-lg shadow divide-y">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('ID') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->id }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Subject') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->subject->code }} - {{ $mask->subject->name }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Exam') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->exam->title }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Mask Value') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->mask_value }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Status') }}</dt>
                                    <dd class="mt-1 text-sm sm:mt-0">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $mask->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $mask->is_active ? __('Active') : __('Inactive') }}
                                        </span>
                                    </dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Description') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->description ?? 'N/A' }}</dd>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Metadata Information -->
                        <div class="md:w-1/2 mt-6 md:mt-0">
                            <h3 class="text-lg font-medium mb-4">{{ __('Metadata') }}</h3>
                            
                            <div class="bg-white rounded-lg shadow divide-y">
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Created By') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->creator->name ?? 'N/A' }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Updated By') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->updater->name ?? 'N/A' }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Created At') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->created_at->format('M d, Y H:i:s') }}</dd>
                                </div>
                                
                                <div class="px-4 py-3 sm:grid sm:grid-cols-2 sm:gap-4">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Updated At') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ $mask->updated_at->format('M d, Y H:i:s') }}</dd>
                                </div>
                            </div>
                            
                            <!-- Delete Button (Danger Zone) -->
                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-red-600 mb-2">{{ __('Danger Zone') }}</h3>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <form action="{{ route('masks.destroy', $mask) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this mask? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <p class="text-sm text-red-600 mb-4">{{ __('Once you delete this mask, there is no going back. Please be certain.') }}</p>
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <i class="fas fa-trash mr-2"></i> {{ __('Delete Mask') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 