@props([
    'userData' => [],
    'updateProfileUrl' => '',
    'updatePasswordUrl' => ''
])

<div x-data="{
    isEditingProfile: false,
    isEditingPassword: false,
    isSavingProfile: false,
    isSavingPassword: false,
    toast: { show: false, message: '', type: 'success' },
    avatarPreview: '{{ $userData['avatar'] ?? '' }}',
    showPasswordCurrent: false,
    showPasswordNew: false,
    showPasswordConfirm: false,

    updateProfileUrl: '{{ $updateProfileUrl }}',
    updatePasswordUrl: '{{ $updatePasswordUrl }}',

    originalProfile: {
        name: '{{ $userData['name'] ?? '' }}',
        email: '{{ $userData['email'] ?? '' }}'
    },

    profile: {
        name: '{{ $userData['name'] ?? '' }}',
        email: '{{ $userData['email'] ?? '' }}'
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

    async saveProfile() {
        if (!this.updateProfileUrl) {
            this.triggerToast('Endpoint update profil belum dikonfigurasi.', 'error');
            return;
        }

        this.isSavingProfile = true;
        try {
            const res = await fetch(this.updateProfileUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify(this.profile),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
                this.triggerToast(firstError || data?.message || 'Gagal menyimpan profil.', 'error');
                return;
            }

            this.isEditingProfile = false;
            this.originalProfile = JSON.parse(JSON.stringify(this.profile));
            this.triggerToast(data.message || 'Data profil berhasil diperbarui!');
        } catch (e) {
            this.triggerToast('Terjadi kesalahan jaringan. Coba lagi.', 'error');
        } finally {
            this.isSavingProfile = false;
        }
    },

    async savePassword() {
        if (!this.passwordForm.current || !this.passwordForm.new || !this.passwordForm.confirm) {
            this.triggerToast('Harap isi semua kolom kata sandi!', 'error');
            return;
        }
        if (this.passwordForm.new !== this.passwordForm.confirm) {
            this.triggerToast('Konfirmasi kata sandi baru tidak cocok!', 'error');
            return;
        }
        if (!this.updatePasswordUrl) {
            this.triggerToast('Endpoint update password belum dikonfigurasi.', 'error');
            return;
        }

        this.isSavingPassword = true;
        try {
            const res = await fetch(this.updatePasswordUrl, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
                body: JSON.stringify({
                    current_password: this.passwordForm.current,
                    new_password: this.passwordForm.new,
                    new_password_confirmation: this.passwordForm.confirm,
                }),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                const firstError = data?.errors ? Object.values(data.errors)[0]?.[0] : null;
                this.triggerToast(firstError || data?.message || 'Gagal mengubah kata sandi.', 'error');
                return;
            }

            this.isEditingPassword = false;
            this.passwordForm = { current: '', new: '', confirm: '' };
            this.triggerToast(data.message || 'Kata sandi berhasil diperbarui!');
        } catch (e) {
            this.triggerToast('Terjadi kesalahan jaringan. Coba lagi.', 'error');
        } finally {
            this.isSavingPassword = false;
        }
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

    <div class="bg-white rounded-2xl border border-black/5 shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative group shrink-0">
                    <img :src="avatarPreview" alt="Foto Profil" class="w-20 h-20 rounded-2xl object-cover shadow-sm bg-surface border border-black/10">
                    <template x-if="isEditingProfile">
                        <label for="avatarInput" class="absolute inset-0 rounded-2xl bg-black/50 transition-opacity flex items-center justify-center cursor-pointer text-white">
                            <span class="material-symbols-outlined text-2xl">photo_camera</span>
                        </label>
                    </template>
                    <input type="file" id="avatarInput" accept="image/*" class="hidden" @change="handleAvatarChange" :disabled="!isEditingProfile">
                </div>

                <div class="space-y-1">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl font-bold text-on-surface" x-text="profile.name"></h1>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                            {{ $userData['role'] ?? '' }}
                        </span>
                    </div>
                    <p class="text-sm text-on-surface-variant/70 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant/60">badge</span>
                        NIP: <span class="font-semibold text-on-surface">{{ $userData['nip'] ?? '-' }}</span>
                    </p>
                </div>
            </div>
            <div class="self-start md:self-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Akun Aktif
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between gap-4 border-b border-black/5 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">person_outline</span>
                            <span x-text="isEditingProfile ? 'Edit Data Diri' : 'Detail Data Diri'"></span>
                        </h3>
                    </div>
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
                                            Simpan
                                        </span>
                                    </template>
                                    <template x-if="isSavingProfile">
                                        <span class="flex items-center gap-1.5">
                                            Menyimpan...
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

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
                    </div>
                </template>

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
                    </div>
                </template>
            </div>

            <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-6">
                <div class="flex items-center justify-between gap-4 border-b border-black/5 pb-4">
                    <div>
                        <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">vpn_key</span>
                            Keamanan & Password
                        </h3>
                    </div>
                    <div class="shrink-0">
                        <template x-if="!isEditingPassword">
                            <button @click="isEditingPassword = true" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary hover:bg-primary-dark text-white font-semibold text-xs transition shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">lock_reset</span>
                                Edit Password
                            </button>
                        </template>
                        <template x-if="isEditingPassword">
                            <div class="flex items-center gap-2">
                                <button @click="cancelEditPassword()" class="px-3.5 py-2 rounded-xl border border-black/10 text-on-surface-variant hover:bg-black/5 text-xs font-medium transition">Batal</button>
                                <button @click="savePassword()" :disabled="isSavingPassword" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs transition shadow-sm disabled:opacity-50">
                                    <template x-if="!isSavingPassword"><span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[16px]">check</span>Simpan</span></template>
                                    <template x-if="isSavingPassword"><span class="flex items-center gap-1.5">Menyimpan...</span></template>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                <template x-if="!isEditingPassword">
                    <div class="p-4 rounded-xl bg-surface-variant/10 border border-black/5 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-2xl text-on-surface-variant/60">lock</span>
                            <div>
                                <p class="text-sm font-bold text-on-surface">Kata Sandi Akun</p>
                            </div>
                        </div>
                        <span class="text-sm font-mono tracking-widest text-on-surface-variant/60">••••••••••••</span>
                    </div>
                </template>

                <template x-if="isEditingPassword">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kata Sandi Saat Ini</label>
                            <div class="relative">
                                <input :type="showPasswordCurrent ? 'text' : 'password'" x-model="passwordForm.current" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition pr-10">
                                <button type="button" @click="showPasswordCurrent = !showPasswordCurrent" class="absolute right-3 top-2.5 text-on-surface-variant/50 hover:text-primary transition">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showPasswordCurrent ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showPasswordNew ? 'text' : 'password'" x-model="passwordForm.new" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition pr-10">
                                <button type="button" @click="showPasswordNew = !showPasswordNew" class="absolute right-3 top-2.5 text-on-surface-variant/50 hover:text-primary transition">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showPasswordNew ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface-variant mb-1">Konfirmasi Kata Sandi Baru</label>
                            <div class="relative">
                                <input :type="showPasswordConfirm ? 'text' : 'password'" x-model="passwordForm.confirm" class="w-full px-3.5 py-2.5 rounded-xl border border-black/10 focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm transition pr-10">
                                <button type="button" @click="showPasswordConfirm = !showPasswordConfirm" class="absolute right-3 top-2.5 text-on-surface-variant/50 hover:text-primary transition">
                                    <span class="material-symbols-outlined text-[18px]" x-text="showPasswordConfirm ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-black/5 p-6 shadow-sm space-y-6 h-fit">
            <div class="border-b border-black/5 pb-4">
                <h3 class="text-base font-bold text-on-surface flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">work_outline</span>
                    Informasi Sistem
                </h3>
            </div>
            <div class="space-y-3.5">
                <div class="p-3.5 rounded-xl bg-surface-variant/15 border border-black/5">
                    <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide block mb-0.5">Nomor Identitas (NIP)</span>
                    <span class="text-sm font-bold text-on-surface">{{ $userData['nip'] ?? '-' }}</span>
                </div>
                <div class="p-3.5 rounded-xl bg-surface-variant/15 border border-black/5">
                    <span class="text-[11px] font-semibold text-on-surface-variant/60 uppercase tracking-wide block mb-0.5">Role Sistem</span>
                    <span class="text-sm font-bold text-on-surface">{{ $userData['role'] ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-white font-medium text-xs border backdrop-blur-md"
         :class="{
             'bg-[#0B3D2E] border-emerald-500/30': toast.type === 'success' || toast.type === 'info',
             'bg-rose-950 border-rose-500/30': toast.type === 'error'
         }" style="display: none;">
        <span class="material-symbols-outlined text-[20px]" :class="toast.type === 'error' ? 'text-rose-400' : 'text-emerald-400'" x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-xs font-semibold"></span>
    </div>
</div>