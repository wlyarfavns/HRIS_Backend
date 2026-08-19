<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - Platform HRIS</title>
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
                <x-auth-illustration />
            </div>

            <div class="mt-10 w-full max-w-sm relative z-10 space-y-4">
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">lock_reset</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Proses Pemulihan Aman</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Tautan reset dikirim langsung ke email Anda.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">support_agent</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Dukungan Admin</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Hubungi IT/HR perusahaan jika Anda kehilangan akses email.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="w-full lg:w-1/2 p-10 md:p-16 flex flex-col justify-center bg-white relative">


            <div class="lg:hidden flex items-center gap-2 text-[#0B3D2E] mb-8">
                <span class="text-xl font-bold tracking-tight">TalentaHR</span>
            </div>

            <div class="mb-10 border-b border-gray-100 pb-6">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Lupa Sandi?</h2>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mereset kata sandi Anda.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-[#0B3D2E] px-4 py-4 rounded-lg text-sm font-medium flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf


                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Alamat Email</label>
                    <input type="email" id="email" name="email" required autofocus
                           placeholder="nama@perusahaan.com"
                           value="{{ old('email') }}"
                           class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm shadow-sm">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>


                <button type="submit"
                        class="w-full bg-[#0B3D2E] hover:bg-[#065A3D] text-white font-bold py-4 rounded-lg mt-8 text-sm transition flex justify-center items-center gap-2 group shadow-md">
                    <span class="material-symbols-outlined text-[18px]">mail</span>
                    Kirim Kode OTP
                </button>
            </form>

            <div class="text-center text-sm text-gray-500 mt-12">
                Kembali ke halaman 
                <a href="{{ route('login') }}" class="text-[#0B3D2E] font-semibold hover:underline transition ml-1">
                    Login
                </a>
            </div>

        </div>
    </div>

</body>
</html>

