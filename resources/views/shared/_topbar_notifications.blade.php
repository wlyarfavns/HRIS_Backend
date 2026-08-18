<div class="relative" x-data="notificationDropdown()" x-init="fetchNotifications()" @click.outside="open = false">


    <button @click="open = !open"
        class="relative p-2 text-on-surface-variant/60 hover:text-primary transition-colors focus:outline-none rounded-full hover:bg-black/5">
        <span class="material-symbols-outlined text-[24px]">notifications</span>
        <span x-show="unreadCount > 0" x-cloak class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-error opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-error ring-2 ring-white"></span>
        </span>
    </button>


    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-2"
        class="absolute right-0 mt-3 w-[340px] sm:w-[380px] bg-white rounded-md shadow-[0_12px_40px_rgba(0,0,0,0.12)] border border-black/5 z-50 overflow-hidden"
        style="display: none;">


        <div
            class="px-5 py-4 flex items-center justify-between border-b border-black/5 bg-gradient-to-r from-surface-container/50 to-transparent">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-medium text-on-surface">Notifikasi</h3>
                <span x-show="unreadCount > 0" x-text="unreadCount + ' Baru'"
                    class="bg-primary/10 text-primary text-[10px] font-medium px-2 py-0.5 rounded-full"></span>
            </div>
            <button x-show="unreadCount > 0" @click="markAllRead()"
                class="text-[11px] font-semibold text-primary hover:text-primary/70 transition-colors">
                Tandai semua dibaca
            </button>
        </div>


        <div class="max-h-[360px] overflow-y-auto divide-y divide-black/5 custom-scrollbar">


            <template x-if="notifications.length === 0">
                <div class="px-5 py-8 text-center">
                    <p class="text-xs font-semibold text-on-surface-variant/60">Tidak ada notifikasi baru.</p>
                </div>
            </template>


            <template x-for="(notif, index) in notifications" :key="notif.id">
                <a href="#" @click.prevent="readAndRedirect(notif.id, notif.data.url)"
                    class="flex gap-4 px-5 py-4 hover:bg-black/[0.02] transition-colors bg-primary/[0.02] relative group">


                    <div
                        class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[20px]">notifications_active</span>
                    </div>

                    <div class="flex-1 min-w-0 pr-4">
                        <p class="text-xs font-medium text-on-surface mb-0.5 group-hover:text-primary transition-colors"
                            x-text="notif.data.title"></p>
                        <p class="text-[11px] text-on-surface-variant/70 leading-relaxed truncate"
                            x-text="notif.data.message"></p>
                        <p class="text-[10px] text-on-surface-variant/50 font-medium mt-1.5 flex items-center gap-1">
                            <span class="material-symbols-outlined text-[12px]">schedule</span>
                            <span x-text="formatDate(notif.created_at)"></span>
                        </p>
                    </div>
                    <div class="absolute right-5 top-1/2 -translate-y-1/2 w-2 h-2 rounded-full bg-primary"></div>
                </a>
            </template>
        </div>


        <div class="p-3 border-t border-black/5 bg-surface-container/20 text-center">
            <a href="{{ route('notifications.index') }}"
                class="inline-block text-xs font-medium text-primary hover:text-primary/80 transition-colors py-1">
                Lihat semua riwayat
            </a>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.2);
        }
    </style>
</div>

<script>
    function notificationDropdown() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,

            fetchNotifications() {
                fetch('{{ route('notifications.unread') }}')
                    .then(res => res.json())
                    .then(data => {
                        this.notifications = data;
                        this.unreadCount = data.length;
                    });
            },

            readAndRedirect(id, url) {
                fetch(`/notifikasi/${id}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                }).then(() => {
                    if (url && url !== '#') window.location.href = url;
                    else this.fetchNotifications();
                });
            },

            markAllRead() {
                fetch('{{ route('notifications.readAll') }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                }).then(() => this.fetchNotifications());
            },

            formatDate(dateStr) {
                const d = new Date(dateStr);
                const now = new Date();
                const diff = (now - d) / 1000; 

                if (diff < 60) return 'Baru saja';
                if (diff < 3600) return Math.floor(diff / 60) + ' menit yang lalu';
                if (diff < 86400) return Math.floor(diff / 3600) + ' jam yang lalu';

                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
            }
        }
    }
</script>
