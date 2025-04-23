@props(['id', 'name', 'value' => null, 'placeholder' => '', 'required' => false, 'autofocus' => false])

<div class="relative">
    <input
        type="password"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $autofocus ? 'autofocus' : '' }}
        {{ $attributes->merge(['class' => 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm pr-10']) }}
    >
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 cursor-pointer" onclick="toggleMask(document.getElementById('{{ $id }}'))">
        <i class="fas fa-eye-slash text-gray-400 hover:text-gray-600 transition"></i>
    </div>
</div> 