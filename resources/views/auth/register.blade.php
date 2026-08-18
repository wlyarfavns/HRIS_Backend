<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Perusahaan - Platform HRIS</title>
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

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;  
            scrollbar-width: none;  
        }
    </style>
</head>
<body class="h-screen w-screen flex items-center justify-center p-4 sm:p-8">


    <div class="w-full max-w-[1200px] h-full max-h-[850px] bg-white rounded-[2rem] shadow-[0_30px_80px_-20px_rgba(8,122,82,0.25)] flex flex-col lg:flex-row overflow-hidden relative z-10 border border-white/50">


        <div class="hidden lg:flex lg:w-[45%] bg-[#0B3D2E] p-12 flex-col justify-center items-center relative">

            <div class="text-center mb-12 z-10">
                <h1 class="text-4xl font-bold text-white mb-2 tracking-tight">TalentaHR</h1>
                <p class="text-emerald-100/80 text-sm">Sistem Informasi SDM Terpadu</p>
            </div>


            <div class="w-full max-w-sm relative z-10">
                <svg viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-auto drop-shadow-2xl">

                    <path d="M80 200 C40 120, 100 60, 200 80 C300 100, 360 160, 320 240 C280 320, 120 280, 80 200 Z" fill="#065A3D" opacity="0.6"/>


                    <rect x="160" y="60" width="80" height="40" rx="8" fill="#FDFBF7" stroke="#043927" stroke-width="2"/>
                    <rect x="170" y="70" width="20" height="20" rx="10" fill="#0B3D2E" opacity="0.3"/>
                    <rect x="200" y="76" width="30" height="8" rx="4" fill="#0B3D2E"/>

                    <path d="M200 100 L200 130" stroke="#FDFBF7" stroke-width="2"/>
                    <path d="M110 130 L290 130" stroke="#FDFBF7" stroke-width="2"/>

                    <path d="M110 130 L110 150" stroke="#FDFBF7" stroke-width="2"/>
                    <path d="M200 130 L200 150" stroke="#FDFBF7" stroke-width="2"/>
                    <path d="M290 130 L290 150" stroke="#FDFBF7" stroke-width="2"/>

                    <rect x="70" y="150" width="80" height="40" rx="8" fill="#FDFBF7" stroke="#043927" stroke-width="2"/>
                    <rect x="80" y="160" width="20" height="20" rx="10" fill="#4ade80" opacity="0.8"/>
                    <rect x="110" y="166" width="30" height="8" rx="4" fill="#0B3D2E"/>

                    <rect x="160" y="150" width="80" height="40" rx="8" fill="#ffffff" stroke="#f59e0b" stroke-width="2"/>
                    <rect x="170" y="160" width="20" height="20" rx="10" fill="#0B3D2E" opacity="0.8"/>
                    <rect x="200" y="166" width="30" height="8" rx="4" fill="#0B3D2E" opacity="0.8"/>

                    <rect x="250" y="150" width="80" height="40" rx="8" fill="#FDFBF7" stroke="#043927" stroke-width="2"/>
                    <rect x="260" y="160" width="20" height="20" rx="10" fill="#f59e0b" opacity="0.8"/>
                    <rect x="290" y="166" width="30" height="8" rx="4" fill="#0B3D2E"/>


                    <circle cx="100" cy="80" r="12" fill="#4ade80" opacity="0.6"/>
                    <circle cx="300" cy="100" r="8" fill="#ffffff" opacity="0.4"/>
                    <rect x="250" y="240" width="60" height="20" rx="10" fill="#FDFBF7" stroke="#043927" stroke-width="2"/>
                    <circle cx="265" cy="250" r="4" fill="#ef4444"/>
                    <rect x="275" y="248" width="20" height="4" rx="2" fill="#0B3D2E"/>
                </svg>
            </div>

            <div class="mt-10 w-full max-w-sm relative z-10 space-y-4">
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">groups</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Struktur Organisasi Terpusat</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Kelola departemen, jabatan, dan data karyawan.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-[#065A3D]/40 p-3 rounded-lg border border-white/5">
                    <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-white text-[18px]">verified_user</span>
                    </div>
                    <div>
                        <h4 class="text-white text-sm font-semibold">Tingkat Keamanan Tinggi</h4>
                        <p class="text-emerald-100/70 text-xs mt-0.5">Data perusahaan Anda dienkripsi dan dijamin aman.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="w-full lg:w-[55%] p-10 md:p-14 flex flex-col bg-white relative overflow-y-auto no-scrollbar">


            <div class="lg:hidden flex items-center gap-2 text-[#0B3D2E] mb-8">
                <span class="text-xl font-bold tracking-tight">TalentaHR</span>
            </div>

            <div class="mb-8 border-b border-gray-100 pb-6 shrink-0 mt-auto">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Pendaftaran Perusahaan</h2>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Bergabunglah dengan TalentaHR. Buat akun admin pertama untuk perusahaan Anda dan mulailah mendigitalisasi proses HR, absensi, hingga penggajian dengan mudah.
                </p>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-8 shrink-0">
                @csrf


                <div class="space-y-5">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-6 h-6 rounded-md bg-[#0B3D2E]/10 text-[#0B3D2E] flex items-center justify-center text-xs font-bold">1</span>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Data Perusahaan</h3>
                    </div>

                    <div>
                        <label for="company_name" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Nama Perusahaan</label>
                        <input type="text" id="company_name" name="company_name" required
                               placeholder="PT. Nama Perusahaan Anda"
                               value="{{ old('company_name') }}"
                               class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm shadow-sm">
                        @error('company_name')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div>
                            <label for="city" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Kota / Kab.</label>
                            <select id="city" name="city" required
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm appearance-none shadow-sm cursor-pointer">
                                <option value="" disabled {{ old('city') ? '' : 'selected' }}>Pilih Kota</option>
                                <option value="Yogyakarta" {{ old('city') == 'Yogyakarta' ? 'selected' : '' }}>Yogyakarta</option>
                                <option value="Jakarta Selatan" {{ old('city') == 'Jakarta Selatan' ? 'selected' : '' }}>Jakarta Selatan</option>
                                <option value="Surabaya" {{ old('city') == 'Surabaya' ? 'selected' : '' }}>Surabaya</option>
                                <option value="Bandung" {{ old('city') == 'Bandung' ? 'selected' : '' }}>Bandung</option>
                            </select>
                        </div>
                        <div>
                            <label for="province" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Provinsi</label>
                            <select id="province" name="province" required
                                    class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm appearance-none shadow-sm cursor-pointer">
                                <option value="" disabled {{ old('province') ? '' : 'selected' }}>Pilih Provinsi</option>
                                <option value="DI Yogyakarta" {{ old('province') == 'DI Yogyakarta' ? 'selected' : '' }}>DI Yogyakarta</option>
                                <option value="DKI Jakarta" {{ old('province') == 'DKI Jakarta' ? 'selected' : '' }}>DKI Jakarta</option>
                                <option value="Jawa Barat" {{ old('province') == 'Jawa Barat' ? 'selected' : '' }}>Jawa Barat</option>
                            </select>
                        </div>
                        <div>
                            <label for="postal_code" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Kode Pos</label>
                            <input type="text" id="postal_code" name="postal_code" required
                                   inputmode="numeric" pattern="[0-9]*" maxlength="5"
                                   placeholder="12345"
                                   value="{{ old('postal_code') }}"
                                   class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm shadow-sm">
                        </div>
                    </div>
                </div>


                <div class="space-y-5 pt-4">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="w-6 h-6 rounded-md bg-[#0B3D2E]/10 text-[#0B3D2E] flex items-center justify-center text-xs font-bold">2</span>
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-widest">Akun Admin</h3>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Alamat Email</label>
                        <input type="email" id="email" name="email" required
                               placeholder="admin@perusahaan.com"
                               value="{{ old('email') }}"
                               class="w-full px-5 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm shadow-sm">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div x-data="{ show: false }">
                            <label for="password" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Kata Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" id="password" name="password" required
                                       placeholder="••••••••"
                                       class="w-full pl-5 pr-12 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm tracking-widest shadow-sm">
                                <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#0B3D2E] transition">
                                    <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                        <div x-data="{ show: false }">
                            <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 uppercase tracking-widest mb-2">Konfirmasi Sandi</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                                       placeholder="••••••••"
                                       class="w-full pl-5 pr-12 py-4 bg-gray-50/50 border border-gray-200 rounded-lg text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B3D2E] focus:ring-1 focus:ring-[#0B3D2E] focus:bg-white transition text-sm tracking-widest shadow-sm">
                                <button type="button" @click="show = !show"
                                        class="absolute inset-y-0 right-4 flex items-center text-gray-400 hover:text-[#0B3D2E] transition">
                                    <span class="material-symbols-outlined text-[20px]" x-text="show ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 mb-8">
                    <button type="submit"
                            class="w-full bg-[#0B3D2E] hover:bg-[#065A3D] text-white font-bold py-4 rounded-lg transition text-sm flex justify-center items-center gap-2 group shadow-md">
                        Daftarkan Perusahaan
                        <span class="material-symbols-outlined text-[18px] transform group-hover:translate-x-1 transition">arrow_forward</span>
                    </button>
                </div>

                <div class="text-center text-sm text-gray-500 mt-auto pb-4 shrink-0">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="text-[#0B3D2E] font-semibold hover:underline transition ml-1">
                        Masuk di sini
                    </a>
                </div>

            </form>

        </div>
    </div>

</body>
</html>
