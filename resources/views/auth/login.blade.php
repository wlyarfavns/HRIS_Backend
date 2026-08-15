<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - TalentaHR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        @keyframes blob {
            0% { transform: translate(0px, 0px) scale(1); }
            33% { transform: translate(30px, -50px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.9); }
            100% { transform: translate(0px, 0px) scale(1); }
        }
        .animate-blob {
            animation: blob 7s infinite;
        }
        .animation-delay-2000 {
            animation-delay: 2s;
        }
        .animation-delay-4000 {
            animation-delay: 4s;
        }
    </style>
</head>
<body class="bg-[#0B3D2E] min-h-screen flex items-center justify-center p-6 relative overflow-hidden font-sans">
    
    {{-- Animated Background Blobs for Glassmorphism Effect --}}
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[#2B7A55] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob"></div>
    <div class="absolute top-[20%] right-[-10%] w-96 h-96 bg-[#1D5F42] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-2000"></div>
    <div class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-[#5C7166] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-4000"></div>

    <div x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 100)"
         class="w-full max-w-5xl rounded-[2rem] overflow-hidden flex flex-col md:flex-row shadow-2xl relative z-10 border border-white/20 transform transition-all duration-1000"
         :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">
        
        {{-- LEFT PANEL (Illustration / Info - Sesuai versi lama di kiri) --}}
        <div class="w-full md:w-1/2 bg-white/80 backdrop-blur-xl p-10 lg:p-14 flex flex-col justify-between relative overflow-hidden hidden md:flex">
            
            {{-- Header/Brand --}}
            <div class="flex items-center gap-3 relative z-10 transform transition-all duration-700 delay-100"
                 :class="mounted ? 'translate-x-0 opacity-100' : '-translate-x-8 opacity-0'">
                <div class="w-10 h-10 rounded-xl bg-[#0B3D2E] flex items-center justify-center text-white shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900">TalentaHR</h1>
            </div>

            {{-- Quote Section --}}
            <div class="relative z-10 transform transition-all duration-700 delay-300"
                 :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Khusus Akses Internal</h3>
                <p class="text-base font-medium text-gray-600 leading-relaxed mb-6">
                    Memberdayakan tim kami dengan manajemen SDM terpusat, pelacakan penggajian, dan wawasan kinerja yang mendalam.
                </p>
            </div>

            {{-- Footer Copyright --}}
            <div class="relative z-10 text-xs text-gray-500 transform transition-all duration-700 delay-500"
                 :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                <p>© {{ date('Y') }} TalentaHR Inc. <a href="#" class="hover:underline">Privasi</a> · <a href="#" class="hover:underline">Keamanan</a></p>
            </div>

            {{-- Mockup Icon / Illustration (Buildings) --}}
            <div class="absolute bottom-[-10%] right-[-10%] w-[120%] h-[60%] opacity-80 pointer-events-none transform transition-all duration-1000 delay-500 origin-bottom"
                 :class="mounted ? 'scale-100 opacity-80' : 'scale-90 opacity-0'">
                <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg" class="w-full h-full drop-shadow-xl text-[#0B3D2E]">
                    <g stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M 50 200 L 50 120 L 100 100 L 100 200" fill="#D7E8DD" />
                        <path d="M 100 100 L 150 120 L 150 200" fill="#EAF4EE" />
                        <path d="M 100 100 L 100 200" />
                        <line x1="60" y1="130" x2="80" y2="122" />
                        <line x1="60" y1="150" x2="80" y2="142" />
                        <line x1="60" y1="170" x2="80" y2="162" />
                        
                        <path d="M 140 200 L 140 50 L 220 20 L 220 200" fill="white" />
                        <path d="M 220 20 L 260 40 L 260 200" fill="#F9F6EE" />
                        <path d="M 220 20 L 220 200" />
                        <line x1="160" y1="60" x2="160" y2="80" stroke="#F6C58A" stroke-width="4" />
                        <line x1="180" y1="55" x2="180" y2="75" stroke="#F6C58A" stroke-width="4" />
                        <line x1="200" y1="50" x2="200" y2="70" stroke="#F6C58A" stroke-width="4" />
                        <line x1="160" y1="100" x2="160" y2="120" stroke="#F6C58A" stroke-width="4" />
                        <line x1="180" y1="95" x2="180" y2="115" stroke="#F6C58A" stroke-width="4" />
                        <line x1="200" y1="90" x2="200" y2="110" stroke="#F6C58A" stroke-width="4" />
                        <line x1="160" y1="140" x2="160" y2="160" stroke="#F6C58A" stroke-width="4" />
                        <line x1="180" y1="135" x2="180" y2="155" stroke="#F6C58A" stroke-width="4" />
                        <line x1="200" y1="130" x2="200" y2="150" stroke="#F6C58A" stroke-width="4" />

                        <path d="M 250 200 L 250 80 L 320 60 L 320 200" fill="#EAF4EE" />
                        <path d="M 320 60 L 370 80 L 370 200" fill="#D7E8DD" />
                        <path d="M 320 60 L 320 200" />
                        <line x1="270" y1="95" x2="300" y2="85" />
                        <line x1="270" y1="125" x2="300" y2="115" />
                        <line x1="270" y1="155" x2="300" y2="145" />
                        <line x1="270" y1="185" x2="300" y2="175" />

                        <path d="M 120 200 L 120 150 L 170 120 L 170 200" fill="#F8B195" />
                        <path d="M 170 120 L 210 140 L 210 200" fill="#F6C58A" />
                        <path d="M 170 120 L 170 200" />

                        <circle cx="100" cy="180" r="15" fill="white" />
                        <path d="M 100 195 L 100 200" />
                        <path d="M 100 180 L 95 175" />
                        <path d="M 100 180 L 105 175" />

                        <circle cx="310" cy="170" r="20" fill="white" />
                        <path d="M 310 190 L 310 200" />
                        <path d="M 310 170 L 300 160" />
                        <path d="M 310 170 L 320 160" />

                        <circle cx="350" cy="185" r="12" fill="white" />
                        <path d="M 350 197 L 350 200" />
                        <path d="M 350 185 L 345 180" />
                    </g>
                </svg>
            </div>
        </div>

        {{-- RIGHT PANEL (Form Login - Cream / Glass) --}}
        <div class="w-full md:w-1/2 bg-[#F9F6EE]/90 backdrop-blur-xl p-10 lg:p-14 flex flex-col justify-center relative">
            
            <div class="mb-8 transform transition-all duration-700 delay-200"
                 :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Masuk</h2>
                <p class="text-gray-600 text-sm">Selamat datang kembali. Silakan masukkan kredensial Anda.</p>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5 transform transition-all duration-700 delay-300"
                  :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email Kerja</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required
                               placeholder="nama@perusahaan.com"
                               value="{{ old('email') }}"
                               class="w-full pl-4 pr-4 py-3 bg-white/60 backdrop-blur-sm border border-white/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/50 focus:border-[#0B3D2E] focus:bg-white transition text-sm">
                    </div>
                    @error('email')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div x-data="{ show: false }">
                    <div class="mb-1">
                        <label for="password" class="block text-xs font-semibold text-gray-700">Kata Sandi</label>
                    </div>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-4 pr-10 py-3 bg-white/60 backdrop-blur-sm border border-white/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/50 focus:border-[#0B3D2E] focus:bg-white transition text-sm">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <span class="material-symbols-outlined text-[18px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                        </button>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <div>
                            @error('password')
                                <p class="text-rose-500 text-xs">{{ $message }}</p>
                            @enderror
                        </div>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-[#2B7A55] hover:text-[#0B3D2E] transition">Lupa kata sandi?</a>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                        class="w-full bg-[#0B3D2E] hover:bg-[#154A34] text-white font-medium py-3 rounded-full transition shadow-lg shadow-[#0B3D2E]/30 mt-4 text-sm">
                    Masuk
                </button>
            </form>

            <div class="text-center text-xs text-gray-600 mt-8 transform transition-all duration-700 delay-400"
                 :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                Belum punya akun perusahaan? 
                <a href="{{ route('register') }}" class="text-[#2B7A55] font-semibold hover:underline">
                    Daftar di sini
                </a>
            </div>

        </div>

    </div>

</body>
</html>