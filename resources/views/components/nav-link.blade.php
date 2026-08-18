@props(['route', 'active' => null])
@php
    $isActive = request()->routeIs($active ?? $route);
@endphp
<a href="{{ route($route) }}"
   {{ $attributes->merge(['class' => 'nav-tab ' . ($isActive ? 'is-active' : '') . ' flex items-center gap-3 p-3 px-4 rounded-lg text-sm transition-all']) }}>
    {{ $slot }}
</a>
