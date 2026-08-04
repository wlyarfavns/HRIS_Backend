<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - TalentaHR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
<div class="flex min-h-screen">

    {{-- SIDEBAR --}}
    <aside class="w-64 bg-[#0d3b2e] text-white flex flex-col justify-between fixed h-screen">
        <div>
            <div class="flex items-center gap-3 px-6 py-6">
                <div class="w-9 h-9 rounded-lg bg-emerald-200 flex items-center justify-center text-[#0d3b2e]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold leading-tight">TalentaHR</p>
                    <p class="text-xs text-emerald-200">Sistem Manajemen HR</p>
                </div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-emerald-700/60 text-white font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 10-8 0 4 4 0 008 0z" />
                    </svg>
                    Direktori Karyawan
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Penggajian
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Cuti
                </a>
                <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V9.5M3 12l9-9 9 9" />
                    </svg>
                    Kinerja
                </a>
            </nav>
        </div>

        <div class="p-3 space-y-2 mb-2">
            <button class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-lg flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Aksi Cepat
            </button>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-100 hover:bg-emerald-800/40 text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                </svg>
                Pengaturan
            </a>
            <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg text-emerald-100 hover:bg-emerald-800/40 text-sm">
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
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <div class="relative w-96">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
                <input type="text" placeholder="Cari karyawan, dokumen..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-100 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b2e]">
            </div>

            <div class="flex items-center gap-5">
                <button class="relative text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                </button>
                <button class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </button>

                {{-- PROFIL + LOGOUT DROPDOWN --}}
                <div class="relative border-l border-gray-200 pl-5" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 group">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800 leading-tight">Sarah Johnson</p>
                            <p class="text-xs text-gray-500">HR ADMIN</p>
                        </div>
                        <img src="https://i.pravatar.cc/40?img=47" alt="Foto profil" class="w-10 h-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-[#0d3b2e]/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-30"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">Sarah Johnson</p>
                            <p class="text-xs text-gray-400">sarah.johnson@talentahr.co.id</p>
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
            <div class="bg-[#0d3b2e] rounded-2xl p-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Selamat datang kembali, Sarah!</h1>
                    <p class="text-emerald-100 max-w-xl">
                        Kamu punya 12 permintaan persetujuan yang tertunda dan 3 hari jadi karyawan
                        yang akan datang minggu ini. Produktivitas naik 8% di semua departemen.
                    </p>
                </div>
                <button class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-3 rounded-lg flex items-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Jalankan Laporan Mingguan
                </button>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 10-8 0 4 4 0 008 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">+4% bulan ini</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Total Karyawan</p>
                    <p class="text-3xl font-bold text-gray-900">1.284</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">Rata-rata harian: 94%</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Kehadiran Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-900">96,2%</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <a href="#" class="text-xs font-semibold text-[#0d3b2e] underline">Lihat Semua</a>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Permintaan Tertunda</p>
                    <p class="text-3xl font-bold text-gray-900">12</p>
                </div>
            </div>

            {{-- MIDDLE: ACTIVITY + CALENDAR --}}
            <div class="grid grid-cols-3 gap-6">

                {{-- RECENT ACTIVITY --}}
                <div class="col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-lg font-bold text-gray-900">Aktivitas Terbaru</h2>
                        <button class="text-sm text-gray-400 hover:text-gray-600">Hapus Log</button>
                    </div>

                    <div class="space-y-5">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2M12.5 7a4 4 0 11-8 0 4 4 0 018 0zM20 8v6M23 11h-6" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-900">Onboarding Karyawan Baru</p>
                                    <span class="text-xs text-gray-400">2 jam lalu</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">Michael Scott telah ditambahkan ke cabang Dunder Mifflin sebagai Regional Manager.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7 3h10a2 2 0 012 2v14l-4-2-4 2-4-2-4 2V5a2 2 0 012-2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-900">Penggajian Diproses</p>
                                    <span class="text-xs text-gray-400">5 jam lalu</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">Bonus kuartalan untuk Departemen Sales telah disetujui dan dijadwalkan.</p>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 3a1 1 0 112 0v1a1 1 0 11-2 0V3zM15.657 5.757a1 1 0 010 1.415M4.343 5.757a1 1 0 000 1.415M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="font-semibold text-gray-900">Hari Jadi Perusahaan</p>
                                    <span class="text-xs text-gray-400">Kemarin</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-0.5">Pam Beesly merayakan 3 tahun bersama perusahaan. Kartu hadiah telah dikirim.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CALENDAR --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm" x-data="{}">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Kalender</h2>
                        <div class="flex gap-1">
                            <button class="w-7 h-7 rounded-md hover:bg-gray-100 flex items-center justify-center text-gray-500">‹</button>
                            <button class="w-7 h-7 rounded-md hover:bg-gray-100 flex items-center justify-center text-gray-500">›</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-7 text-center text-xs text-gray-400 mb-2">
                        <span>M</span><span>S</span><span>S</span><span>R</span><span>K</span><span>J</span><span>S</span>
                    </div>
                    <div class="grid grid-cols-7 text-center text-sm gap-y-2">
                        @php
                            $days = ['28','29','30','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'];
                        @endphp
                        @foreach ($days as $i => $d)
                            <span class="py-1 rounded-full
                                {{ $d === '15' && $i > 5 ? 'bg-[#0d3b2e] text-white font-bold' : '' }}
                                {{ $i < 2 ? 'text-gray-300' : 'text-gray-700' }}">
                                {{ $d }}
                            </span>
                        @endforeach
                    </div>

                    <p class="text-xs font-semibold text-gray-400 mt-6 mb-3">ACARA MENDATANG</p>
                    <div class="space-y-3">
                        <div class="flex gap-3">
                            <div class="w-11 h-11 rounded-lg bg-amber-100 text-amber-700 flex flex-col items-center justify-center text-[10px] font-bold leading-tight shrink-0">
                                <span>OKT</span><span class="text-sm">24</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Trip Team Building</p>
                                <p class="text-xs text-gray-400">10:00 - Sepanjang hari</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-11 h-11 rounded-lg bg-emerald-100 text-emerald-700 flex flex-col items-center justify-center text-[10px] font-bold leading-tight shrink-0">
                                <span>OKT</span><span class="text-sm">27</span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Ulang Tahun Kevin Malone</p>
                                <p class="text-xs text-gray-400">Office Lounge</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BOTTOM: PENDING APPROVALS + STAFF DISTRIBUTION --}}
            <div class="grid grid-cols-3 gap-6">

                {{-- PENDING APPROVALS --}}
                <div class="col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Persetujuan Tertunda</h2>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-400 border-b border-gray-100">
                                <th class="pb-3 font-medium">Karyawan</th>
                                <th class="pb-3 font-medium">Jenis Permintaan</th>
                                <th class="pb-3 font-medium">Tanggal</th>
                                <th class="pb-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-50">
                                <td class="py-4 flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img=12" class="w-8 h-8 rounded-full" alt="">
                                    <span class="font-semibold text-gray-800">Jim Halpert</span>
                                </td>
                                <td>
                                    <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-1 rounded">CUTI TAHUNAN</span>
                                </td>
                                <td class="text-gray-600">24 Okt - 28 Okt</td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 text-emerald-600">✓</button>
                                        <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-red-50 hover:border-red-500 text-red-500">✕</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-4 flex items-center gap-3">
                                    <img src="https://i.pravatar.cc/32?img=33" class="w-8 h-8 rounded-full" alt="">
                                    <span class="font-semibold text-gray-800">Angela Martin</span>
                                </td>
                                <td>
                                    <span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded">REIMBURSEMENT BIAYA</span>
                                </td>
                                <td class="text-gray-600">21 Okt</td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 text-emerald-600">✓</button>
                                        <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-red-50 hover:border-red-500 text-red-500">✕</button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- STAFF DISTRIBUTION --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-gray-900 mb-5">Distribusi Staf</h2>

                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-700">Engineering</span>
                                <span class="font-semibold text-gray-900">42%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full">
                                <div class="h-2 bg-[#0d3b2e] rounded-full" style="width: 42%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-700">Marketing</span>
                                <span class="font-semibold text-gray-900">28%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full">
                                <div class="h-2 bg-[#0d3b2e] rounded-full" style="width: 28%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1.5">
                                <span class="text-gray-700">Sales</span>
                                <span class="font-semibold text-gray-900">15%</span>
                            </div>
                            <div class="w-full h-2 bg-gray-100 rounded-full">
                                <div class="h-2 bg-amber-500 rounded-full" style="width: 15%"></div>
                            </div>
                        </div>
                    </div>

                    <button class="w-full mt-6 border border-gray-300 rounded-lg py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Analitik Departemen Lengkap
                    </button>
                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>