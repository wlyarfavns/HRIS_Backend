@props([
    'userData' => [
        'name' => 'Sarah Johnson',
        'email' => 'sarah.johnson@talentahr.co.id',
        'phone' => '0812-3456-7890',
        'role' => 'HR Admin',
        'department' => 'Human Resources',
        'position' => 'Senior HR Specialist',
        'nip' => 'EMP-2024-001',
        'avatar' => 'https://i.pravatar.cc/150?img=47',
        'join_date' => '15 Januari 2022',
        'gender' => 'Perempuan',
        'birth_place' => 'Jakarta',
        'birth_date' => '1995-04-12',
        'address' => 'Jl. Sudirman No. 45, Jakarta Selatan',
    ]
])

<div x-data="{
    isEditingProfile: false,
    isEditingPassword: false,
    isSavingProfile: false,
    isSavingPassword: false,
    toast: { show: false, message: '', type: 'success' },
    avatarPreview: '{{ $userData['avatar'] }}',
    showPasswordCurrent: false,
    showPasswordNew: false,
    showPasswordConfirm: false,
    
    // Backup data for profile cancel operation
    originalProfile: {
        name: '{{ $userData['name'] }}',
        email: '{{ $userData['email'] }}',
        phone: '{{ $userData['phone'] }}',
        gender: '{{ $userData['gender'] }}',
        birth_place: '{{ $userData['birth_place'] }}',
        birth_date: '{{ $userData['birth_date'] }}',
        address: '{{ $userData['address'] }}'
    },
    
    profile: {
        name: '{{ $userData['name'] }}',
        email: '{{ $userData['email'] }}',
        phone: '{{ $userData['phone'] }}',
        gender: '{{ $userData['gender'] }}',
        birth_place: '{{ $userData['birth_place'] }}',
        birth_date: '{{ $userData['birth_date'] }}',
        address: '{{ $userData['address'] }}'
    },
    
    passwordForm: {
        current: '',
        new: '',
        confirm: ''
    },

    cancelEditProfile() {
        this.profile = JSON.parse(JSON.stringify(this.originalProfile));
        this.isEditingProfile = false;
    },

    cancelEditPassword() {
        this.passwordForm = { current: '', new: '', confirm: '' };
        this.isEditingPassword = false;
    },

    handleAvatarChange(event) {
        if (!this.isEditingProfile) return;
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.avatarPreview = e.target.result;
                this.triggerToast('Foto profil dipilih! Klik Simpan Profil untuk menerapkan.', 'info');
            };
            reader.readAsDataURL(file);
        }
    },

    saveProfile() {
        this.isSavingProfile = true;
        setTimeout(() => {
            this.isSavingProfile = false;
            this.isEditingProfile = false;
            this.originalProfile = JSON.parse(JSON.stringify(this.profile));
            this.triggerToast('Data profil berhasil diperbarui!');
        }, 700);
    },

    savePassword() {
        if (!this.passwordForm.current || !this.passwordForm.new || !this.passwordForm.confirm) {
            this.triggerToast('Harap isi semua kolom kata sandi!', 'error');
            return;
        }
        if (this.passwordForm.new !== this.passwordForm.confirm) {
            this.triggerToast('Konfirmasi kata sandi baru tidak cocok!', 'error');
            return;
        }
        
        this.isSavingPassword = true;
        setTimeout(() => {
            this.isSavingPassword = false;
            this.isEditingPassword = false;
            this.passwordForm = { current: '', new: '', confirm: '' };
            this.triggerToast('Kata sandi berhasil diperbarui!');
        }, 700);
    },

    triggerToast(message, type = 'success') {
        this.toast.message = message;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => {
            this.toast.show = false;
        }, 3500);
    }
}" class="space-y-6">

    <!-- HEADER PROFILE CARD (PURE WHITE - NO TABS) -->
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <!-- Avatar Picture -->
                <div class="relative group shrink-0">
                    <img :src="avatarPreview" alt="Foto Profil" class="w-20 h-20 rounded-2xl object-cover shadow-sm bg-surface border border-black/10">
                    
                    <template x-if="isEditingProfile">
                        <label for="avatarInput" class="absolute inset-0 rounded-2xl bg-black/50 transition-opacity flex items-center justify-center cursor-pointer text-white">
                            <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        </label>
                    </template>
                    <input type="file" id="avatarInput" accept="image/*" class="hidden" @change="handleAvatarChange" :disabled="!isEditingProfile">
                    
                    <template x-if="isEditingProfile">
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-primary text-white rounded-full flex items-center justify-center text-[10px] ring-2 ring-white pointer-events-none" title="Ubah Foto">
                            <span class="material-symbols-outlined text-xs">edit</span>
                        </span>
                    </template>
                </div>

                <!-- User Header Info -->
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl font-bold text-on-surface" x-text="profile.name"></h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                            {{ $userData['role'] }}
                        </span>
                    </div>
                    <p class="text-sm text-on-surface-variant/70 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant/60">badge</span>
                        NIP: <span class="font-semibold text-on-surface">{{ $userData['nip'] }}</span>
                        <span class="text-black/20">•</span>
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant/60">domain</span>
                        {{ $userData['department'] }}
                    </p>
                </div>
            </div>

            <!-- Akun Aktif Badge -->
            <div class="self-start md:self-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Akun Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- MAIN SINGLE-PAGE GRID CONTENT -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Personal Info & Security -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Card 1: Detail Data Diri & Kontak (SEPARATE EDIT PROFILES) -->
            <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between gap-4 border-b border-black/5 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">person_outline</span>
                            <span x-text="isEditingProfile ? 'Edit Data Diri & Kontak' : 'Detail Data Diri & Kontak'"></span>
                        </h3>
                        <p class="text-xs text-on-surface-variant/60 mt-0.5" x-text="isEditingProfile ? 'Perbarui informasi kontak dan profil pribadi Anda di bawah ini.' : 'Informasi profil dan kontak pribadi Anda yang terdaftar pada sistem.'"></p>
                    </div>

                    <!-- EDIT PROFIL ACTION BUTTONS -->
                    <div class="shrink-0">
                        <template x-if="!isEditingProfile">
                            <button @click="isEditingProfile = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold text-xs transition shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                Edit Profil
                            </button>
                        </template>

                        <template x-if="isEditingProfile">
                            <div class="flex items-center gap-2">
                                <button @click="cancelEditProfile()" class="px-3.5 py-2 rounded-xl border border-black/10 text-on-surface-variant hover:bg-black/5 text-xs font-medium transition">
                                    Batal
                                </button>
                                <button @click="saveProfile()" :disabled="isSavingProfile" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm disabled:opacity-50">
                                    <template x-if="!isSavingProfile">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                            Simpan Profil
                                        </span>
                                    </template>
                                    <template x-if="isSavingProfile">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Menyimpan...
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- VIEW MODE (READ-ONLY DETAILS) -->
                <template x-if="!isEditingProfile">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Nama Lengkap</span>
                            <p class="text-sm font-bold text-on-surface" x-text="profile.name"></p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Alamat Email</span>
                            <p class="text-sm font-bold text-on-surface" x-text="profile.email"></p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Nomor Telepon / WA</span>
                            <p class="text-sm font-bold text-on-surface" x-text="profile.phone"></p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Jenis Kelamin</span>
                            <p class="text-sm font-bold text-on-surface" x-text="profile.gender"></p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Tempat Lahir</span>
                            <p class="text-sm font-bold text-on-surface" x-text="profile.birth_place"></p>
                        </div>

                        <div class="p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Tanggal Lahir</span>
                            <p class="text-sm font-bold text-on-surface" x-text="profile.birth_date"></p>
                        </div>

                        <div class="md:col-span-2 p-3.5 rounded-xl bg-surface-variant/10 border border-black/5 space-y-1">
                            <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide">Alamat Domisili Lengkap</span>
                            <p class="text-sm font-medium text-on-surface leading-relaxed" x-text="profile.address"></p>
                        </div>
                    </div>
                </template>

                <!-- EDIT MODE (FORM INPUTS) -->
                <template x-if="isEditingProfile">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nama Lengkap</label>
                            <input type="text" x-model="profile.name" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Alamat Email</label>
                            <input type="email" x-model="profile.email" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Nomor Telepon / WA</label>
                            <input type="text" x-model="profile.phone" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Jenis Kelamin</label>
                            <select x-model="profile.gender" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition">
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Tempat Lahir</label>
                            <input type="text" x-model="profile.birth_place" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Tanggal Lahir</label>
                            <input type="date" x-model="profile.birth_date" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Alamat Domisili Lengkap</label>
                            <textarea x-model="profile.address" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition"></textarea>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Card 2: Keamanan & Password (SEPARATE EDIT PASSWORD) -->
            <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between gap-4 border-b border-black/5 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">vpn_key</span>
                            Keamanan & Password
                        </h3>
                        <p class="text-xs text-on-surface-variant/60 mt-0.5">Kelola kata sandi akun untuk menjaga keamanan akses Anda.</p>
                    </div>

                    <!-- UBAH PASSWORD ACTION BUTTONS -->
                    <div class="shrink-0">
                        <template x-if="!isEditingPassword">
                            <button @click="isEditingPassword = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold text-xs transition shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                                Edit Password
                            </button>
                        </template>

                        <template x-if="isEditingPassword">
                            <div class="flex items-center gap-2">
                                <button @click="cancelEditPassword()" class="px-3.5 py-2 rounded-xl border border-black/10 text-on-surface-variant hover:bg-black/5 text-xs font-medium transition">
                                    Batal
                                </button>
                                <button @click="savePassword()" :disabled="isSavingPassword" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs transition shadow-sm disabled:opacity-50">
                                    <template x-if="!isSavingPassword">
                                        <span class="flex items-center gap-1.5">
                                            <span class="material-symbols-outlined text-[16px]">check</span>
                                            Simpan Password
                                        </span>
                                    </template>
                                    <template x-if="isSavingPassword">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="animate-spin h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            Menyimpan...
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- VIEW MODE -->
                <template x-if="!isEditingPassword">
                    <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl text-on-surface-variant/60">lock</span>
                            <div>
                                <p class="text-sm font-bold text-on-surface">Kata Sandi Akun</p>
                                <p class="text-xs text-on-surface-variant/60">Terakhir diperbarui 30 hari yang lalu</p>
                            </div>
                        </div>
                        <span class="text-sm font-mono tracking-widest text-on-surface-variant/60">••••••••••••</span>
                    </div>
                </template>

                <!-- EDIT MODE -->
                <template x-if="isEditingPassword">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kata Sandi Saat Ini</label>
                            <div class="relative">
                                <input :type="showPasswordCurrent ? 'text' : 'password'" x-model="passwordForm.current" placeholder="Masukkan kata sandi lama" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition pr-10">
                                <button type="button" @click="showPasswordCurrent = !showPasswordCurrent" class="absolute right-3 top-2.5 text-on-surface-variant/50 hover:text-primary transition">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showPasswordCurrent ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showPasswordNew ? 'text' : 'password'" x-model="passwordForm.new" placeholder="Minimal 8 karakter" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition pr-10">
                                <button type="button" @click="showPasswordNew = !showPasswordNew" class="absolute right-3 top-2.5 text-on-surface-variant/50 hover:text-primary transition">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showPasswordNew ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Konfirmasi Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showPasswordConfirm ? 'text' : 'password'" x-model="passwordForm.confirm" placeholder="Ulangi kata sandi baru" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition pr-10">
                                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute right-3 top-2.5 text-on-surface-variant/50 hover:text-primary transition">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showPasswordConfirm ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right Column: Readonly Employment Details Card -->
        <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-6 h-fit">
            <div class="border-b border-black/5 pb-4">
                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">work_outline</span>
                    Informasi Pekerjaan
                </h3>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Detail kepegawaian resmi pada sistem HRIS.</p>
            </div>

            <div class="space-y-3.5">
                <div class="p-3.5 rounded-xl bg-surface-variant/15 border border-black/5">
                    <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide block mb-0.5">Nomor Induk Pegawai (NIP)</span>
                    <span class="text-sm font-bold text-on-surface">{{ $userData['nip'] }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-surface-variant/15 border border-black/5">
                    <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide block mb-0.5">Departemen / Divisi</span>
                    <span class="text-sm font-bold text-on-surface">{{ $userData['department'] }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-surface-variant/15 border border-black/5">
                    <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide block mb-0.5">Jabatan / Role</span>
                    <span class="text-sm font-bold text-on-surface">{{ $userData['position'] }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-surface-variant/15 border border-black/5">
                    <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide block mb-0.5">Tanggal Bergabung</span>
                    <span class="text-sm font-bold text-on-surface">{{ $userData['join_date'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- TOAST NOTIFICATION (THEME-MATCHED DEEP EMERALD) -->
    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-white font-medium text-xs border border-emerald-500/30 backdrop-blur-md"
         :class="{
             'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
             'bg-rose-950 border-rose-500/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-xs font-semibold"></span>
    </div>

</div>
