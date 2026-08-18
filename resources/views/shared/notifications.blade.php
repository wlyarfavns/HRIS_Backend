@php

    $role = auth()->user()->getRoleNames()->first() ?? 'admin';
    if ($role === 'company') $role = 'admin'; 
@endphp

@extends("layouts.{$role}")

@section('page-title', 'Riwayat Notifikasi')
@section('page-desc', 'Semua daftar pemberitahuan dan aktivitas sistem Anda.')

@section('content')
<div class="bg-white rounded-md border border-black/5 shadow-sm overflow-hidden">
    <div class="px-6 py-5 border-b border-black/5 flex justify-between items-center bg-surface-container/10">
        <h2 class="text-base font-medium text-on-surface">Semua Notifikasi</h2>
    </div>

    <div class="divide-y divide-black/5">
        @forelse ($notifications as $notif)
            <div class="p-6 flex gap-5 {{ is_null($notif->read_at) ? 'bg-primary/[0.02]' : 'hover:bg-black/[0.02]' }} transition-colors relative">


                <div class="w-12 h-12 rounded-full {{ is_null($notif->read_at) ? 'bg-primary/10 text-primary' : 'bg-black/5 text-on-surface-variant/40' }} flex items-center justify-center shrink-0">
                    </div>


                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start mb-1.5 gap-4">
                        <h4 class="text-sm font-medium {{ is_null($notif->read_at) ? 'text-on-surface' : 'text-on-surface-variant/70' }}">
                            {{ $notif->data['title'] ?? 'Pemberitahuan Sistem' }}
                        </h4>
                        <span class="text-[11px] -data text-on-surface-variant/50 shrink-0 mt-0.5">
                            {{ $notif->created_at->translatedFormat('d M Y, H:i') }}
                        </span>
                    </div>
                    <p class="text-[13px] text-on-surface-variant/70 leading-relaxed max-w-4xl">
                        {{ $notif->data['message'] ?? '' }}
                    </p>

                </div>


                @if(is_null($notif->read_at))
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 w-2.5 h-2.5 rounded-full bg-primary"></div>
                @endif
            </div>
        @empty
            <div class="p-16 text-center">
                <h3 class="text-sm font-medium text-on-surface mb-1">Belum ada riwayat</h3>
                <p class="text-xs text-on-surface-variant/60">Notifikasi yang masuk akan otomatis tersimpan di sini.</p>
            </div>
        @endforelse
    </div>


    @if($notifications->hasPages())
        <div class="p-5 border-t border-black/5 bg-surface-container/10">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection
