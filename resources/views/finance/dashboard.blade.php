<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Finance - TalentaHR</title>
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
                    <p class="text-xs text-emerald-200">Modul Finance</p>
                </div>
            </div>

            <nav class="mt-4 px-3 space-y-1">
                <a href="{{ route('finance.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-emerald-700/60 text-white font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard
                </a>
                <a href="#klaim" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Klaim Pending
                </a>
                <a href="#reimbursement" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verifikasi Reimbursement
                </a>
                <a href="#payroll" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z" />
                    </svg>
                    Approval Payroll
                </a>
                <a href="#export" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export Bank Transfer
                </a>
                <a href="#riwayat" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-emerald-100 hover:bg-emerald-800/40">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Riwayat Disbursement
                </a>
            </nav>
        </div>

    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-64">

        {{-- TOP BAR --}}
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-bold text-gray-900">Dashboard Finance</h1>
                <p class="text-xs text-gray-400">Akses khusus tim keuangan</p>
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
                            <p class="text-sm font-semibold text-gray-800 leading-tight">Budi Santoso</p>
                            <p class="text-xs text-gray-500">FINANCE STAFF</p>
                        </div>
                        <img src="https://i.pravatar.cc/40?img=15" alt="Foto profil" class="w-10 h-10 rounded-full object-cover ring-2 ring-transparent group-hover:ring-[#0d3b2e]/20 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-30"
                         style="display: none;">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-800">Budi Santoso</p>
                            <p class="text-xs text-gray-400">budi.santoso@talentahr.co.id</p>
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
                    <h1 class="text-3xl font-bold text-white mb-2">Selamat datang, Budi!</h1>
                    <p class="text-emerald-100 max-w-xl">
                        Ada 8 klaim menunggu verifikasi dan payroll bulan ini siap untuk approval final.
                        Pastikan semua transaksi dicek sebelum proses disbursement.
                    </p>
                </div>
                <button class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-3 rounded-lg flex items-center gap-2 whitespace-nowrap">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Jalankan Laporan Keuangan
                </button>
            </div>

            {{-- STAT CARDS --}}
            <div class="grid grid-cols-4 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Klaim Pending</p>
                    <p class="text-3xl font-bold text-gray-900">8</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Menunggu Verifikasi</p>
                    <p class="text-3xl font-bold text-gray-900">5</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a4 4 0 00-8 0v2M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Payroll Siap Approval</p>
                    <p class="text-3xl font-bold text-gray-900">Rp 1,2 M</p>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-700 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm mb-1">Disbursement Bulan Ini</p>
                    <p class="text-3xl font-bold text-gray-900">24</p>
                </div>
            </div>

            {{-- 1. KLAIM PENDING --}}
            <div id="klaim" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Klaim Pending</h2>
                    <span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full">8 klaim</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-medium">Karyawan</th>
                            <th class="pb-3 font-medium">Jenis Klaim</th>
                            <th class="pb-3 font-medium">Jumlah</th>
                            <th class="pb-3 font-medium">Tanggal Ajuan</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-50">
                            <td class="py-4 flex items-center gap-3">
                                <img src="https://i.pravatar.cc/32?img=12" class="w-8 h-8 rounded-full" alt="">
                                <span class="font-semibold text-gray-800">Jim Halpert</span>
                            </td>
                            <td class="text-gray-600">Transport Dinas</td>
                            <td class="text-gray-800 font-medium">Rp 450.000</td>
                            <td class="text-gray-600">2 Nov 2025</td>
                            <td><span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-1 rounded">MENUNGGU</span></td>
                            <td>
                                <div class="flex gap-2">
                                    <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 text-emerald-600">✓</button>
                                    <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-red-50 hover:border-red-500 text-red-500">✕</button>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b border-gray-50">
                            <td class="py-4 flex items-center gap-3">
                                <img src="https://i.pravatar.cc/32?img=33" class="w-8 h-8 rounded-full" alt="">
                                <span class="font-semibold text-gray-800">Angela Martin</span>
                            </td>
                            <td class="text-gray-600">Alat Tulis Kantor</td>
                            <td class="text-gray-800 font-medium">Rp 180.000</td>
                            <td class="text-gray-600">1 Nov 2025</td>
                            <td><span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-1 rounded">MENUNGGU</span></td>
                            <td>
                                <div class="flex gap-2">
                                    <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-emerald-50 hover:border-emerald-500 text-emerald-600">✓</button>
                                    <button class="w-7 h-7 rounded-full border border-gray-300 flex items-center justify-center hover:bg-red-50 hover:border-red-500 text-red-500">✕</button>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="py-4 flex items-center gap-3">
                                <img src="https://i.pravatar.cc/32?img=8" class="w-8 h-8 rounded-full" alt="">
                                <span class="font-semibold text-gray-800">Dwight Schrute</span>
                            </td>
                            <td class="text-gray-600">Makan Lembur</td>
                            <td class="text-gray-800 font-medium">Rp 95.000</td>
                            <td class="text-gray-600">30 Okt 2025</td>
                            <td><span class="text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-1 rounded">MENUNGGU</span></td>
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

            {{-- 2. VERIFIKASI REIMBURSEMENT --}}
            <div id="reimbursement" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-bold text-gray-900">Verifikasi Reimbursement</h2>
                    <span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded-full">5 menunggu</span>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl p-4">
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/40?img=22" class="w-10 h-10 rounded-full" alt="">
                            <div>
                                <p class="font-semibold text-gray-800">Pam Beesly — Reimbursement Kesehatan</p>
                                <p class="text-sm text-gray-500">Bukti: kwitansi_klinik.pdf · Rp 320.000</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">Lihat Bukti</button>
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg bg-[#0d3b2e] text-white hover:bg-[#0a2f24]">Verifikasi</button>
                        </div>
                    </div>
                    <div class="flex items-center justify-between border border-gray-100 rounded-xl p-4">
                        <div class="flex items-center gap-4">
                            <img src="https://i.pravatar.cc/40?img=5" class="w-10 h-10 rounded-full" alt="">
                            <div>
                                <p class="font-semibold text-gray-800">Kevin Malone — Reimbursement Transportasi</p>
                                <p class="text-sm text-gray-500">Bukti: struk_taksi.jpg · Rp 150.000</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50">Lihat Bukti</button>
                            <button class="px-4 py-2 text-sm font-semibold rounded-lg bg-[#0d3b2e] text-white hover:bg-[#0a2f24]">Verifikasi</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. APPROVAL FINAL PAYROLL --}}
            <div id="payroll" class="bg-[#0d3b2e] rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-white">Approval Final Payroll</h2>
                        <p class="text-sm text-emerald-200">Periode: November 2025</p>
                    </div>
                    <span class="text-xs font-semibold bg-amber-500 text-white px-3 py-1.5 rounded-full">Menunggu Approval</span>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-emerald-200 text-sm">Total Karyawan</p>
                        <p class="text-2xl font-bold text-white">1.284</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-emerald-200 text-sm">Total Gaji Bersih</p>
                        <p class="text-2xl font-bold text-white">Rp 1.240.500.000</p>
                    </div>
                    <div class="bg-white/10 rounded-xl p-4">
                        <p class="text-emerald-200 text-sm">Total Potongan</p>
                        <p class="text-2xl font-bold text-white">Rp 186.200.000</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button class="bg-white text-[#0d3b2e] font-semibold px-5 py-3 rounded-lg hover:bg-gray-100 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Setujui Payroll
                    </button>
                    <button class="bg-transparent border border-white/30 text-white font-semibold px-5 py-3 rounded-lg hover:bg-white/10">
                        Tinjau Detail
                    </button>
                </div>
            </div>

            {{-- 4. EXPORT BANK TRANSFER --}}
            <div id="export" class="bg-white rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Export Bank Transfer</h2>
                        <p class="text-sm text-gray-500">Unduh file CSV untuk proses transfer massal ke bank</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="border border-gray-100 rounded-xl p-4">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Periode</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b2e]">
                            <option>November 2025</option>
                            <option>Oktober 2025</option>
                        </select>
                    </div>
                    <div class="border border-gray-100 rounded-xl p-4">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Format Bank</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#0d3b2e]">
                            <option>BCA (CSV)</option>
                            <option>Mandiri (CSV)</option>
                            <option>BNI (CSV)</option>
                        </select>
                    </div>
                    <div class="border border-gray-100 rounded-xl p-4 flex flex-col justify-between">
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">Total Transaksi</label>
                        <p class="text-lg font-bold text-gray-800">1.284 rekening</p>
                    </div>
                </div>

                <button class="mt-5 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-5 py-3 rounded-lg flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export CSV
                </button>
            </div>

            {{-- 5. RIWAYAT DISBURSEMENT --}}
            <div id="riwayat" class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Riwayat Disbursement</h2>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-gray-100">
                            <th class="pb-3 font-medium">Tanggal</th>
                            <th class="pb-3 font-medium">Jenis</th>
                            <th class="pb-3 font-medium">Jumlah Penerima</th>
                            <th class="pb-3 font-medium">Total Nominal</th>
                            <th class="pb-3 font-medium">Status</th>
                            <th class="pb-3 font-medium">File</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-50">
                            <td class="py-4 text-gray-600">1 Nov 2025</td>
                            <td class="text-gray-800 font-medium">Payroll Oktober</td>
                            <td class="text-gray-600">1.280 orang</td>
                            <td class="text-gray-800 font-medium">Rp 1.198.400.000</td>
                            <td><span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded">SELESAI</span></td>
                            <td><a href="#" class="text-[#0d3b2e] font-medium hover:underline">Unduh CSV</a></td>
                        </tr>
                        <tr class="border-b border-gray-50">
                            <td class="py-4 text-gray-600">28 Okt 2025</td>
                            <td class="text-gray-800 font-medium">Reimbursement Batch #12</td>
                            <td class="text-gray-600">34 orang</td>
                            <td class="text-gray-800 font-medium">Rp 8.750.000</td>
                            <td><span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded">SELESAI</span></td>
                            <td><a href="#" class="text-[#0d3b2e] font-medium hover:underline">Unduh CSV</a></td>
                        </tr>
                        <tr>
                            <td class="py-4 text-gray-600">1 Okt 2025</td>
                            <td class="text-gray-800 font-medium">Payroll September</td>
                            <td class="text-gray-600">1.276 orang</td>
                            <td class="text-gray-800 font-medium">Rp 1.185.900.000</td>
                            <td><span class="text-xs font-semibold bg-emerald-100 text-emerald-700 px-2.5 py-1 rounded">SELESAI</span></td>
                            <td><a href="#" class="text-[#0d3b2e] font-medium hover:underline">Unduh CSV</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </div>
</div>
</body>
</html>