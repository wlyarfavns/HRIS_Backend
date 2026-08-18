@props(['icon', 'label', 'active' => false])
<div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button type="button" @click="open = !open"
            class="nav-tab {{ $active ? 'is-active' : '' }} w-full flex items-center justify-between gap-3 p-3 px-4 rounded-lg text-sm transition-all">
        <span class="flex items-center gap-3">
            <span class="material-symbols-outlined text-[20px]">{{ $icon }}</span>
            <span>{{ $label }}</span>
        </span>
        <span class="material-symbols-outlined text-[16px] transition-transform duration-200" :class="open && 'rotate-180'">expand_more</span>
    </button>
    <div x-show="open" x-transition class="pl-4 mt-1 space-y-0.5" @if(!$active) style="display:none;" @endif>
        {{ $slot }}
    </div>
</div>
