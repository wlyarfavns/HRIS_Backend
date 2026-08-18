<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Ulang Kata Sandi - TalentaHR</title>
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


                    <rect x="130" y="90" width="140" height="130" rx="12" fill="#ffffff" stroke="#043927" stroke-width="2"/>
                    <rect x="130" y="90" width="140" height="30" rx="12" fill="#e2e8f0" stroke="#043927" stroke-width="2"/>


                    <rect x="165" y="150" width="70" height="50" rx="8" fill="#0B3D2E"/>

                    <path d="M178 150 L178 135 Q200 115 222 135 L222 150" stroke="#ffffff" stroke-width="5" fill="none" stroke-linecap="round"/>

                    <circle cx="200" cy="170" r="8" fill="white"/>
                    <rect x="197" y="170" width="6" height="12" rx="2" fill="white"/>


                    <rect x="95" y="110" width="25" height="4" rx="2" fill="#4ade80" opacity="0.6"/>
                    <rect x="95" y="122" width="18" height="4" rx="2" fill="#4ade80" opacity="0.4"/>


                    <rect x="280" y="110" width="25" height="4" rx="2" fill="#4ade80" opacity="0.6"/>
                    <rect x="280" y="122" width="18" height="4" rx="2" fill="#4ade80" opacity="0.4"/>


                    <rect x="256" y="60" width="80" height="30" rx="15" fill="#f59e0b"/>
                    <circle cx="271" cy="75" r="8" fill="#ffffff"/>
                    <path d="M268 75 L270 77 L274 73" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <rect x="284" y="73" width="40" height="4" rx="2" fill="#ffffff"/>


                    <rect x="64" y="58" width="70" height="28" rx="14" fill="#065A3D"/>
                    <circle cx="78" cy="72" r="7" fill="white" opacity="0.2"/>
                    <rect x="90" y="70" width="34" height="4" rx="2" fill="white" opacity="0.7"/>
                </svg>
            </div>

            <div class="mt-10 w-full max-w-sm relative z-10 space-y-4">
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">lock_reset</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Keamanan Akun Terjaga</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Kata sandi baru dienkripsi & disimpan dengan aman.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">verified_user</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Verifikasi Berlapis</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Akses hanya dapat diubah melalui email terdaftar.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="w-full lg:w-1/2 p-10 md:p-16 flex flex-col justify-center bg-white relative">


            <div class="lg:hidden flex items-center gap-2 text-[#0B3D2E] mb-8">
                <span class="text-xl font-bold tracking-tight">TalentaHR</span>
            </div>

            <div class="mb-8">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-emerald-50 rounded-2xl mb-6">
                    </div>
                <h2 class="text-4xl font-bold text-gray-900 mb-3">Buat Kata Sandi Baru</h2>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Masukkan kata sandi baru yang kuat untuk mendapatkan kembali akses ke akun TalentaHR Anda.
                </p>
            </div>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">


                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Email Kerja</label>
                    <input type="email" id="email" name="email" readonly
                           value="{{ $email ?? old('email') }}"
                           class="w-full px-5 py-4 bg-gray-100 border border-gray-200 rounded-lg text-gray-500 cursor-not-allowed text-sm shadow-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>


                <div x-data="{ show: false }">
                    <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">
                        Kata Sandi Baru <span class="text-red-400">*</span>
                    </label>
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


                <div x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">
                        Konfirmasi Kata Sandi <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password_confirmation"
                               name="password_confirmation" required
                               placeholder="••••••••"
                               class="w-full pl-5 pr-12 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm tracking-widest shadow-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#0B3D2E] transition">
                            <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                </div>


                <button type="submit"
                        class="w-full bg-[#0B3D2E] hover:bg-[#065A3D] text-white font-bold py-4 rounded-lg mt-4 text-sm transition flex justify-center items-center gap-2 group shadow-md hover:shadow-lg">
                    Simpan Kata Sandi Baru
                    <span class="material-symbols-outlined text-[18px] transform group-hover:translate-x-1 transition">arrow_forward</span>
                </button>
            </form>

            <div class="text-center text-sm text-gray-500 mt-8">
                Ingat kata sandi Anda?
                <a href="{{ route('login') }}" class="text-[#0B3D2E] font-semibold hover:underline transition ml-1">
                    Kembali Masuk
                </a>
            </div>
        </div>
    </div>

</body>
</html>
