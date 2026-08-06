<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TalentaHR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#0d3b2e] min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-5xl bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

        {{-- LEFT PANEL --}}
        <div class="w-full md:w-1/2 bg-[#f1efe9] p-10 flex flex-col justify-between relative">
            <div>
                <div class="rounded-xl overflow-hidden shadow-md mb-6">
                    <img src="https://placehold.co/500x300/0d3b2e/ffffff?text=TalentaHR+Team"
                         alt="Ilustrasi Tim TalentaHR"
                         class="w-full h-56 object-cover">
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">Khusus Akses Internal</h2>
                <p class="text-gray-600 leading-relaxed">
                    Memberdayakan tim kami dengan manajemen SDM terpusat,
                    pelacakan penggajian, dan wawasan kinerja.
                </p>
            </div>

            
        </div>

        {{-- RIGHT PANEL --}}
        <div class="w-full md:w-1/2 p-10 flex flex-col justify-center">

            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-lg bg-[#0d3b2e] flex items-center justify-center text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900">TalentaHR</h1>
            </div>

            <p class="text-gray-600 mb-8">Selamat datang kembali. Silakan masukkan kredensial Anda.</p>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-800 mb-1">Email Kerja</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" required
                               placeholder="nama@perusahaan.com"
                               value="{{ old('email') }}"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                    </div>
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-gray-800 mb-1">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input :type="show ? 'text' : 'password'" id="password" name="password" required
                               placeholder="••••••••"
                               class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                        <button type="button" @click="show = !show"
                                class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror

                    <div class="text-right mt-2">
                        <a href="#" class="text-sm font-medium text-[#0d3b2e] hover:underline">
                            Lupa kata sandi?
                        </a>
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                        class="w-full bg-[#0d3b2e] hover:bg-[#0a2f24] text-white font-semibold py-3 rounded-lg transition">
                    Masuk
                </button>
            </form>

            <div class="text-center text-sm text-gray-600 mt-6">
                Belum punya akun perusahaan?
                <a href="{{ route('register') }}" class="text-[#0d3b2e] font-semibold hover:underline">
                    Daftar di sini
                </a>
            </div>

            <hr class="my-6 border-gray-200">

            <div class="text-center text-sm text-gray-500 space-y-2">
                <p class="text-xs">
                    © {{ date('Y') }} TalentaHR Inc.
                    <a href="#" class="hover:underline">Privasi</a> ·
                    <a href="#" class="hover:underline">Keamanan</a>
                </p>
            </div>
        </div>
    </div>

</body>
</html>