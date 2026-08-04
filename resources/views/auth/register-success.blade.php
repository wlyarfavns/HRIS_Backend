<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil - TalentaHR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Fraunces', serif; }
        .notch {
            position: absolute;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: #0d3b2e;
            left: -13px;
        }
        @media (max-width: 767px) {
            .perforation-v { display: none; }
            .perforation-h { display: block !important; }
        }
    </style>
</head>
<body class="bg-[#0d3b2e] min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-3xl bg-[#fffdf9] rounded-2xl shadow-2xl overflow-hidden flex flex-col md:flex-row relative">

        {{-- MAIN SECTION --}}
        <div class="w-full md:w-[64%] p-9 md:p-10">

            <div class="flex items-center gap-2 mb-8">
                <div class="w-8 h-8 rounded-lg bg-[#0d3b2e] flex items-center justify-center text-white shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4v18M13 21V11h6v10M9 9h.01M9 12h.01M9 15h.01" />
                    </svg>
                </div>
                <span class="text-sm font-semibold text-gray-900">TalentaHR</span>
            </div>

            <p class="text-xs font-semibold tracking-widest text-[#c9a15a] uppercase mb-3">
                Pendaftaran selesai
            </p>

            <h1 class="font-display text-4xl font-medium text-gray-900 leading-[1.1] mb-4">
                Akses admin Anda<br>sudah aktif.
            </h1>

            <p class="text-gray-600 text-[15px] leading-relaxed mb-8 max-w-sm">
                {{ $company_name ?? 'PT. Nama Perusahaan Anda' }} kini terdaftar di TalentaHR.
                Gunakan kredensial berikut untuk masuk dan mulai mengatur tim Anda.
            </p>

            <div class="space-y-4 mb-9">
                <div class="flex gap-3">
                    <span class="mt-0.5 w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center text-[10px] text-gray-400 shrink-0">1</span>
                    <p class="text-sm text-gray-700">Cek kotak masuk <span class="font-medium text-gray-900">{{ $email ?? 'admin@perusahaan.com' }}</span> untuk salinan detail akun.</p>
                </div>
                <div class="flex gap-3">
                    <span class="mt-0.5 w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center text-[10px] text-gray-400 shrink-0">2</span>
                    <p class="text-sm text-gray-700">Lengkapi profil perusahaan dan struktur tim Anda.</p>
                </div>
                <div class="flex gap-3">
                    <span class="mt-0.5 w-5 h-5 rounded-full border border-gray-300 flex items-center justify-center text-[10px] text-gray-400 shrink-0">3</span>
                    <p class="text-sm text-gray-700">Undang anggota HR pertama untuk mulai berkolaborasi.</p>
                </div>
            </div>

            <a href="{{ route('login') }}"
               class="inline-flex items-center justify-center bg-[#0d3b2e] hover:bg-[#0a2f24] text-white font-semibold py-3 px-7 text-sm rounded-lg transition">
                Masuk ke Dashboard
            </a>

            <p class="text-xs text-gray-400 mt-6">
                Ada kendala? <a href="#" class="text-[#0d3b2e] font-medium hover:underline">Hubungi support</a>
            </p>
        </div>

        {{-- PERFORATION (vertical, desktop) --}}
        <div class="perforation-v hidden md:block relative w-0 border-l-2 border-dashed border-gray-200">
            <div class="notch" style="top: -13px;"></div>
            <div class="notch" style="bottom: -13px;"></div>
        </div>

        {{-- PERFORATION (horizontal, mobile) --}}
        <div class="perforation-h hidden border-t-2 border-dashed border-gray-200 mx-9"></div>

        {{-- STUB SECTION --}}
        <div class="w-full md:w-[36%] bg-[#f8f6f0] p-8 flex flex-col justify-between">

            <div>
                <p class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase mb-1">Perusahaan</p>
                <p class="text-sm font-semibold text-gray-900 mb-5 leading-snug">
                    {{ $company_name ?? 'PT. Nama Perusahaan Anda' }}
                </p>

                <p class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase mb-1">Email admin</p>
                <p class="text-sm font-medium text-gray-800 mb-5 break-all">
                    {{ $email ?? 'admin@perusahaan.com' }}
                </p>

                <p class="text-[10px] font-semibold tracking-widest text-gray-400 uppercase mb-1">Terdaftar pada</p>
                <p class="text-sm font-medium text-gray-800">
                    {{ $registered_at ?? date('d M Y') }}
                </p>
            </div>

            <div class="flex items-center gap-2 mt-8">
                <span class="w-2 h-2 rounded-full bg-[#0d3b2e]"></span>
                <span class="text-xs font-medium text-gray-600">Akun aktif</span>
            </div>
        </div>
    </div>

</body>
</html>