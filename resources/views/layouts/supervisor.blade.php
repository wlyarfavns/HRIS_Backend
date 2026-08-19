<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard Supervisor') - TalentaHR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/turbolinks/5.0.0/turbolinks.js"></script>
    <style>
        .turbolinks-progress-bar {
            height: 3px;
            background-color: #0B3D2E;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 300, 'GRAD' 0, 'opsz' 24;
            display: inline-block;
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }


        .nav-tab.is-active {
            background-color: rgba(253, 251, 247, 0.1);
            color: #FDFBF7;
            font-weight: 600;
        }
        .nav-tab.is-active:hover { background-color: rgba(253, 251, 247, 0.15); }
        .nav-tab:not(.is-active) { color: rgba(253, 251, 247, 0.7); }
        .nav-tab:not(.is-active):hover { background-color: rgba(253, 251, 247, 0.05); color: #FDFBF7; }

        .nav-subtab.is-active {
            background-color: rgba(253, 251, 247, 0.1);
            color: #FDFBF7;
            font-weight: 600;
        }
        .nav-subtab.is-active:hover { background-color: rgba(253, 251, 247, 0.15); }
        .nav-subtab:not(.is-active) { color: rgba(253, 251, 247, 0.6); }
        .nav-subtab:not(.is-active):hover { background-color: rgba(253, 251, 247, 0.05); color: #FDFBF7; }

        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="text-gray-800 antialiased bg-gray-50 relative">
<div class="flex min-h-screen relative z-10">


        <aside class="w-64 bg-[#0B3D2E] text-[#FDFBF7] fixed h-screen flex flex-col shadow-sm z-50">
            <div class="px-6 py-6 border-b border-white/10">
                <h1 class="text-xl font-semibold tracking-tight leading-tight">TalentaHR</h1>
                <p class="text-[10px] font-medium text-white/50 uppercase tracking-widest mt-1">Supervisor</p>
            </div>

            <nav class="flex-1 px-4 pt-6 space-y-1 overflow-y-auto pb-8 custom-scrollbar">

                <x-nav-link route="supervisor.dashboard">
                    <span class="material-symbols-outlined text-[20px]">dashboard</span>
                    Dashboard
                </x-nav-link>

                <x-nav-group icon="fact_check" label="Persetujuan Tim"
                    :active="request()->routeIs('supervisor.approvals.*')">
                    <x-nav-sublink route="supervisor.approvals.leave" badge="2">Cuti &amp; Izin</x-nav-sublink>
                    <x-nav-sublink route="supervisor.approvals.overtime">Lembur (SPL)</x-nav-sublink>
                    <x-nav-sublink route="supervisor.approvals.reimbursement">Reimbursement</x-nav-sublink>
                    <x-nav-sublink route="supervisor.approvals.shift">Tukar Shift</x-nav-sublink>
                </x-nav-group>

                <x-nav-link route="supervisor.attendance.report">
                    <span class="material-symbols-outlined text-[20px]">groups</span>
                    Laporan Kehadiran Tim
                </x-nav-link>
            </nav>
        </aside>


        <div class="flex-1 ml-64 flex flex-col min-h-screen">


            <header class="sticky top-0 z-40 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between px-8 py-3.5 gap-6">
                    <div class="min-w-0">
                        <h1 class="text-lg font-medium text-gray-800">@yield('page-title', 'Dashboard')</h1>
                        @hasSection('page-desc')
                            <p class="text-sm text-gray-500">@yield('page-desc')</p>
                        @endif
                    </div>

                    <div class="flex items-center gap-4 shrink-0">
                        @yield('page-action')

                        @include('shared._topbar_notifications')

                        <div class="h-5 w-px bg-gray-200"></div>

                        @php
                            $user = auth()->user();
                            $userName = $user->name ?? 'User';
                            $userEmail = $user->email ?? 'email@example.com';
                            $roleLabel = $user?->getRoleNames()->first() ?? 'Supervisor';
                        @endphp


                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-3 group">
                                <div class="text-right hidden sm:block">
                                    <p class="text-sm font-medium text-gray-800 leading-tight">{{ $userName }}</p>
                                    <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">{{ $roleLabel }}</p>
                                </div>
                                <div class="relative flex items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=E9F3EF&color=043927"
                                        alt="Foto profil"
                                        class="w-9 h-9 rounded-full object-cover border border-gray-200 group-hover:border-[#0B3D2E] transition">
                                    <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover:text-gray-600 transition-transform duration-200 ml-1" :class="{'rotate-180': open}">expand_more</span>
                                </div>
                                </button>

                            <div x-show="open" x-transition.origin.top.right x-cloak
                                class="absolute right-0 mt-3 w-56 bg-white rounded-md shadow-sm border border-gray-100 py-2 z-30">
                                <div class="px-4 py-3 border-b border-gray-100 mb-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $userName }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $userEmail }}</p>
                                </div>
                                <a href="{{ route('supervisor.profile') }}"
                                    class="flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition cursor-pointer">
                                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                    Profil Saya
                                </a>
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 text-left transition cursor-pointer">
                                            <span class="material-symbols-outlined text-[18px]">logout</span>
                                            Keluar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="p-8 flex-1">
                @yield('content')
            </main>
        </div>
    </div>
    @include('shared._toast')
</body>
</html>
