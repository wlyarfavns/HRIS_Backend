@props(['route', 'active' => null, 'badge' => null])
@php
    $isActive = request()->routeIs($active ?? $route);
@endphp
<a href="{{ route($route) }}"
   class="nav-subtab {{ $isActive ? 'is-active' : '' }} flex items-center justify-between gap-2 py-2 pl-4 pr-3 rounded-lg text-[13px] transition-all">
    <span class="flex items-center gap-2.5">
        <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-primary' : 'bg-on-surface-variant/25' }} shrink-0"></span>
        <span>{{ $slot }}</span>
    </span>
    @if($badge)
        <span class="bg-brand-gold/20 text-primary text-[10px] font-bold px-1.5 py-0.5 rounded">{{ $badge }}</span>
    @endif
</a>