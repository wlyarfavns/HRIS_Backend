<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Platform HRIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #eef2f6; 
            background-image: radial-gradient(#d1d5db 1px, transparent 1px);
            background-size: 32px 32px;
            margin: 0;
            padding: 0;
            overflow: hidden; 
        }
    </style>
</head>
<body class="h-screen w-screen flex items-center justify-center p-4 sm:p-8">


    <div class="w-full max-w-[1200px] h-full max-h-[800px] bg-white rounded-[2rem] shadow-[0_30px_80px_-20px_rgba(8,122,82,0.25)] flex flex-col lg:flex-row overflow-hidden relative z-10 border border-white/50">


        <div class="hidden lg:flex lg:w-1/2 bg-[#0B3D2E] p-12 flex-col justify-center items-center relative">

            <div class="text-center mb-12 z-10">
                <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">TalentaHR</h1>
                <p class="text-emerald-100/80 text-sm">Sistem Informasi SDM Terpadu</p>
            </div>


            <div class="w-full max-w-sm relative z-10">
                <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-2xl">

                    <path d="M50 150 C50 80, 120 50, 200 50 C280 50, 350 100, 350 180 C350 260, 260 280, 180 280 C100 280, 50 220, 50 150 Z" fill="#065A3D" opacity="0.6"/>


                    <rect x="70" y="80" width="260" height="160" rx="8" fill="#ffffff" stroke="#043927" stroke-width="2"/>

                    <rect x="70" y="80" width="260" height="24" rx="8" fill="#e2e8f0" stroke="#043927" stroke-width="2"/>
                    <circle cx="86" cy="92" r="4" fill="#ef4444"/>
                    <circle cx="100" cy="92" r="4" fill="#f59e0b"/>
                    <circle cx="114" cy="92" r="4" fill="#10b981"/>


                    <rect x="70" y="104" width="60" height="136" fill="#f8fafc" stroke="#043927" stroke-width="2"/>
                    <rect x="80" y="118" width="40" height="6" rx="3" fill="#cbd5e1"/>
                    <rect x="80" y="132" width="40" height="6" rx="3" fill="#cbd5e1"/>
                    <rect x="80" y="146" width="30" height="6" rx="3" fill="#cbd5e1"/>


                    <rect x="144" y="118" width="80" height="40" rx="4" fill="#0B3D2E" stroke="#043927" stroke-width="2"/>
                    <rect x="154" y="130" width="20" height="20" rx="10" fill="#FDFBF7" opacity="0.3"/>
                    <rect x="180" y="134" width="34" height="4" rx="2" fill="#FDFBF7"/>
                    <rect x="180" y="144" width="20" height="4" rx="2" fill="#FDFBF7"/>

                    <rect x="234" y="118" width="80" height="40" rx="4" fill="#ffffff" stroke="#043927" stroke-width="2"/>
                    <rect x="244" y="130" width="20" height="20" rx="10" fill="#0B3D2E" opacity="0.2"/>
                    <rect x="270" y="134" width="34" height="4" rx="2" fill="#0B3D2E" opacity="0.4"/>


                    <rect x="144" y="170" width="170" height="56" rx="4" fill="#ffffff" stroke="#043927" stroke-width="2"/>
                    <path d="M154 214 L154 186 L174 186 L174 214 Z" fill="#0B3D2E"/>
                    <path d="M184 214 L184 196 L204 196 L204 214 Z" fill="#4ade80"/>
                    <path d="M214 214 L214 176 L234 176 L234 214 Z" fill="#0B3D2E"/>
                    <path d="M244 214 L244 190 L264 190 L264 214 Z" fill="#4ade80"/>
                    <path d="M274 214 L274 200 L294 200 L294 214 Z" fill="#0B3D2E"/>


                    <rect x="260" y="60" width="80" height="30" rx="15" fill="#f59e0b" shadow="sm"/>
                    <circle cx="275" cy="75" r="8" fill="#ffffff"/>
                    <path d="M272 75 L274 77 L278 73" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="288" y="73" width="40" height="4" rx="2" fill="#ffffff"/>
                </svg>
            </div>

            <div class="mt-10 w-full max-w-sm relative z-10 space-y-4">
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">how_to_reg</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Absensi & Shift Terpadu</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Pantau kehadiran karyawan secara real-time.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">account_balance_wallet</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Payroll Otomatis</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Perhitungan gaji, lembur, dan potongan otomatis.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="w-full lg:w-1/2 p-10 md:p-16 flex flex-col justify-center bg-white relative">


            <div class="lg:hidden flex items-center gap-2 text-[#0B3D2E] mb-8">
                <span class="text-xl font-bold tracking-tight">TalentaHR</span>
            </div>

            <div class="mb-10">
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Selamat Datang</h2>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Masuk ke ekosistem TalentaHR untuk mengelola administrasi SDM dengan lebih cepat, aman, dan efisien. Silakan masukkan kredensial Anda.
                </p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                @csrf


                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Email</label>
                    <input type="email" id="email" name="email" required
                           placeholder="nama@perusahaan.com"
                           value="{{ old('email') }}"
                           class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm shadow-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>


                <div x-data="{ show: false }">
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest">Kata Sandi</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-[#0B3D2E] hover:text-[#065A3D] hover:underline transition">
                            Lupa sandi?
                        </a>
                    </div>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-5 pr-12 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm tracking-widest shadow-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#0B3D2E] transition">
                            <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>


                <button type="submit"
                        class="w-full bg-[#0B3D2E] hover:bg-[#065A3D] text-white font-bold py-4 rounded-lg mt-8 text-sm transition flex justify-center items-center gap-2 group shadow-md hover:shadow-lg">
                    Masuk ke Sistem
                    <span class="material-symbols-outlined text-[18px] transform group-hover:translate-x-1 transition">arrow_forward</span>
                </button>
            </form>

            <div class="text-center text-sm text-gray-500 mt-12">
                Belum mendaftarkan perusahaan? 
                <a href="{{ route('register') }}" class="text-[#0B3D2E] font-semibold hover:underline transition ml-1">
                    Daftar di sini
                </a>
            </div>

        </div>
    </div>

</body>
</html>
