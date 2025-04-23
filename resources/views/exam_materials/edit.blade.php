@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-semibold">Edit Exam Material</h1>
        <a href="{{ route('exam-materials.index') }}" class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Back to Materials
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('exam-materials.update', $examMaterial) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                            <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title', $examMaterial->title) }}" required
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            placeholder="Enter material title">
                    </div>

                    <div>
                        <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-1">Related Exam <span class="text-red-500">*</span></label>
                        <select name="exam_id" id="exam_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select an exam</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ old('exam_id', $examMaterial->exam_id) == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->title }} ({{ $exam->subject->name ?? 'No Subject' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Material Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">Select type</option>
                            <option value="notes" {{ old('type', $examMaterial->type) == 'notes' ? 'selected' : '' }}>Notes</option>
                            <option value="practice" {{ old('type', $examMaterial->type) == 'practice' ? 'selected' : '' }}>Practice Questions/Answers</option>
                            <option value="syllabus" {{ old('type', $examMaterial->type) == 'syllabus' ? 'selected' : '' }}>Syllabus/Curriculum</option>
                            <option value="reference" {{ old('type', $examMaterial->type) == 'reference' ? 'selected' : '' }}>Reference Material</option>
                        </select>
                    </div>

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File</label>
                        <input type="file" name="file" id="file"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.rar">
                        <p class="mt-1 text-xs text-gray-500">Allowed formats: PDF, Word, Excel, PowerPoint, Text, ZIP (Max: 50MB)</p>
                        
                        @if($examMaterial->file_path)
                        <div class="mt-2 flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-file text-gray-500 mr-2"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate">
                                    Current file: {{ basename($examMaterial->file_path) }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Upload a new file to replace the current one
                                </p>
                            </div>
                            <div class="ml-4 flex-shrink-0">
                                <a href="{{ route('exam-materials.download', $examMaterial) }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="4"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                            placeholder="Enter a description of this material">{{ old('description', $examMaterial->description) }}</textarea>
                    </div>

                    <div>
                        <label for="release_date" class="block text-sm font-medium text-gray-700 mb-1">Release Date</label>
                        <input type="date" name="release_date" id="release_date" 
                            value="{{ old('release_date', $examMaterial->release_date ? date('Y-m-d', strtotime($examMaterial->release_date)) : '') }}"
                            class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        <p class="mt-1 text-xs text-gray-500">Leave empty for immediate release</p>
                    </div>

                    <div>
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-700 mb-1">Visibility</legend>
                            <div class="mt-1 space-y-2">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="is_active" name="is_active" type="checkbox" value="1" 
                                            {{ old('is_active', $examMaterial->is_active) ? 'checked' : '' }}
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_active" class="font-medium text-gray-700">Active</label>
                                        <p class="text-gray-500">Material will be visible to students when active</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="is_featured" name="is_featured" type="checkbox" value="1" 
                                            {{ old('is_featured', $examMaterial->is_featured) ? 'checked' : '' }}
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="is_featured" class="font-medium text-gray-700">Featured</label>
                                        <p class="text-gray-500">Material will be highlighted in listings</p>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div>
                        <fieldset>
                            <legend class="block text-sm font-medium text-gray-700 mb-1">Statistics</legend>
                            <div class="mt-1 bg-gray-50 p-3 rounded-md">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Downloads</p>
                                        <p class="text-lg font-semibold">{{ $examMaterial->download_count ?? 0 }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Created</p>
                                        <p class="text-sm">{{ $examMaterial->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Updated</p>
                                        <p class="text-sm">{{ $examMaterial->updated_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <div class="mt-6 border-t border-gray-200 pt-6">
                    <div class="flex justify-end space-x-3">
                        <a href="{{ route('exam-materials.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Cancel
                        </a>
                        <button type="submit" class="bg-blue-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Update Material
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // File upload validation
        const fileInput = document.getElementById('file');
        const fileLabel = document.querySelector('label[for="file"]');
        
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const fileSize = this.files[0].size / 1024 / 1024; // Convert to MB
                if (fileSize > 50) {
                    alert('File size exceeds the maximum limit of 50MB.');
                    this.value = '';
                } else {
                    fileLabel.textContent = 'File: ' + this.files[0].name;
                }
            }
        });
    });
</script>
@endsection