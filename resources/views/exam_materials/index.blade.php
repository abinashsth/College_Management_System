@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold">{{ isset($exam) ? $exam->title . ' - ' : '' }}Exam Materials</h1>
        @if(isset($exam))
        <a href="{{ route('exams.create-material', $exam) }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Material
        </a>
        @else
        <a href="{{ route('exam-materials.create') }}" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i> Add New Material
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-500"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4 sm:p-6">
            @if(!isset($exam))
            <div class="mb-4">
                <form action="{{ route('exam-materials.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                placeholder="Search by title or description">
                        </div>
                    </div>
                    <div class="sm:w-48">
                        <label for="type" class="sr-only">Material Type</label>
                        <select name="type" id="type"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Types</option>
                            @if(isset($types))
                                @foreach($types as $typeKey => $typeName)
                                    <option value="{{ $typeKey }}" {{ request('type') == $typeKey ? 'selected' : '' }}>
                                        {{ $typeName }}
                                    </option>
                                @endforeach
                            @else
                                <option value="notes" {{ request('type') == 'notes' ? 'selected' : '' }}>Notes</option>
                                <option value="practice" {{ request('type') == 'practice' ? 'selected' : '' }}>Practice</option>
                                <option value="syllabus" {{ request('type') == 'syllabus' ? 'selected' : '' }}>Syllabus</option>
                                <option value="reference" {{ request('type') == 'reference' ? 'selected' : '' }}>Reference</option>
                            @endif
                        </select>
                    </div>
                    <div class="sm:w-48">
                        <label for="exam_id" class="sr-only">Exam</label>
                        <select name="exam_id" id="exam_id"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Exams</option>
                            @foreach($exams as $examItem)
                                <option value="{{ $examItem->id }}" {{ request('exam_id') == $examItem->id ? 'selected' : '' }}>
                                    {{ $examItem->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                            Filter
                        </button>
                        <a href="{{ route('exam-materials.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
            @endif

            @php
                // Handle both $examMaterials and $materials variables
                $displayMaterials = isset($materials) ? $materials : (isset($examMaterials) ? $examMaterials : collect());
            @endphp

            @if($displayMaterials->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Exam / Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Release Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Downloads
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($displayMaterials as $material)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center bg-gray-100 rounded-md">
                                        @php
                                            $iconClass = 'text-gray-500';
                                            $icon = 'fa-file';
                                            
                                            if(property_exists($material, 'type')) {
                                                switch($material->type) {
                                                    case 'notes':
                                                        $icon = 'fa-file-alt';
                                                        $iconClass = 'text-blue-500'; 
                                                        break;
                                                    case 'practice':
                                                        $icon = 'fa-tasks';
                                                        $iconClass = 'text-green-500'; 
                                                        break;
                                                    case 'syllabus':
                                                        $icon = 'fa-list-ul';
                                                        $iconClass = 'text-purple-500'; 
                                                        break;
                                                    case 'reference':
                                                        $icon = 'fa-book';
                                                        $iconClass = 'text-yellow-500'; 
                                                        break;
                                                }
                                            }
                                        @endphp
                                        <i class="fas {{ $icon }} text-xl {{ $iconClass }}"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $material->title }}
                                        </div>
                                        @if(isset($material->is_featured) && $material->is_featured)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            Featured
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $material->exam->title ?? 'No Exam' }}</div>
                                <div class="text-sm text-gray-500">
                                    @if(property_exists($material, 'type'))
                                        @switch($material->type)
                                            @case('notes')
                                                Notes
                                                @break
                                            @case('practice')
                                                Practice Questions
                                                @break
                                            @case('syllabus')
                                                Syllabus
                                                @break
                                            @case('reference')
                                                Reference Material
                                                @break
                                            @default
                                                {{ ucfirst($material->type) }}
                                        @endswitch
                                    @else
                                        Unknown
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if(isset($material->release_date))
                                        {{ date('M d, Y', strtotime($material->release_date)) }}
                                    @else
                                        Immediate
                                    @endif
                                </div>
                                <div class="text-sm text-gray-500">
                                    Added: {{ $material->created_at->format('M d, Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if(isset($material->is_active) && $material->is_active)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $material->download_count ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2">
                                    @if(isset($exam))
                                    <a href="{{ route('exams.download-material', ['exam' => $exam->id, 'material' => $material->id]) }}" class="text-indigo-600 hover:text-indigo-900" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('exams.edit-material', ['exam' => $exam->id, 'material' => $material->id]) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('exams.destroy-material', ['exam' => $exam->id, 'material' => $material->id]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @else
                                    <a href="{{ route('exam-materials.download', $material) }}" class="text-indigo-600 hover:text-indigo-900" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <a href="{{ route('exam-materials.edit', $material) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('exam-materials.destroy', $material) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this material?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if(isset($examMaterials) && method_exists($examMaterials, 'links'))
            <div class="mt-4">
                {{ $examMaterials->links() }}
            </div>
            @endif
            @else
            <div class="text-center py-10">
                <i class="fas fa-file-alt text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No exam materials found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request('search') || request('type') || request('exam_id'))
                        No materials match your current filters. Try changing your search criteria.
                    @else
                        @if(isset($exam))
                        No materials have been added for this exam yet.
                        @else
                        Get started by adding your first exam material.
                        @endif
                    @endif
                </p>
                <div class="mt-6">
                    @if(isset($exam))
                    <a href="{{ route('exams.create-material', $exam) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Add New Material
                    </a>
                    @else
                    <a href="{{ route('exam-materials.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                        <i class="fas fa-plus mr-2"></i> Add New Material
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection