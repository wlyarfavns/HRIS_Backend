<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Perusahaan - TalentaHR</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-[#0d3b2e] h-screen overflow-hidden flex items-center justify-center p-4">

    <div class="w-full max-w-6xl h-[92vh] max-h-[720px] bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row">

        {{-- LEFT PANEL (CREAM) --}}
        <div class="w-full md:w-[32%] bg-[#f1efe9] p-6 flex flex-col justify-start shrink-0">
            <div class="rounded-xl overflow-hidden shadow-md mb-4">
                <img src="https://placehold.co/500x260/0d3b2e/ffffff?text=TalentaHR+Company"
                     alt="Ilustrasi Registrasi Perusahaan TalentaHR"
                     class="w-full h-32 object-cover">
            </div>

            <h2 class="text-xl font-bold text-gray-900 mb-2 leading-tight">
                Daftarkan<br>Perusahaan Anda
            </h2>
            <p class="text-gray-600 text-sm leading-relaxed">
                Kelola HR, administrasi payroll, hingga performa karyawan
                dengan lebih mudah, akurat, dan terintegrasi dalam satu
                platform modern.
            </p>
        </div>

        {{-- RIGHT PANEL (FORM) --}}
        <div class="w-full md:w-[68%] p-8 flex flex-col justify-center">

            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-lg bg-[#0d3b2e] flex items-center justify-center text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                    </svg>
                </div>
                <h1 class="text-lg font-bold text-gray-900">TalentaHR</h1>
            </div>

            <p class="text-gray-600 text-sm mb-4">
                Lengkapi data perusahaan Anda untuk membuat akun admin
            </p>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-3">
                @csrf

                {{-- NAMA PERUSAHAAN --}}
                <div>
                    <label for="company_name" class="block text-xs font-semibold text-gray-800 mb-1">Nama Perusahaan</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                            </svg>
                        </span>
                        <input type="text" id="company_name" name="company_name" required
                               placeholder="PT. Nama Perusahaan Anda"
                               value="{{ old('company_name') }}"
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                    </div>
                    @error('company_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- KOTA, PROVINSI, KODE POS --}}
                <div class="grid grid-cols-3 gap-3">
                    {{-- KOTA / KABUPATEN --}}
                    <div>
                        <label for="city" class="block text-xs font-semibold text-gray-800 mb-1">Kota / Kabupaten</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <select id="city" name="city" required
                                    class="w-full pl-9 pr-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent appearance-none bg-white">
                                <option value="" disabled {{ old('city') ? '' : 'selected' }}>Kota</option>
                                <option value="Yogyakarta" {{ old('city') == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                                <option value="Bantul" {{ old('city') == 'Bantul' ? 'selected' : '' }}>Bantul</option>
                                <option value="Sleman" {{ old('city') == 'Sleman' ? 'selected' : '' }}>Sleman</option>
                                <option value="Jakarta Selatan" {{ old('city') == 'Jakarta Selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
                                <option value="Surabaya" {{ old('city') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                <option value="Bandung" {{ old('city') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                            </select>
                        </div>
                        @error('city')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PROVINSI --}}
                    <div>
                        <label for="province" class="block text-xs font-semibold text-gray-800 mb-1">Provinsi</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                </svg>
                            </span>
                            <select id="province" name="province" required
                                    class="w-full pl-9 pr-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent appearance-none bg-white">
                                <option value="" disabled {{ old('province') ? '' : 'selected' }}>Provinsi</option>
                                <option value="DI Yogyakarta" {{ old('province') == 'DI Yogyakarta' ? 'selected' : '' }}>DI Yogyakarta</option>
                                <option value="DKI Jakarta" {{ old('province') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                                <option value="Jawa Barat" {{ old('province') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                                <option value="Jawa Tengah" {{ old('province') == 'Jawa Tengah' ? 'selected' : '' }}>Jawa Tengah</option>
                                <option value="Jawa Timur" {{ old('province') == 'Jawa Timur' ? 'selected' : '' }}>Jawa Timur</option>
                            </select>
                        </div>
                        @error('province')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KODE POS --}}
                    <div>
                        <label for="postal_code" class="block text-xs font-semibold text-gray-800 mb-1">Kode Pos</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </span>
                            <input type="text" id="postal_code" name="postal_code" required
                                   inputmode="numeric" pattern="[0-9]*" maxlength="5"
                                   placeholder="12345"
                                   value="{{ old('postal_code') }}"
                                   class="w-full pl-9 pr-2 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                        </div>
                        @error('postal_code')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="block text-xs font-semibold text-gray-800 mb-1">Alamat Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </span>
                        <input type="email" id="email" name="email" required
                               placeholder="admin@perusahaan.com"
                               value="{{ old('email') }}"
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                    </div>
                    <p class="text-[11px] text-gray-500 mt-1">Email ini akan digunakan sebagai nama akun untuk masuk (login)</p>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- PASSWORD & KONFIRMASI --}}
                <div class="grid grid-cols-2 gap-3">
                    {{-- PASSWORD --}}
                    <div x-data="{ show: false }">
                        <label for="password" class="block text-xs font-semibold text-gray-800 mb-1">Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                   placeholder="••••••••"
                                   class="w-full pl-9 pr-9 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- KONFIRMASI PASSWORD --}}
                    <div x-data="{ show: false }">
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-800 mb-1">Konfirmasi Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                   placeholder="••••••••"
                                   class="w-full pl-9 pr-9 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0d3b2e] focus:border-transparent">
                            <button type="button" @click="show = !show"
                                    class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- SUBMIT --}}
                <button type="submit"
                        class="w-full bg-[#0d3b2e] hover:bg-[#0a2f24] text-white font-semibold py-2.5 text-sm rounded-lg transition mt-1">
                    Daftar Perusahaan
                </button>
            </form>

            <div class="text-center text-xs text-gray-600 mt-3 pt-3 border-t border-gray-200">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-[#0d3b2e] font-semibold hover:underline">
                    Masuk di sini
                </a>
            </div>

            <p class="text-center text-[11px] text-gray-400 mt-1">
                © {{ date('Y') }} TalentaHR. All rights reserved.
            </p>
        </div>
    </div>

</body>
</html>