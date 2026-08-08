<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Supervisor') - TalentaHR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono-data { font-family: 'IBM Plex Mono', ui-monospace, monospace; }
        .sidebar-border { border-right: 1px solid rgba(0,0,0,0.06); }
        .card-flat {
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        [x-cloak] { display: none !important; }
        /* Sidebar Supervisor — identitas terpisah, warna aksen sama (brand) */
        /* Dashboard Entrance Animations & Styled Visual Widgets */
        @keyframes dashboardFadeInUp {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.98);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        @keyframes gaugeRotate {
            0% { transform: rotate(-90deg); }
            100% { transform: rotate(var(--gauge-deg, 45deg)); }
        }
        @keyframes barGrowHorizontal {
            0% { width: 0%; }
        }
        @keyframes barGrowVertical {
            0% { height: 0%; }
        }
        @keyframes strokeDraw {
            0% { stroke-dashoffset: 600; }
            100% { stroke-dashoffset: 0; }
        }

        .animate-dash-card {
            opacity: 0;
            animation: dashboardFadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .dash-delay-1 { animation-delay: 0.06s; }
        .dash-delay-2 { animation-delay: 0.12s; }
        .dash-delay-3 { animation-delay: 0.18s; }
        .dash-delay-4 { animation-delay: 0.24s; }
        .dash-delay-5 { animation-delay: 0.30s; }
        .dash-delay-6 { animation-delay: 0.36s; }

        .animate-bar-grow {
            animation: barGrowHorizontal 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-bar-vertical {
            animation: barGrowVertical 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-gauge-needle {
            transform-origin: 50px 50px;
            animation: gaugeRotate 1.2s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }

        .animate-line-draw {
            stroke-dasharray: 600;
            stroke-dashoffset: 600;
            animation: strokeDraw 1.5s ease-out forwards;
        }

        .nav-tab.is-active { background-color: rgba(11, 61, 46, 0.1); color: #0B3D2E; font-weight: 700; }
        .nav-tab:hover:not(.is-active) { background-color: rgba(11, 61, 46, 0.05); }
        .nav-tab:not(.is-active) { color: rgba(65, 73, 68, 0.8); }
        .nav-subtab.is-active { background-color: rgba(11, 61, 46, 0.1); color: #0B3D2E; font-weight: 700; }
        .nav-subtab:hover:not(.is-active) { background-color: rgba(11, 61, 46, 0.08); }
        .nav-subtab.is-active:hover { background-color: rgba(11, 61, 46, 0.16); }
        .nav-subtab:not(.is-active) { color: rgba(65, 73, 68, 0.7); }
    </style>
</head>
<body class="text-on-surface antialiased bg-[#F4F7F5]">
<div class="flex min-h-screen">

    {{-- SIDEBAR SUPERVISOR — struktur & menu khusus role Supervisor --}}
    <aside class="w-72 bg-white sidebar-border fixed h-screen flex flex-col">
        <div class="px-8 py-6 border-b border-black/5">
            <h1 class="text-xl font-extrabold text-primary tracking-tight leading-tight">HRIS System</h1>
            <p class="text-[11px] font-bold text-on-surface-variant/50 uppercase tracking-widest mt-1">Modul Supervisor</p>
        </div>

        <nav class="flex-1 px-4 pt-6 space-y-1 overflow-y-auto pb-8">

            <x-nav-link route="supervisor.dashboard">
                <span class="material-symbols-outlined text-[20px]">dashboard</span>
                Dashboard
            </x-nav-link>

            <x-nav-group icon="fact_check" label="Persetujuan Tim"
                :active="request()->routeIs('supervisor.approvals.*')">
                <x-nav-sublink route="supervisor.approvals.leave" badge="2">Cuti &amp; Izin</x-nav-sublink>
                <x-nav-sublink route="supervisor.approvals.overtime">Lembur (SPL)</x-nav-sublink>
                <x-nav-sublink route="supervisor.approvals.reimbursement">Reimbursement</x-nav-sublink>
            </x-nav-group>

            <x-nav-link route="supervisor.attendance.report">
                <span class="material-symbols-outlined text-[20px]">groups</span>
                Laporan Kehadiran Tim
            </x-nav-link>
        </nav>
    </aside>

    {{-- MAIN CONTENT --}}
    <div class="flex-1 ml-72">

        {{-- TOP BAR --}}
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-black/5">
            <div class="flex items-center justify-between px-8 py-3.5 gap-6">
                <div class="min-w-0">
                    <h1 class="text-lg font-bold text-on-surface">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('page-desc')
                        <p class="text-sm text-on-surface-variant/60">@yield('page-desc')</p>
                    @endif
                </div>

                <div class="flex items-center gap-4 shrink-0">
                    @yield('page-action')

                    <button class="relative p-2 text-on-surface-variant/60 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-brand-gold rounded-full ring-2 ring-white"></span>
                    </button>

                    <div class="h-5 w-px bg-black/10"></div>

                    {{-- PROFIL + DROPDOWN --}}
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" class="flex items-center gap-3 group">
                            <div class="text-right hidden sm:block">
                                <p class="text-sm font-bold text-on-surface leading-tight">Andy Bernard</p>
                                <p class="text-[10px] text-on-surface-variant/60 font-bold uppercase tracking-wide">Supervisor</p>
                            </div>
                            <img src="https://i.pravatar.cc/40?img=51" alt="Foto profil"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-transparent group-hover:ring-primary/20 transition">
                            <span class="material-symbols-outlined text-on-surface-variant/50 text-[18px] transition"
                                :class="open && 'rotate-180'">expand_more</span>
                        </button>

                        <div x-show="open" x-transition.origin.top.right
                            class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-black/5 py-2 z-30"
                            style="display: none;">
                            <div class="px-4 py-2 border-b border-black/5">
                                <p class="text-sm font-bold text-on-surface">Andy Bernard</p>
                                <p class="text-xs text-on-surface-variant/60">andy.bernard@talentahr.co.id</p>
                            </div>
                            <a href="{{ route('supervisor.profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-sm text-on-surface-variant/80 hover:bg-primary/5 hover:text-primary transition">
                                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                Profil Saya
                            </a>
                            <div class="border-t border-black/5 mt-1 pt-1">
                                <form method="POST" action="">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-error hover:bg-error/5 text-left transition">
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

        <main class="p-8 space-y-6">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>