<div class="relative" x-data="{ open: false }" @click.outside="open = false">
    {{-- TOMBOL NOTIFIKASI --}}
    <button @click="open = !open" 
            class="relative p-2 text-on-surface-variant/60 hover:text-primary transition-colors focus:outline-none rounded-full hover:bg-black/5">
        <span class="material-symbols-outlined text-[24px]">notifications</span>
        
        {{-- Badge Unread dengan animasi --}}
        <span class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-gold opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-brand-gold ring-2 ring-white"></span>
        </span>
    </button>

    {{-- DROPDOWN PANEL --}}
    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="absolute right-0 mt-3 w-[340px] sm:w-[380px] bg-white rounded-2xl shadow-[0_12px_40px_rgba(0,0,0,0.12)] border border-black/5 z-50 overflow-hidden"
         style="display: none;">
         
         {{-- HEADER --}}
         <div class="px-5 py-4 flex items-center justify-between border-b border-black/5 bg-gradient-to-r from-surface-container/50 to-transparent">
             <div class="flex items-center gap-2">
                 <h3 class="text-sm font-bold text-on-surface">Notifikasi</h3>
                 <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-0.5 rounded-full">3 Baru</span>
             </div>
             <button class="text-[11px] font-semibold text-primary hover:text-primary/70 transition-colors">
                 Tandai semua dibaca
             </button>
         </div>

         {{-- DAFTAR NOTIFIKASI --}}
         <div class="max-h-[360px] overflow-y-auto divide-y divide-black/5 custom-scrollbar">
             
             {{-- Item 1: Unread --}}
             <a href="#" class="flex gap-4 px-5 py-4 hover:bg-black/[0.02] transition-colors bg-primary/[0.02] relative group">
                 <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-[20px]">task_alt</span>
                 </div>
                 <div class="flex-1 min-w-0 pr-4">
                     <p class="text-xs font-bold text-on-surface mb-0.5 group-hover:text-primary transition-colors">Approval Cuti Disetujui</p>
                     <p class="text-[11px] text-on-surface-variant/70 leading-relaxed truncate">Pengajuan cuti tahunan Anda (2 hari) telah disetujui oleh HR.</p>
                     <p class="text-[10px] text-on-surface-variant/50 font-medium mt-1.5 flex items-center gap-1">
                         <span class="material-symbols-outlined text-[12px]">schedule</span> 10 menit yang lalu
                     </p>
                 </div>
                 <div class="absolute right-5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-primary"></div>
             </a>

             {{-- Item 2: Unread --}}
             <a href="#" class="flex gap-4 px-5 py-4 hover:bg-black/[0.02] transition-colors bg-primary/[0.02] relative group">
                 <div class="w-10 h-10 rounded-full bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-[20px]">event_note</span>
                 </div>
                 <div class="flex-1 min-w-0 pr-4">
                     <p class="text-xs font-bold text-on-surface mb-0.5 group-hover:text-amber-700 transition-colors">Tugas Baru Diberikan</p>
                     <p class="text-[11px] text-on-surface-variant/70 leading-relaxed truncate">Supervisor menugaskan "Review Dokumen Q3" kepada Anda.</p>
                     <p class="text-[10px] text-on-surface-variant/50 font-medium mt-1.5 flex items-center gap-1">
                         <span class="material-symbols-outlined text-[12px]">schedule</span> 1 jam yang lalu
                     </p>
                 </div>
                 <div class="absolute right-5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-primary"></div>
             </a>

             {{-- Item 3: Unread (System) --}}
             <a href="#" class="flex gap-4 px-5 py-4 hover:bg-black/[0.02] transition-colors bg-primary/[0.02] relative group">
                 <div class="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                 </div>
                 <div class="flex-1 min-w-0 pr-4">
                     <p class="text-xs font-bold text-on-surface mb-0.5 group-hover:text-emerald-700 transition-colors">Slip Gaji Tersedia</p>
                     <p class="text-[11px] text-on-surface-variant/70 leading-relaxed truncate">Slip gaji untuk periode Agustus 2026 sudah dapat diunduh.</p>
                     <p class="text-[10px] text-on-surface-variant/50 font-medium mt-1.5 flex items-center gap-1">
                         <span class="material-symbols-outlined text-[12px]">schedule</span> 3 jam yang lalu
                     </p>
                 </div>
                 <div class="absolute right-5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-primary"></div>
             </a>

             {{-- Item 4: Read --}}
             <a href="#" class="flex gap-4 px-5 py-4 hover:bg-black/[0.02] transition-colors relative group opacity-75 hover:opacity-100">
                 <div class="w-10 h-10 rounded-full bg-slate-500/10 text-slate-600 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-[20px]">campaign</span>
                 </div>
                 <div class="flex-1 min-w-0">
                     <p class="text-xs font-bold text-on-surface mb-0.5 group-hover:text-slate-800 transition-colors">Pengumuman Perusahaan</p>
                     <p class="text-[11px] text-on-surface-variant/70 leading-relaxed truncate">Townhall bulanan akan diadakan pada hari Jumat pkl 14:00.</p>
                     <p class="text-[10px] text-on-surface-variant/50 font-medium mt-1.5 flex items-center gap-1">
                         <span class="material-symbols-outlined text-[12px]">schedule</span> Kemarin
                     </p>
                 </div>
             </a>

             {{-- Item 5: Read --}}
             <a href="#" class="flex gap-4 px-5 py-4 hover:bg-black/[0.02] transition-colors relative group opacity-75 hover:opacity-100">
                 <div class="w-10 h-10 rounded-full bg-red-500/10 text-red-600 flex items-center justify-center shrink-0">
                     <span class="material-symbols-outlined text-[20px]">cancel</span>
                 </div>
                 <div class="flex-1 min-w-0">
                     <p class="text-xs font-bold text-on-surface mb-0.5 group-hover:text-red-700 transition-colors">Pengajuan Reimbursement Ditolak</p>
                     <p class="text-[11px] text-on-surface-variant/70 leading-relaxed truncate">Klaim transport "Taksi Bandara" tidak sesuai kebijakan.</p>
                     <p class="text-[10px] text-on-surface-variant/50 font-medium mt-1.5 flex items-center gap-1">
                         <span class="material-symbols-outlined text-[12px]">schedule</span> 2 hari yang lalu
                     </p>
                 </div>
             </a>
         </div>

         {{-- FOOTER --}}
         <div class="p-3 border-t border-black/5 bg-surface-container/20 text-center">
             <a href="#" class="inline-block text-xs font-bold text-primary hover:text-primary/80 transition-colors py-1">
                 Lihat semua notifikasi
             </a>
         </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.2);
        }
    </style>
</div>