<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Supervisor - TalentaHR</title>
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
                    <p class="text-xs text-emerald-200">Modul Supervisor</p>
                </div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="{{ route('supervisor.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-emerald-700/60 text-white font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
            </nav>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-64" x-data="{ activeTab: 'tim' }">

        {{-- TOP BAR --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Dashboard Supervisor</h1>
                <p class="text-xs text-gray-400">Pantau dan kelola tim kamu</p>
            </div>

            <div class="flex items-center gap-5">
                <button class="relative text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                </button>

                {{-- PROFIL + LOGOUT DROPDOWN --}}
                <div class="relative border-l border-gray-200 pl-5" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open" class="flex items-center gap-3 group">
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-800 leading-tight">Andy Bernard</p>
                            <p class="text-xs text-gray-500">SUPERVISOR</p>
                        </div>
                        <img src="https://i.pravatar.cc/40?img=51" alt="Foto profil" class="w-10 h-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-[#0d3b2e]/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-30"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">Andy Bernard</p>
                            <p class="text-xs text-gray-400">andy.bernard@talentahr.co.id</p>
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
            {{-- TAB NAVIGATION --}}
            <div class="bg-white rounded-2xl shadow-sm">
                <div class="flex overflow-x-auto border-b border-gray-100">

                    <button @click="activeTab = 'tim'"
                            :class="activeTab === 'tim' ? 'border-[#0d3b2e] text-[#0d3b2e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex items-center gap-2 px-5 py-4 text-sm font-semibold border-b-2 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 10-8 0 4 4 0 008 0z" />
                        </svg>
                        Dashboard Tim
                    </button>

                    <button @click="activeTab = 'approval'"
                            :class="activeTab === 'approval' ? 'border-[#0d3b2e] text-[#0d3b2e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="relative flex items-center gap-2 px-5 py-4 text-sm font-semibold border-b-2 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Approval Pending
                        <span class="bg-amber-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">6</span>
                    </button>

                    <button @click="activeTab = 'detail'"
                            :class="activeTab === 'detail' ? 'border-[#0d3b2e] text-[#0d3b2e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex items-center gap-2 px-5 py-4 text-sm font-semibold border-b-2 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Detail Pengajuan
                    </button>

                    <button @click="activeTab = 'laporan'"
                            :class="activeTab === 'laporan' ? 'border-[#0d3b2e] text-[#0d3b2e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex items-center gap-2 px-5 py-4 text-sm font-semibold border-b-2 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V9.5M3 12l9-9 9 9" />
                        </svg>
                        Laporan Kehadiran
                    </button>

                    <button @click="activeTab = 'mobile'"
                            :class="activeTab === 'mobile' ? 'border-[#0d3b2e] text-[#0d3b2e]' : 'border-transparent text-gray-500 hover:text-gray-700'"
                            class="flex items-center gap-2 px-5 py-4 text-sm font-semibold border-b-2 whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Notifikasi Mobile
                    </button>

                </div>

                {{-- TAB CONTENT --}}
                <div class="p-8">

                    {{-- TAB 1: DASHBOARD TIM --}}
                    <div x-show="activeTab === 'tim'" x-cloak>
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-4a4 4 0 10-8 0 4 4 0 008 0zm6 0a4 4 0 10-8 0 4 4 0 008 0z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-700">Dashboard Tim</p>
                            <p class="text-sm text-gray-400 mt-1">Konten siapa hadir & siapa cuti akan ditampilkan di sini</p>
                        </div>
                    </div>

                    {{-- TAB 2: APPROVAL PENDING --}}
                    <div x-show="activeTab === 'approval'" x-cloak>
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-700">Approval Pending</p>
                            <p class="text-sm text-gray-400 mt-1">Daftar pengajuan cuti/lembur bawahan akan ditampilkan di sini</p>
                        </div>
                    </div>

                    {{-- TAB 3: DETAIL PENGAJUAN --}}
                    <div x-show="activeTab === 'detail'" x-cloak>
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-700">Detail Pengajuan</p>
                            <p class="text-sm text-gray-400 mt-1">Detail lengkap pengajuan untuk direview akan ditampilkan di sini</p>
                        </div>
                    </div>

                    {{-- TAB 4: LAPORAN KEHADIRAN --}}
                    <div x-show="activeTab === 'laporan'" x-cloak>
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center text-purple-700 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 012-2h2a2 2 0 012 2v6m-9 0h10a2 2 0 002-2V9.5M3 12l9-9 9 9" />
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-700">Laporan Kehadiran Tim</p>
                            <p class="text-sm text-gray-400 mt-1">Rekap kehadiran tim akan ditampilkan di sini</p>
                        </div>
                    </div>

                    {{-- TAB 5: NOTIFIKASI MOBILE --}}
                    <div x-show="activeTab === 'mobile'" x-cloak>
                        <div class="flex flex-col items-center justify-center py-16 text-center">
                            <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center text-gray-700 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="font-semibold text-gray-700">Notifikasi Approval Cepat (Mobile)</p>
                            <p class="text-sm text-gray-400 mt-1">Tampilan notifikasi approval versi mobile akan ditampilkan di sini</p>
                        </div>
                    </div>

                </div>
            </div>

        </main>
    </div>
</div>
</body>
</html>