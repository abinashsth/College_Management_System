@props(['permission'])

@if(auth()->check() && auth()->user()->hasPermissionTo($permission))
    {{ $slot }}
@endif 