@extends('layouts.hr')

@section('title', 'Struktur Organisasi')
@section('page-title', 'Struktur Organisasi')
@section('page-desc', 'Kelola hierarki departemen dan posisi jabatan perusahaan.')

@php
    $departments = [
        ['name' => 'Human Resources', 'head' => 'Rina Kartika', 'head_avatar' => 32, 'positions' => 4, 'employees' => 18, 'code' => 'HR-01'],
        ['name' => 'Finance & Accounting', 'head' => 'Fajar Nugroho', 'head_avatar' => 44, 'positions' => 5, 'employees' => 22, 'code' => 'FA-02'],
        ['name' => 'Engineering & IT', 'head' => 'Dewi Lestari', 'head_avatar' => 33, 'positions' => 7, 'employees' => 64, 'code' => 'ENG-03'],
        ['name' => 'Sales & Marketing', 'head' => 'Budi Santoso', 'head_avatar' => 22, 'positions' => 6, 'employees' => 41, 'code' => 'SLS-04'],
    ];

    // NODE DATA WITH UNIQUE IDs FOR DROPDOWN
    $ceoNode = ['id' => 'n-ceo', 'name' => 'Tahsan Khan', 'role' => 'Founder – CEO', 'nip' => 'EMP-00001', 'email' => 'tahsan.khan@talentahr.co.id', 'phone' => '081122334455', 'dept' => 'Executive Board', 'avatar' => 60];

    $level2Nodes = [
        ['id' => 'n-l2-1', 'name' => 'Herry Kane', 'role' => 'Engineering Lead', 'nip' => 'EMP-00102', 'email' => 'herry.kane@talentahr.co.id', 'phone' => '081234567801', 'dept' => 'Engineering & IT', 'avatar' => 11, 'hasChildren' => false],
        ['id' => 'n-l2-2', 'name' => 'Herry Brooks', 'role' => 'Commercial Director', 'nip' => 'EMP-00103', 'email' => 'herry.brooks@talentahr.co.id', 'phone' => '081234567802', 'dept' => 'Commercial', 'avatar' => 53, 'hasChildren' => true],
        ['id' => 'n-l2-3', 'name' => 'David Warner', 'role' => 'Finance Director', 'nip' => 'EMP-00104', 'email' => 'david.warner@talentahr.co.id', 'phone' => '081234567803', 'dept' => 'Finance & Accounting', 'avatar' => 52, 'hasChildren' => false],
    ];

    $level3Nodes = [
        ['id' => 'n-l3-1', 'name' => 'Azam Khan', 'role' => 'Marketing Manager', 'nip' => 'EMP-00201', 'email' => 'azam.khan@talentahr.co.id', 'phone' => '081399887701', 'dept' => 'Sales & Marketing', 'avatar' => 15, 'hasChildren' => false],
        ['id' => 'n-l3-2', 'name' => 'Tim David', 'role' => 'HR Operations Manager', 'nip' => 'EMP-00202', 'email' => 'tim.david@talentahr.co.id', 'phone' => '081399887702', 'dept' => 'Human Resources', 'avatar' => 32, 'hasChildren' => true],
        ['id' => 'n-l3-3', 'name' => 'Joe Root', 'role' => 'Account Executive Lead', 'nip' => 'EMP-00203', 'email' => 'joe.root@talentahr.co.id', 'phone' => '081399887703', 'dept' => 'Commercial', 'avatar' => 65, 'hasChildren' => false],
    ];

    $level4Nodes = [
        ['id' => 'n-l4-1', 'name' => 'Hames James', 'role' => 'Senior Account Executive', 'nip' => 'EMP-00301', 'email' => 'hames.james@talentahr.co.id', 'phone' => '081566778801', 'dept' => 'Commercial', 'avatar' => 29],
        ['id' => 'n-l4-2', 'name' => 'Jaman Khan', 'role' => 'Software Engineer', 'nip' => 'EMP-00302', 'email' => 'jaman.khan@talentahr.co.id', 'phone' => '081566778802', 'dept' => 'Engineering & IT', 'avatar' => 38],
    ];
@endphp

@section('content')
<div x-data="{
    showDeptModal: false,
    showProfileModal: false,
    showEditModal: false,
    openMenuId: null,
    selectedNode: null,
    zoomLevel: 100,
    showLevel3: true,
    showLevel4: true,
    toast: { show: false, message: '', type: 'success' },
    triggerToast(msg, type='success') {
        this.toast.message = msg;
        this.toast.type = type;
        this.toast.show = true;
        setTimeout(() => this.toast.show = false, 3000);
    },
    zoomIn() { if (this.zoomLevel < 130) this.zoomLevel += 10; },
    zoomOut() { if (this.zoomLevel > 70) this.zoomLevel -= 10; },
    resetZoom() { this.zoomLevel = 100; },
    openProfile(node) {
        this.selectedNode = node;
        this.showProfileModal = true;
        this.openMenuId = null;
    },
    openEdit(node) {
        this.selectedNode = Object.assign({}, node);
        this.showEditModal = true;
        this.openMenuId = null;
    }
}">

    {{-- STAT ROW --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Total Departemen</p>
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">corporate_fare</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-on-surface leading-none">{{ count($departments) }}</p>
            <p class="text-[11px] text-on-surface-variant/60 mt-1">Struktur divisional aktif</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Total Posisi Terdaftar</p>
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">badge</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-primary leading-none">{{ collect($departments)->sum('positions') }} Posisi</p>
            <p class="text-[11px] text-on-surface-variant/60 mt-1">Seluruh tingkatan jabatan</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-black/5 shadow-sm space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-bold text-on-surface-variant/60 uppercase tracking-wide">Total Karyawan Aktif</p>
                <span class="material-symbols-outlined text-[20px] text-on-surface-variant/40">groups</span>
            </div>
            <p class="text-2xl font-extrabold font-mono text-on-surface leading-none">1.284 Org</p>
            <p class="text-[11px] text-on-surface-variant/60 mt-1">Terpetakan dalam org chart</p>
        </div>
    </div>

    {{-- INTERACTIVE ORG CHART VIEWER --}}
    <div class="bg-white rounded-2xl border border-black/5 shadow-sm p-6 mt-6 overflow-hidden">
        <div class="flex items-center justify-between mb-5 flex-wrap gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-on-surface">Hierarki Struktur Organisasi (Org Chart)</h2>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-primary/10 text-primary border border-primary/20">Interactive Canvas</span>
                </div>
                <p class="text-xs text-on-surface-variant/60 mt-0.5">Tambah departemen baru atau klik titik 3 pada node untuk mengubah posisi struktur</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs font-bold bg-surface-variant/10 px-3 py-1.5 rounded-xl border border-black/5">
                    <span class="text-on-surface-variant/60 font-mono">Scale: <strong x-text="zoomLevel + '%'">100%</strong></span>
                </div>
                <button type="button" @click="showDeptModal = true"
                        class="bg-primary hover:bg-primary-dark text-white text-xs font-bold px-4 py-2 rounded-xl flex items-center gap-1.5 shadow-sm transition">
                    <span class="material-symbols-outlined text-[17px]">add</span>
                    Tambah Departemen
                </button>
            </div>
        </div>

        {{-- DOT PATTERN CANVAS CONTAINER DENGAN DOTS HIJAU SEDANG SESUAI TEMA --}}
        <div class="relative overflow-x-auto rounded-2xl bg-[#F8FAFC] border border-slate-200 p-8 min-h-[580px] flex flex-col items-center justify-start select-none"
             style="background-image: radial-gradient(rgba(11, 61, 46, 0.25) 1.5px, transparent 1.5px); background-size: 18px 18px;">

            {{-- CANVAS CONTENT SCALER --}}
            <div class="transition-transform duration-200 origin-top flex flex-col items-center gap-0 w-full py-4 min-w-[850px]"
                 :style="'transform: scale(' + (zoomLevel / 100) + ')'">

                {{-- LEVEL 1: CEO NODE --}}
                <div class="relative flex flex-col items-center z-20">
                    <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-3.5 flex items-center justify-between w-64 hover:border-primary hover:shadow-md transition cursor-pointer relative">
                        <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($ceoNode) }})">
                            <img src="https://i.pravatar.cc/64?img={{ $ceoNode['avatar'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-slate-100" alt="{{ $ceoNode['name'] }}">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $ceoNode['name'] }}</p>
                                <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">{{ $ceoNode['role'] }}</p>
                            </div>
                        </div>

                        {{-- DROPDOWN MENU TITIK 3 --}}
                        <div class="relative" @click.outside="openMenuId === '{{ $ceoNode['id'] }}' && (openMenuId = null)">
                            <button type="button" @click.stop="openMenuId = openMenuId === '{{ $ceoNode['id'] }}' ? null : '{{ $ceoNode['id'] }}'"
                                    class="text-slate-400 hover:text-primary transition shrink-0 p-1.5 rounded-lg hover:bg-slate-100">
                                <span class="material-symbols-outlined text-[18px]">more_vert</span>
                            </button>

                            <div x-show="openMenuId === '{{ $ceoNode['id'] }}'" x-cloak x-transition.origin.top.right
                                 class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs font-bold">
                                <button type="button" @click.stop="openProfile({{ json_encode($ceoNode) }})"
                                        class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                    <span class="material-symbols-outlined text-[16px]">account_circle</span>
                                    Lihat Profil
                                </button>
                                <button type="button" @click.stop="openEdit({{ json_encode($ceoNode) }})"
                                        class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                    <span class="material-symbols-outlined text-[16px]">edit</span>
                                    Edit Data
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- STEM & PLUS BADGE --}}
                    <div class="w-px h-8 bg-slate-300"></div>
                    <button type="button" @click="showDeptModal = true"
                            class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center shadow-sm border-2 border-white -mt-2.5 z-30 hover:scale-110 transition cursor-pointer"
                            title="Tambah Sub-Departemen">
                        <span class="material-symbols-outlined text-[14px]">add</span>
                    </button>
                </div>

                {{-- LEVEL 2 CONNECTOR LINE TREE --}}
                <div class="w-full flex flex-col items-center">
                    {{-- HORIZONTAL CONNECTOR BUS --}}
                    <div class="w-[66%] h-px bg-slate-300"></div>

                    {{-- LEVEL 2 NODES ROW --}}
                    <div class="grid grid-cols-3 gap-12 w-full pt-0">
                        @foreach ($level2Nodes as $n2)
                            <div class="flex flex-col items-center">
                                <div class="w-px h-6 bg-slate-300"></div>
                                <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-3.5 flex items-center justify-between w-64 hover:border-primary hover:shadow-md transition cursor-pointer relative">
                                    <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($n2) }})">
                                        <img src="https://i.pravatar.cc/64?img={{ $n2['avatar'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-slate-100" alt="{{ $n2['name'] }}">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $n2['name'] }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">{{ $n2['role'] }}</p>
                                        </div>
                                    </div>

                                    {{-- DROPDOWN MENU TITIK 3 --}}
                                    <div class="relative" @click.outside="openMenuId === '{{ $n2['id'] }}' && (openMenuId = null)">
                                        <button type="button" @click.stop="openMenuId = openMenuId === '{{ $n2['id'] }}' ? null : '{{ $n2['id'] }}'"
                                                class="text-slate-400 hover:text-primary transition shrink-0 p-1.5 rounded-lg hover:bg-slate-100">
                                            <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenuId === '{{ $n2['id'] }}'" x-cloak x-transition.origin.top.right
                                             class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs font-bold">
                                            <button type="button" @click.stop="openProfile({{ json_encode($n2) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                                <span class="material-symbols-outlined text-[16px]">account_circle</span>
                                                Lihat Profil
                                            </button>
                                            <button type="button" @click.stop="openEdit({{ json_encode($n2) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                Edit Data
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if ($n2['hasChildren'])
                                    <div class="w-px h-8 bg-slate-300"></div>
                                    <button type="button" @click="showLevel3 = !showLevel3"
                                            class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center shadow-sm border-2 border-white -mt-2.5 z-30 hover:scale-110 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]" x-text="showLevel3 ? 'remove' : 'add'">add</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- LEVEL 3 NODES ROW --}}
                <div class="w-full flex flex-col items-center" x-show="showLevel3" x-transition>
                    {{-- HORIZONTAL CONNECTOR BUS --}}
                    <div class="w-[66%] h-px bg-slate-300"></div>

                    <div class="grid grid-cols-3 gap-12 w-full">
                        @foreach ($level3Nodes as $n3)
                            <div class="flex flex-col items-center">
                                <div class="w-px h-6 bg-slate-300"></div>
                                <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-3.5 flex items-center justify-between w-64 hover:border-primary hover:shadow-md transition cursor-pointer relative">
                                    <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($n3) }})">
                                        <img src="https://i.pravatar.cc/64?img={{ $n3['avatar'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-slate-100" alt="{{ $n3['name'] }}">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $n3['name'] }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">{{ $n3['role'] }}</p>
                                        </div>
                                    </div>

                                    {{-- DROPDOWN MENU TITIK 3 --}}
                                    <div class="relative" @click.outside="openMenuId === '{{ $n3['id'] }}' && (openMenuId = null)">
                                        <button type="button" @click.stop="openMenuId = openMenuId === '{{ $n3['id'] }}' ? null : '{{ $n3['id'] }}'"
                                                class="text-slate-400 hover:text-primary transition shrink-0 p-1.5 rounded-lg hover:bg-slate-100">
                                            <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenuId === '{{ $n3['id'] }}'" x-cloak x-transition.origin.top.right
                                             class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs font-bold">
                                            <button type="button" @click.stop="openProfile({{ json_encode($n3) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                                <span class="material-symbols-outlined text-[16px]">account_circle</span>
                                                Lihat Profil
                                            </button>
                                            <button type="button" @click.stop="openEdit({{ json_encode($n3) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                Edit Data
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if ($n3['hasChildren'])
                                    <div class="w-px h-8 bg-slate-300"></div>
                                    <button type="button" @click="showLevel4 = !showLevel4"
                                            class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center shadow-sm border-2 border-white -mt-2.5 z-30 hover:scale-110 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[14px]" x-text="showLevel4 ? 'remove' : 'add'">add</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- LEVEL 4 NODES ROW --}}
                <div class="w-full flex flex-col items-center" x-show="showLevel3 && showLevel4" x-transition>
                    {{-- HORIZONTAL CONNECTOR BUS --}}
                    <div class="w-[33%] h-px bg-slate-300"></div>

                    <div class="grid grid-cols-2 gap-12 w-[66%]">
                        @foreach ($level4Nodes as $n4)
                            <div class="flex flex-col items-center">
                                <div class="w-px h-6 bg-slate-300"></div>
                                <div class="bg-white border border-slate-200 shadow-sm rounded-xl p-3.5 flex items-center justify-between w-64 hover:border-primary hover:shadow-md transition cursor-pointer relative">
                                    <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($n4) }})">
                                        <img src="https://i.pravatar.cc/64?img={{ $n4['avatar'] }}" class="w-10 h-10 rounded-full object-cover shrink-0 ring-2 ring-slate-100" alt="{{ $n4['name'] }}">
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-800 leading-tight truncate">{{ $n4['name'] }}</p>
                                            <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">{{ $n4['role'] }}</p>
                                        </div>
                                    </div>

                                    {{-- DROPDOWN MENU TITIK 3 --}}
                                    <div class="relative" @click.outside="openMenuId === '{{ $n4['id'] }}' && (openMenuId = null)">
                                        <button type="button" @click.stop="openMenuId = openMenuId === '{{ $n4['id'] }}' ? null : '{{ $n4['id'] }}'"
                                                class="text-slate-400 hover:text-primary transition shrink-0 p-1.5 rounded-lg hover:bg-slate-100">
                                            <span class="material-symbols-outlined text-[18px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenuId === '{{ $n4['id'] }}'" x-cloak x-transition.origin.top.right
                                             class="absolute right-0 mt-1 w-36 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-40 text-xs font-bold">
                                            <button type="button" @click.stop="openProfile({{ json_encode($n4) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                                <span class="material-symbols-outlined text-[16px]">account_circle</span>
                                                Lihat Profil
                                            </button>
                                            <button type="button" @click.stop="openEdit({{ json_encode($n4) }})"
                                                    class="w-full flex items-center gap-2 px-3 py-1.5 text-slate-700 hover:bg-primary/5 hover:text-primary transition text-left">
                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                Edit Data
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

            {{-- FLOATING CANVAS CONTROLS --}}
            <div class="absolute bottom-4 left-4 flex flex-col rounded-xl bg-white border border-slate-200 shadow-md divide-y divide-slate-100 z-30">
                <button type="button" @click="zoomOut()" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:text-primary hover:bg-slate-50 transition rounded-t-xl" title="Zoom Out (-)">
                    <span class="material-symbols-outlined text-[18px]">remove</span>
                </button>
                <button type="button" @click="zoomIn()" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:text-primary hover:bg-slate-50 transition" title="Zoom In (+)">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                </button>
                <button type="button" @click="resetZoom()" class="w-9 h-9 flex items-center justify-center text-slate-600 hover:text-primary hover:bg-slate-50 transition rounded-b-xl" title="Fit to Screen">
                    <span class="material-symbols-outlined text-[18px]">aspect_ratio</span>
                </button>
            </div>
        </div>
    </div>



    {{-- MODAL 1: LIHAT PROFIL KARYAWAN --}}
    <div x-show="showProfileModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showProfileModal = false">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150" x-show="selectedNode">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">account_circle</span>
                    <h3 class="text-base font-bold text-on-surface">Profil Pegawai</h3>
                </div>
                <button type="button" @click="showProfileModal = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex flex-col items-center text-center space-y-2 py-2">
                <img :src="'https://i.pravatar.cc/96?img=' + (selectedNode ? selectedNode.avatar : 1)"
                     class="w-16 h-16 rounded-full object-cover ring-4 ring-primary/10 shadow-md" alt="">
                <div>
                    <h4 class="text-sm font-bold text-on-surface" x-text="selectedNode ? selectedNode.name : ''"></h4>
                    <p class="text-xs text-primary font-bold mt-0.5" x-text="selectedNode ? selectedNode.role : ''"></p>
                    <span class="inline-block mt-1 font-mono text-[10px] px-2.5 py-0.5 rounded-full bg-surface-variant/20 text-on-surface-variant/70 border border-black/5"
                          x-text="selectedNode ? selectedNode.nip : ''"></span>
                </div>
            </div>

            <div class="space-y-2 text-xs border-t border-b border-black/5 py-3">
                <div class="flex justify-between">
                    <span class="text-on-surface-variant/60">Departemen:</span>
                    <span class="font-bold text-on-surface" x-text="selectedNode ? selectedNode.dept : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant/60">Email:</span>
                    <span class="font-mono text-on-surface-variant/80" x-text="selectedNode ? selectedNode.email : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant/60">Telepon/WA:</span>
                    <span class="font-mono text-on-surface-variant/80" x-text="selectedNode ? selectedNode.phone : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-on-surface-variant/60">Status Kepegawaian:</span>
                    <span class="font-bold text-primary">PKWTT (Tetap)</span>
                </div>
            </div>

            <div class="flex items-center justify-end pt-1">
                <button type="button" @click="showProfileModal = false"
                        class="px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm transition">
                    Tutup Profil
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: EDIT DATA PEGAWAI --}}
    <div x-show="showEditModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showEditModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150" x-show="selectedNode">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">edit</span>
                    <h3 class="text-base font-bold text-on-surface">Edit Jabatan &amp; Nama Pegawai</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nama Lengkap</label>
                    <input type="text" x-model="selectedNode.name" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Jabatan / Role</label>
                    <input type="text" x-model="selectedNode.role" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Departemen</label>
                    <select x-model="selectedNode.dept" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                        <option>Executive Board</option>
                        <option>Human Resources</option>
                        <option>Finance &amp; Accounting</option>
                        <option>Engineering &amp; IT</option>
                        <option>Sales &amp; Marketing</option>
                        <option>Commercial</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                <button type="button" @click="showEditModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showEditModal = false; triggerToast('Data jabatan berhasil diperbarui!')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm transition">
                    Simpan Perubahan
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL 3: TAMBAH DEPARTEMEN / NODE BARU --}}
    <div x-show="showDeptModal" x-cloak
         class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4"
         @click.self="showDeptModal = false">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 animate-in fade-in zoom-in-95 duration-150">
            <div class="flex items-center justify-between border-b border-black/5 pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[20px]">add_business</span>
                    <h3 class="text-base font-bold text-on-surface">Tambah Departemen / Node Chart</h3>
                </div>
                <button type="button" @click="showDeptModal = false" class="text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Nama Departemen / Unit Baru</label>
                    <input type="text" placeholder="Contoh: Product & Design" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                </div>
                <div>
                    <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Departemen Induk / Atasan (Parent Node Chart)</label>
                    <select class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                        <option>Executive Board (CEO - Tahsan Khan)</option>
                        <option>Commercial Director (Herry Brooks)</option>
                        <option>Engineering &amp; IT (Herry Kane)</option>
                        <option>Finance &amp; Accounting (David Warner)</option>
                        <option>Human Resources (Tim David)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Kode Divisi</label>
                        <input type="text" placeholder="Contoh: PRD-05" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="font-bold text-on-surface-variant/70 uppercase block mb-1">Kepala Unit (Head)</label>
                        <input type="text" placeholder="Nama Manager" class="w-full border border-black/10 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-primary/20 focus:outline-none">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-black/5">
                <button type="button" @click="showDeptModal = false"
                        class="px-4 py-2 rounded-xl border border-black/10 text-xs font-bold text-on-surface-variant/70 hover:bg-black/5 transition">
                    Batal
                </button>
                <button type="button" @click="showDeptModal = false; triggerToast('Departemen baru berhasil ditambahkan & terhubung ke Org Chart!')"
                        class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold hover:bg-primary-dark shadow-sm transition">
                    Simpan &amp; Hubungkan ke Chart
                </button>
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
@endsection