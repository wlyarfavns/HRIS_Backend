<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Super Admin - TalentaHR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; }
        .font-mono-data { font-family: 'IBM Plex Mono', ui-monospace, monospace; }
        .timeline-line::before {
            content: '';
            position: absolute;
            left: 19px;
            top: 40px;
            bottom: -20px;
            width: 1.5px;
            background: linear-gradient(to bottom, #d9e0dc, transparent);
        }
        .timeline-item:last-child .timeline-line::before { display: none; }
    </style>
</head>
<body class="bg-[#F6F6F3]">
<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#0d3b2e] text-white flex flex-col justify-between fixed h-screen">
        <div>
            <div class="flex items-center gap-3 px-6 py-6 border-b border-white/10">
                <div class="w-9 h-9 rounded-lg bg-emerald-200 flex items-center justify-center text-[#0d3b2e]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold leading-tight tracking-tight">TalentaHR</p>
                    <p class="text-[11px] text-emerald-200/80 font-mono-data tracking-wide">KONTROL PERUSAHAAN</p>
                </div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-emerald-700/60 text-white font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-emerald-300/70 uppercase tracking-wide">Perusahaan</p>
                <a href="#perusahaan" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7a2 2 0 012-2h4a2 2 0 012 2v14M15 21v-8a2 2 0 012-2h2a2 2 0 012 2v8M9 9h.01M9 13h.01M9 17h.01" />
                    </svg>
                    Manajemen Perusahaan
                </a>
                <a href="#struktur" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6a2 2 0 100-4 2 2 0 000 4zM6 20a2 2 0 100-4 2 2 0 000 4zM18 20a2 2 0 100-4 2 2 0 000 4zM12 6v6m0 0H6m6 0h6m-6 0v6m-6 2v-2m12 2v-2" />
                    </svg>
                    Struktur Organisasi
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-emerald-300/70 uppercase tracking-wide">Akses</p>
                <a href="#pengguna" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 10-8 0 4 4 0 008 0z" />
                    </svg>
                    Pengguna & Hak Akses
                </a>
                <a href="#keamanan" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v3h8z" />
                    </svg>
                    Keamanan
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-emerald-300/70 uppercase tracking-wide">Modul</p>
                <a href="#modul-hr" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12.5 7a4 4 0 11-8 0 4 4 0 018 0zM20 8v6M23 11h-6" />
                    </svg>
                    Modul HR
                </a>
                <a href="#modul-finance" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Modul Finance
                </a>

                <p class="px-3 pt-4 pb-1 text-xs font-semibold text-emerald-300/70 uppercase tracking-wide">Sistem</p>
                <a href="#langganan" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h16a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Langganan & Tagihan
                </a>
                <a href="#log-aktivitas" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Log Aktivitas
                </a>
                <a href="#integrasi" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Integrasi
                </a>
            </nav>
        </div>

        <div class="p-3 space-y-2 mb-2">
            <button class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-lg flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Undang Pengguna
            </button>
            <a href="#pengaturan" class="flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-100 hover:bg-emerald-800/40 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                Pengaturan Sistem
            </a>
            <a href="#bantuan" class="flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-100 hover:bg-emerald-800/40 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pusat Bantuan
            </a>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-64">

        {{-- TOP BAR --}}
        <header class="bg-white border-b border-gray-200 px-8 py-3.5 flex items-center justify-between sticky top-0 z-20">
            <div class="relative w-96">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                <input type="text" placeholder="Cari pengguna, cabang, departemen..."
                       class="w-full pl-9 pr-4 py-2 bg-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b2e]/30 focus:bg-white border border-transparent focus:border-gray-200 transition">
            </div>

            <div class="flex items-center gap-4">
                <button class="relative text-gray-400 hover:text-gray-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-amber-500 rounded-full ring-2 ring-white"></span>
                </button>

                {{-- PROFIL + LOGOUT DROPDOWN --}}
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 pl-4 border-l border-gray-200 group">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800 leading-tight">Andi Wijaya</p>
                            <p class="text-[11px] text-gray-400 font-mono-data tracking-wide">SUPER ADMIN</p>
                        </div>
                        <img src="https://i.pravatar.cc/40?img=15" alt="Foto profil" class="w-9 h-9 rounded-full object-cover ring-2 ring-transparent group-hover:ring-[#0d3b2e]/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-30"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">Andi Wijaya</p>
                            <p class="text-xs text-gray-400">andi.wijaya@talentahr.co.id</p>
                        </div>
                        <a href="#profil" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profil Saya
                        </a>
                        <a href="#pengaturan" class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            Pengaturan Akun
                        </a>
                        <div class="border-t border-gray-100 mt-1 pt-1">
                            <form method="POST" action="">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 text-left">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="p-8 space-y-6">

            {{-- WELCOME BANNER --}}
            <div class="bg-[#0d3b2e] rounded-xl p-8 flex items-center justify-between relative overflow-hidden">
                <div class="absolute right-0 top-0 bottom-0 w-64 opacity-[0.06]" style="background-image: repeating-linear-gradient(45deg, white 0, white 1px, transparent 1px, transparent 12px);"></div>
                <div class="relative z-10">
                    <p class="text-emerald-300 text-xs font-mono-data tracking-[0.12em] mb-2">RINGKASAN AKUN &middot; NOVEMBER 2026</p>
                    <h1 class="text-3xl font-extrabold text-white mb-2 tracking-tight">Selamat datang, Andi.</h1>
                    <p class="text-emerald-100/80 max-w-xl text-sm leading-relaxed">
                        Perusahaan kamu punya 3 cabang aktif dengan 1.284 karyawan dan 24 pengguna sistem.
                        Paket Business aktif hingga 15 Des 2026.
                    </p>
                </div>
                <button class="relative z-10 bg-amber-500 hover:bg-amber-600 transition text-white font-semibold px-5 py-3 rounded-lg flex items-center gap-2 whitespace-nowrap text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Undang Pengguna
                </button>
            </div>

            {{-- STAT ROW: 1 highlight + 3 compact --}}
            <div class="grid grid-cols-12 gap-5">

                <div class="col-span-4 bg-[#0d3b2e] rounded-xl p-6 text-white flex flex-col justify-between">
                    <div class="flex items-center justify-between">
                        <p class="text-emerald-200/70 text-xs font-mono-data tracking-wide">TOTAL KARYAWAN</p>
                        <span class="text-[11px] font-mono-data bg-white/10 px-2 py-0.5 rounded">+4%</span>
                    </div>
                    <p class="text-5xl font-extrabold font-mono-data mt-6 mb-1">1.284</p>
                    <p class="text-emerald-200/60 text-xs">Tersebar di 3 cabang aktif</p>
                </div>

                <div class="col-span-8 bg-white rounded-xl p-6 grid grid-cols-3 divide-x divide-gray-100">
                    <div class="pr-5">
                        <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-3">CABANG AKTIF</p>
                        <p class="text-3xl font-bold font-mono-data text-gray-900 mb-1">3</p>
                        <a href="#perusahaan" class="text-xs font-semibold text-[#0d3b2e] hover:underline">Kelola cabang →</a>
                    </div>
                    <div class="px-5">
                        <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-3">PENGGUNA SISTEM</p>
                        <p class="text-3xl font-bold font-mono-data text-gray-900 mb-1">24</p>
                        <a href="#pengguna" class="text-xs font-semibold text-[#0d3b2e] hover:underline">Kelola akses →</a>
                    </div>
                    <div class="pl-5">
                        <p class="text-gray-400 text-xs font-mono-data tracking-wide mb-3">PAKET LANGGANAN</p>
                        <p class="text-2xl font-bold text-gray-900 mb-1">Business</p>
                        <p class="text-xs text-amber-600 font-medium">Aktif &middot; s/d 15 Des</p>
                    </div>
                </div>
            </div>

            {{-- MIDDLE: RINGKASAN MODUL — left-border style, no repeated icon-box pattern --}}
            <div class="grid grid-cols-3 gap-5">
                <a href="#modul-hr" class="block bg-white rounded-xl p-5 border-l-[3px] border-amber-500 hover:shadow-sm transition">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-gray-900">Modul HR</p>
                        <span class="text-[11px] font-mono-data text-amber-700 bg-amber-50 px-2 py-0.5 rounded">12 pending</span>
                    </div>
                    <p class="text-sm text-gray-500">Pengajuan cuti, onboarding, dan kinerja karyawan.</p>
                </a>

                <a href="#modul-finance" class="block bg-white rounded-xl p-5 border-l-[3px] border-emerald-600 hover:shadow-sm transition">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-gray-900">Modul Finance</p>
                        <span class="text-[11px] font-mono-data text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Lancar</span>
                    </div>
                    <p class="text-sm text-gray-500">Penggajian, klaim, dan anggaran departemen.</p>
                </a>

                <a href="#log-aktivitas" class="block bg-white rounded-xl p-5 border-l-[3px] border-gray-300 hover:shadow-sm transition">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-gray-900">Log Aktivitas</p>
                        <span class="text-[11px] font-mono-data text-gray-500 bg-gray-50 px-2 py-0.5 rounded">48 entri</span>
                    </div>
                    <p class="text-sm text-gray-500">Riwayat perubahan data dan akses pengguna.</p>
                </a>
            </div>

            {{-- BOTTOM: LOG AKTIVITAS (timeline) + DISTRIBUSI ROLE --}}
            <div class="grid grid-cols-3 gap-5">

                {{-- LOG AKTIVITAS TERBARU — real timeline, not repeated icon circles --}}
                <div class="col-span-2 bg-white rounded-xl p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-base font-bold text-gray-900">Aktivitas Sistem Terbaru</h2>
                        <a href="#log-aktivitas" class="text-xs font-semibold text-gray-400 hover:text-gray-600">Lihat Semua</a>
                    </div>

                    <div class="space-y-6">
                        <div class="flex gap-4 relative timeline-item timeline-line">
                            <div class="w-10 shrink-0 text-center">
                                <p class="text-[11px] font-mono-data text-gray-400">1 jam</p>
                            </div>
                            <div class="flex-1 pb-1">
                                <p class="font-semibold text-gray-900 text-sm">Pengguna baru ditambahkan</p>
                                <p class="text-sm text-gray-500 mt-0.5">Dwight Schrute ditambahkan sebagai Supervisor cabang Scranton.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 relative timeline-item timeline-line">
                            <div class="w-10 shrink-0 text-center">
                                <p class="text-[11px] font-mono-data text-gray-400">3 jam</p>
                            </div>
                            <div class="flex-1 pb-1">
                                <p class="font-semibold text-gray-900 text-sm">Hak akses diubah</p>
                                <p class="text-sm text-gray-500 mt-0.5">Role Angela Martin diubah dari Staff menjadi Finance Admin.</p>
                            </div>
                        </div>

                        <div class="flex gap-4 relative timeline-item timeline-line">
                            <div class="w-10 shrink-0 text-center">
                                <p class="text-[11px] font-mono-data text-gray-400">Kmrn</p>
                            </div>
                            <div class="flex-1 pb-1">
                                <p class="font-semibold text-gray-900 text-sm">Tagihan langganan lunas</p>
                                <p class="text-sm text-gray-500 mt-0.5">Pembayaran paket Business bulan November telah diverifikasi.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DISTRIBUSI ROLE PENGGUNA --}}
                <div class="bg-white rounded-xl p-6">
                    <h2 class="text-base font-bold text-gray-900 mb-5">Distribusi Role Pengguna</h2>

                    <div class="space-y-3.5">
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 w-24 shrink-0">HR Admin</span>
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#0d3b2e] rounded-full" style="width: 25%"></div>
                            </div>
                            <span class="font-mono-data text-sm font-semibold text-gray-900 w-5 text-right">6</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 w-24 shrink-0">Finance</span>
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#0d3b2e] rounded-full" style="width: 21%"></div>
                            </div>
                            <span class="font-mono-data text-sm font-semibold text-gray-900 w-5 text-right">5</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 w-24 shrink-0">Supervisor</span>
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full" style="width: 46%"></div>
                            </div>
                            <span class="font-mono-data text-sm font-semibold text-gray-900 w-5 text-right">11</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-gray-600 w-24 shrink-0">Super Admin</span>
                            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gray-400 rounded-full" style="width: 8%"></div>
                            </div>
                            <span class="font-mono-data text-sm font-semibold text-gray-900 w-5 text-right">2</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs text-gray-400">Total 24 pengguna</p>
                        <button class="text-sm font-semibold text-[#0d3b2e] hover:underline">Kelola Semua →</button>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>