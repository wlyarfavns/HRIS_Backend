<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - Platform HRIS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20,400,0,0" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
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

            <div class="mb-10 border-b border-gray-100 pb-6">
                <h2 class="text-4xl font-bold text-gray-900 mb-2">Verifikasi OTP</h2>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Kami telah mengirimkan 6 digit kode OTP ke email <strong class="text-gray-600">{{ $email }}</strong>. Masukkan kode tersebut di bawah ini.
                </p>
            </div>

            @if (session('status'))
                <div class="mb-6 bg-emerald-50 border border-emerald-200 text-[#0B3D2E] px-4 py-4 rounded-lg text-sm font-medium flex items-start gap-3">
                    <span class="material-symbols-outlined text-[20px]">check_circle</span>
                    <div>{{ session('status') }}</div>
                </div>
            @endif

            <form method="POST" action="{{ route('password.verify') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">


                <div>
                    <label for="otp_code" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Kode OTP</label>
                    <input type="text" id="otp_code" name="otp_code" required autofocus maxlength="6"
                           placeholder="123456"
                           value="{{ old('otp_code') }}"
                           class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm shadow-sm text-center tracking-[1em] font-bold text-xl">
                    @error('otp_code')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>


                <button type="submit"
                        class="w-full bg-[#0B3D2E] hover:bg-[#065A3D] text-white font-bold py-4 rounded-lg mt-8 text-sm transition flex justify-center items-center gap-2 group shadow-md">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    Verifikasi Kode
                </button>
            </form>

            <div class="text-center text-sm text-gray-500 mt-12">
                Belum menerima kode? 
                <a href="{{ route('password.request') }}" class="text-[#0B3D2E] font-semibold hover:underline transition ml-1">
                    Kirim Ulang
                </a>
            </div>

        </div>
    </div>

</body>
</html>
