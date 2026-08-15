<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi - TalentaHR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=IBM+Plex+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }

            33% {
                transform: translate(30px, -50px) scale(1.1);
            }

            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }

            100% {
                transform: translate(0px, 0px) scale(1);
            }
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
    <div
        class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-[#2B7A55] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob">
    </div>
    <div
        class="absolute top-[20%] right-[-10%] w-96 h-96 bg-[#1D5F42] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-2000">
    </div>
    <div
        class="absolute bottom-[-20%] left-[20%] w-96 h-96 bg-[#5C7166] rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-4000">
    </div>

    <div x-data="{ mounted: false }" x-init="setTimeout(() => mounted = true, 100)"
        class="w-full max-w-5xl rounded-[2rem] overflow-hidden flex flex-col md:flex-row-reverse shadow-2xl relative z-10 border border-white/20 transform transition-all duration-1000"
        :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-12 opacity-0'">

        {{-- LEFT PANEL / FORM (Cream / Glass) --}}
        <div
            class="w-full md:w-1/2 bg-[#F9F6EE]/90 backdrop-blur-xl p-10 lg:p-14 flex flex-col justify-center relative">

            <div class="flex flex-col mb-10 transform transition-all duration-700 delay-100"
                :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                <h1 class="text-2xl font-extrabold text-[#0B3D2E] tracking-tight">HRIS System</h1>
                <span class="text-[11px] font-bold text-gray-400 tracking-[0.15em] uppercase mt-0.5">TalentaHR</span>
            </div>

            <div class="mb-8 transform transition-all duration-700 delay-200"
                :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                <h2 class="text-3xl font-bold text-gray-900 mb-2">Pemulihan Akun</h2>
                <p class="text-gray-600 text-sm leading-relaxed">Masukkan alamat email kerja Anda dan kami akan
                    mengirimkan tautan untuk mengatur ulang kata sandi.</p>
            </div>

            {{-- Notifikasi Sukses --}}
            @if (session('status'))
                <div
                    class="p-4 mb-4 text-sm text-[#0B3D2E] bg-[#D7E8DD] border border-[#2B7A55] rounded-xl flex items-center gap-2">
                    <span class="material-symbols-outlined text-xl">mark_email_read</span>
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}"
                class="space-y-6 transform transition-all duration-700 delay-300"
                :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                @csrf

                {{-- EMAIL (Tambahkan @error untuk pesan gagal) --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-700 mb-1">Email Kerja <span
                            class="text-[#0B3D2E]">*</span></label>
                    <div class="relative">
                        <input type="email" id="email" name="email" required value="{{ old('email') }}"
                            placeholder="nama@perusahaan.com"
                            class="w-full pl-4 pr-4 py-3 bg-white/60 backdrop-blur-sm border border-white/50 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0B3D2E]/50 focus:border-[#0B3D2E] focus:bg-white transition text-sm">
                    </div>
                    @error('email')
                        <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-[#0B3D2E] hover:bg-[#154A34] text-white font-medium py-3 rounded-full transition shadow-lg shadow-[#0B3D2E]/30 mt-2">
                    Kirim Tautan Pemulihan
                </button>
            </form>

            <div class="text-center text-xs text-gray-600 mt-8 transform transition-all duration-700 delay-400"
                :class="mounted ? 'translate-y-0 opacity-100' : 'translate-y-4 opacity-0'">
                Teringat kata sandi Anda?
                <a href="{{ route('login') }}" class="text-[#2B7A55] font-semibold hover:underline">
                    Kembali ke halaman masuk
                </a>
            </div>

        </div>

        {{-- RIGHT PANEL / ILLUSTRATION (White / Glass) --}}
        <div
            class="w-full md:w-1/2 bg-white/80 backdrop-blur-xl p-10 lg:p-14 flex flex-col justify-between relative overflow-hidden hidden md:flex">

            {{-- Quote Section --}}
            <div class="relative z-10 transform transition-all duration-700 delay-300"
                :class="mounted ? 'translate-x-0 opacity-100' : 'translate-x-8 opacity-0'">
                <span class="material-symbols-outlined text-4xl text-[#0B3D2E] mb-4 block">lock_reset</span>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Keamanan Terjamin</h3>
                <p class="text-base font-medium text-gray-600 leading-relaxed mb-6">
                    Kami melindungi data perusahaan Anda dengan keamanan tingkat lanjut. Proses pemulihan kata sandi
                    didesain untuk memastikan hanya pihak berwenang yang dapat mengakses akun.
                </p>
            </div>

            {{-- Mockup Icon / Illustration (Buildings) --}}
            <div class="absolute bottom-[-10%] right-[-10%] w-[120%] h-[60%] opacity-80 pointer-events-none transform transition-all duration-1000 delay-500 origin-bottom"
                :class="mounted ? 'scale-100 opacity-80' : 'scale-90 opacity-0'">
                <svg viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg"
                    class="w-full h-full drop-shadow-xl text-[#0B3D2E]">
                    <!-- Simple abstract buildings with strokes -->
                    <g stroke="currentColor" stroke-width="3" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <!-- Building 1 (Left) -->
                        <path d="M 50 200 L 50 120 L 100 100 L 100 200" fill="#D7E8DD" />
                        <path d="M 100 100 L 150 120 L 150 200" fill="#EAF4EE" />
                        <path d="M 100 100 L 100 200" />
                        <!-- Lines -->
                        <line x1="60" y1="130" x2="80" y2="122" />
                        <line x1="60" y1="150" x2="80" y2="142" />
                        <line x1="60" y1="170" x2="80" y2="162" />

                        <!-- Building 2 (Center Tall) -->
                        <path d="M 140 200 L 140 50 L 220 20 L 220 200" fill="white" />
                        <path d="M 220 20 L 260 40 L 260 200" fill="#F9F6EE" />
                        <path d="M 220 20 L 220 200" />
                        <!-- Windows -->
                        <line x1="160" y1="60" x2="160" y2="80" stroke="#F6C58A" stroke-width="4" />
                        <line x1="180" y1="55" x2="180" y2="75" stroke="#F6C58A" stroke-width="4" />
                        <line x1="200" y1="50" x2="200" y2="70" stroke="#F6C58A" stroke-width="4" />

                        <line x1="160" y1="100" x2="160" y2="120" stroke="#F6C58A" stroke-width="4" />
                        <line x1="180" y1="95" x2="180" y2="115" stroke="#F6C58A" stroke-width="4" />
                        <line x1="200" y1="90" x2="200" y2="110" stroke="#F6C58A" stroke-width="4" />

                        <line x1="160" y1="140" x2="160" y2="160" stroke="#F6C58A" stroke-width="4" />
                        <line x1="180" y1="135" x2="180" y2="155" stroke="#F6C58A" stroke-width="4" />
                        <line x1="200" y1="130" x2="200" y2="150" stroke="#F6C58A" stroke-width="4" />

                        <!-- Building 3 (Right) -->
                        <path d="M 250 200 L 250 80 L 320 60 L 320 200" fill="#EAF4EE" />
                        <path d="M 320 60 L 370 80 L 370 200" fill="#D7E8DD" />
                        <path d="M 320 60 L 320 200" />

                        <line x1="270" y1="95" x2="300" y2="85" />
                        <line x1="270" y1="125" x2="300" y2="115" />
                        <line x1="270" y1="155" x2="300" y2="145" />
                        <line x1="270" y1="185" x2="300" y2="175" />

                        <!-- Building 4 (Bottom Front) -->
                        <path d="M 120 200 L 120 150 L 170 120 L 170 200" fill="#F8B195" />
                        <path d="M 170 120 L 210 140 L 210 200" fill="#F6C58A" />
                        <path d="M 170 120 L 170 200" />

                        <!-- Trees -->
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

    </div>

</body>

</html>