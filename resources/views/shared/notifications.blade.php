@php
    // Deteksi role user untuk menentukan layout sidebar mana yang akan dipakai
    $role = auth()->user()->getRoleNames()->first() ?? 'admin';
    if ($role === 'company') $role = 'admin'; // Penyesuaian nama folder layout super admin
@endphp

@extends("layouts.{$role}")

@section('page-title', 'Riwayat Notifikasi')
@section('page-desc', 'Semua daftar pemberitahuan dan aktivitas sistem Anda.')

@section('content')
<div class="bg-white rounded-2xl border border-black/5 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-black/5 flex justify-between items-center bg-surface-container/10">
        <h2 class="text-base font-bold text-on-surface">Semua Notifikasi</h2>
    </div>

    <div class="divide-y divide-black/5">
        @forelse ($notifications as $notif)
            <div class="p-6 flex gap-5 {{ is_null($notif->read_at) ? 'bg-primary/[0.02]' : 'hover:bg-black/[0.02]' }} transition-colors relative">
                
                {{-- Icon --}}
                <div class="w-12 h-12 rounded-full {{ is_null($notif->read_at) ? 'bg-primary/10 text-primary' : 'bg-black/5 text-on-surface-variant/40' }} flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-[24px]">
                        {{ is_null($notif->read_at) ? 'notifications_active' : 'notifications' }}
                    </span>
                </div>
                
                {{-- Konten --}}
                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-1.5 gap-4">
                        <h4 class="text-sm font-bold {{ is_null($notif->read_at) ? 'text-on-surface' : 'text-on-surface-variant/70' }}">
                            {{ $notif->data['title'] ?? 'Pemberitahuan Sistem' }}
                        </h4>
                        <span class="text-[11px] font-mono-data text-on-surface-variant/50 shrink-0 mt-0.5">
                            {{ $notif->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                    <p class="text-[13px] text-on-surface-variant/70 leading-relaxed max-w-4xl">
                        {{ $notif->data['message'] ?? '' }}
                    </p>
                    
                </div>

                {{-- Indikator Belum Dibaca --}}
                @if(is_null($notif->read_at))
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 w-2.5 h-2.5 rounded-full bg-primary"></div>
                @endif
            </div>
        @empty
            <div class="p-16 text-center">
                <span class="material-symbols-outlined text-[48px] text-black/10 block mb-3">notifications_off</span>
                <h3 class="text-sm font-bold text-on-surface mb-1">Belum ada riwayat</h3>
                <p class="text-xs text-on-surface-variant/60">Notifikasi yang masuk akan otomatis tersimpan di sini.</p>
            </div>
        @endforelse
    </div>

    {{-- Paginasi --}}
    @if($notifications->hasPages())
        <div class="p-5 border-t border-black/5 bg-surface-container/10">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection