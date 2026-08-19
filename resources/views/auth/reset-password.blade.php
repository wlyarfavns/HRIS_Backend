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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            
            <div class="text-center mb-10 w-full max-w-md">
                <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-4 tracking-tight" style="font-family: 'Plus Jakarta Sans', sans-serif;">
                    TalentaHR
                </h1>
                <p class="text-emerald-100/90 text-base leading-relaxed">
                    Sistem Informasi SDM Terpadu untuk mengelola administrasi, absensi, dan payroll dengan efisien.
                </p>
            </div>

            <div class="w-full max-w-[350px] relative z-10">
                <x-auth-illustration />
            </div>

        </div>
        <!-- RIGHT SIDE -->
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

