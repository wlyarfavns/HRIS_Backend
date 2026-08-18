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


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-md p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-[#0B3D2E]/30 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px] text-[#0B3D2E]">corporate_fare</span>
                </div>
                <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Departemen</span>
            </div>
            <p class="text-3xl font-semibold  text-gray-800 leading-none mb-2">{{ count($departments) }}</p>
            <p class="text-sm text-gray-500">Struktur divisional aktif</p>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
        </div>
        <div class="bg-white rounded-md p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-[#0B3D2E]/30 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px] text-gray-700">badge</span>
                </div>
                <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Posisi</span>
            </div>
            <p class="text-3xl font-semibold  text-[#0B3D2E] leading-none mb-2">{{ collect($departments)->sum('positions') }}</p>
            <p class="text-sm text-gray-500">Seluruh tingkatan jabatan</p>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
        </div>
        <div class="bg-white rounded-md p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:border-[#0B3D2E]/30 transition-colors">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px] text-gray-700">groups</span>
                </div>
                <span class="text-xs font-medium text-gray-400 uppercase tracking-wider">Karyawan</span>
            </div>
            <p class="text-3xl font-semibold  text-gray-800 leading-none mb-2">1.284</p>
            <p class="text-sm text-gray-500">Terpetakan dalam org chart</p>
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-gray-50 rounded-full opacity-0 group-hover:opacity-100 transition-opacity "></div>
        </div>
    </div>


    <div class="bg-white rounded-md border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between flex-wrap gap-4 bg-gray-50/50">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E]">
                        <span class="material-symbols-outlined text-[20px]">account_tree</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-medium text-gray-800">Hierarki Struktur Organisasi</h2>
                            <span class="text-[10px] font-medium px-2 py-0.5 rounded-md bg-gray-50 text-gray-700 uppercase tracking-wider">Interactive</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-0.5">Kelola departemen dan posisi dalam bentuk visualisasi chart</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-sm font-medium bg-white px-4 py-2.5 rounded-md border border-gray-200 shadow-sm">
                    <span class="text-gray-500">Scale: <strong class="text-gray-800 " x-text="zoomLevel + '%'">100%</strong></span>
                </div>
                <button type="button" @click="showDeptModal = true"
                        class="bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium px-5 py-2.5 rounded-md flex items-center gap-2 shadow-sm transition">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    Tambah Departemen
                </button>
            </div>
        </div>


        <div class="relative overflow-x-auto bg-gray-50/50 min-h-[600px] flex flex-col items-center justify-start select-none"
             style="background-image: radial-gradient(#E5E7EB 1.5px, transparent 1.5px); background-size: 24px 24px;">


            <div class="transition-transform duration-300 origin-top flex flex-col items-center gap-0 w-full py-8 min-w-[1000px]"
                 :style="'transform: scale(' + (zoomLevel / 100) + ')'">


                <div class="relative flex flex-col items-center z-20">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-md p-4 flex items-center justify-between w-72 hover:border-[#0B3D2E] hover:shadow-sm transition cursor-pointer relative group">
                        <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($ceoNode) }})">
                            <img src="https://i.pravatar.cc/64?img={{ $ceoNode['avatar'] }}" class="w-12 h-12 rounded-full object-cover shrink-0 ring-4 ring-emerald-50" alt="{{ $ceoNode['name'] }}">
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-800 leading-tight truncate group-hover:text-[#0B3D2E] transition-colors">{{ $ceoNode['name'] }}</p>
                                <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $ceoNode['role'] }}</p>
                            </div>
                        </div>


                        <div class="relative" @click.outside="openMenuId === '{{ $ceoNode['id'] }}' && (openMenuId = null)">
                            <button type="button" @click.stop="openMenuId = openMenuId === '{{ $ceoNode['id'] }}' ? null : '{{ $ceoNode['id'] }}'"
                                    class="text-gray-400 hover:text-gray-800 transition shrink-0 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[20px]">more_vert</span>
                            </button>

                            <div x-show="openMenuId === '{{ $ceoNode['id'] }}'" x-cloak x-transition.origin.top.right
                                 class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-sm border border-gray-100 py-1.5 z-40">
                                <button type="button" @click.stop="openProfile({{ json_encode($ceoNode) }})"
                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                    <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                    Lihat Profil
                                </button>
                                <button type="button" @click.stop="openEdit({{ json_encode($ceoNode) }})"
                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                    Edit Data
                                </button>
                            </div>
                        </div>
                    </div>


                    <div class="w-px h-10 bg-gray-300"></div>
                    <button type="button" @click="showDeptModal = true"
                            class="w-6 h-6 rounded-full bg-[#0B3D2E] text-white flex items-center justify-center shadow-sm ring-4 ring-white -mt-3 z-30 hover:scale-110 transition cursor-pointer"
                            title="Tambah Sub-Departemen">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                    </button>
                </div>


                <div class="w-full flex flex-col items-center">

                    <div class="w-[66%] h-px bg-gray-300"></div>


                    <div class="grid grid-cols-3 gap-16 w-full pt-0">
                        @foreach ($level2Nodes as $n2)
                            <div class="flex flex-col items-center">
                                <div class="w-px h-8 bg-gray-300"></div>
                                <div class="bg-white border border-gray-200 shadow-sm rounded-md p-4 flex items-center justify-between w-72 hover:border-[#0B3D2E] hover:shadow-sm transition cursor-pointer relative group">
                                    <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($n2) }})">
                                        <img src="https://i.pravatar.cc/64?img={{ $n2['avatar'] }}" class="w-12 h-12 rounded-full object-cover shrink-0 ring-4 ring-gray-50" alt="{{ $n2['name'] }}">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 leading-tight truncate group-hover:text-[#0B3D2E] transition-colors">{{ $n2['name'] }}</p>
                                            <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $n2['role'] }}</p>
                                        </div>
                                    </div>


                                    <div class="relative" @click.outside="openMenuId === '{{ $n2['id'] }}' && (openMenuId = null)">
                                        <button type="button" @click.stop="openMenuId = openMenuId === '{{ $n2['id'] }}' ? null : '{{ $n2['id'] }}'"
                                                class="text-gray-400 hover:text-gray-800 transition shrink-0 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenuId === '{{ $n2['id'] }}'" x-cloak x-transition.origin.top.right
                                             class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-sm border border-gray-100 py-1.5 z-40">
                                            <button type="button" @click.stop="openProfile({{ json_encode($n2) }})"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                                Lihat Profil
                                            </button>
                                            <button type="button" @click.stop="openEdit({{ json_encode($n2) }})"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                                Edit Data
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if ($n2['hasChildren'])
                                    <div class="w-px h-10 bg-gray-300"></div>
                                    <button type="button" @click="showLevel3 = !showLevel3"
                                            class="w-6 h-6 rounded-full bg-[#0B3D2E] text-white flex items-center justify-center shadow-sm ring-4 ring-white -mt-3 z-30 hover:scale-110 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]" x-text="showLevel3 ? 'remove' : 'add'">add</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>


                <div class="w-full flex flex-col items-center" x-show="showLevel3" x-transition>

                    <div class="w-[66%] h-px bg-gray-300"></div>

                    <div class="grid grid-cols-3 gap-16 w-full">
                        @foreach ($level3Nodes as $n3)
                            <div class="flex flex-col items-center">
                                <div class="w-px h-8 bg-gray-300"></div>
                                <div class="bg-white border border-gray-200 shadow-sm rounded-md p-4 flex items-center justify-between w-72 hover:border-[#0B3D2E] hover:shadow-sm transition cursor-pointer relative group">
                                    <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($n3) }})">
                                        <img src="https://i.pravatar.cc/64?img={{ $n3['avatar'] }}" class="w-12 h-12 rounded-full object-cover shrink-0 ring-4 ring-gray-50" alt="{{ $n3['name'] }}">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 leading-tight truncate group-hover:text-[#0B3D2E] transition-colors">{{ $n3['name'] }}</p>
                                            <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $n3['role'] }}</p>
                                        </div>
                                    </div>


                                    <div class="relative" @click.outside="openMenuId === '{{ $n3['id'] }}' && (openMenuId = null)">
                                        <button type="button" @click.stop="openMenuId = openMenuId === '{{ $n3['id'] }}' ? null : '{{ $n3['id'] }}'"
                                                class="text-gray-400 hover:text-gray-800 transition shrink-0 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenuId === '{{ $n3['id'] }}'" x-cloak x-transition.origin.top.right
                                             class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-sm border border-gray-100 py-1.5 z-40">
                                            <button type="button" @click.stop="openProfile({{ json_encode($n3) }})"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                                Lihat Profil
                                            </button>
                                            <button type="button" @click.stop="openEdit({{ json_encode($n3) }})"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                                Edit Data
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if ($n3['hasChildren'])
                                    <div class="w-px h-10 bg-gray-300"></div>
                                    <button type="button" @click="showLevel4 = !showLevel4"
                                            class="w-6 h-6 rounded-full bg-[#0B3D2E] text-white flex items-center justify-center shadow-sm ring-4 ring-white -mt-3 z-30 hover:scale-110 transition cursor-pointer">
                                        <span class="material-symbols-outlined text-[16px]" x-text="showLevel4 ? 'remove' : 'add'">add</span>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>


                <div class="w-full flex flex-col items-center" x-show="showLevel3 && showLevel4" x-transition>

                    <div class="w-[33%] h-px bg-gray-300"></div>

                    <div class="grid grid-cols-2 gap-16 w-[66%]">
                        @foreach ($level4Nodes as $n4)
                            <div class="flex flex-col items-center">
                                <div class="w-px h-8 bg-gray-300"></div>
                                <div class="bg-white border border-gray-200 shadow-sm rounded-md p-4 flex items-center justify-between w-72 hover:border-[#0B3D2E] hover:shadow-sm transition cursor-pointer relative group">
                                    <div class="flex items-center gap-3 min-w-0" @click="openProfile({{ json_encode($n4) }})">
                                        <img src="https://i.pravatar.cc/64?img={{ $n4['avatar'] }}" class="w-12 h-12 rounded-full object-cover shrink-0 ring-4 ring-gray-50" alt="{{ $n4['name'] }}">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800 leading-tight truncate group-hover:text-[#0B3D2E] transition-colors">{{ $n4['name'] }}</p>
                                            <p class="text-xs text-gray-500 font-medium truncate mt-0.5">{{ $n4['role'] }}</p>
                                        </div>
                                    </div>


                                    <div class="relative" @click.outside="openMenuId === '{{ $n4['id'] }}' && (openMenuId = null)">
                                        <button type="button" @click.stop="openMenuId = openMenuId === '{{ $n4['id'] }}' ? null : '{{ $n4['id'] }}'"
                                                class="text-gray-400 hover:text-gray-800 transition shrink-0 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center">
                                            <span class="material-symbols-outlined text-[20px]">more_vert</span>
                                        </button>

                                        <div x-show="openMenuId === '{{ $n4['id'] }}'" x-cloak x-transition.origin.top.right
                                             class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-sm border border-gray-100 py-1.5 z-40">
                                            <button type="button" @click.stop="openProfile({{ json_encode($n4) }})"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                                <span class="material-symbols-outlined text-[18px]">account_circle</span>
                                                Lihat Profil
                                            </button>
                                            <button type="button" @click.stop="openEdit({{ json_encode($n4) }})"
                                                    class="w-full flex items-center gap-2.5 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-[#0B3D2E] transition text-left">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
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


            <div class="absolute bottom-6 left-6 flex flex-col rounded-md bg-white border border-gray-200 shadow-sm divide-y divide-gray-100 z-30">
                <button type="button" @click="zoomOut()" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-[#0B3D2E] hover:bg-gray-50 transition rounded-t-[16px]" title="Zoom Out (-)">
                    <span class="material-symbols-outlined text-[20px]">remove</span>
                </button>
                <button type="button" @click="zoomIn()" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-[#0B3D2E] hover:bg-gray-50 transition" title="Zoom In (+)">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                </button>
                <button type="button" @click="resetZoom()" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:text-[#0B3D2E] hover:bg-gray-50 transition rounded-b-[16px]" title="Fit to Screen">
                    <span class="material-symbols-outlined text-[20px]">aspect_ratio</span>
                </button>
            </div>
        </div>
    </div>


    <div x-show="showProfileModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showProfileModal = false">
        <div class="bg-white rounded-md max-w-sm w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in duration-200 border border-gray-100" x-show="selectedNode">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E]">
                        <span class="material-symbols-outlined text-[20px]">account_circle</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800">Profil Pegawai</h3>
                </div>
                <button type="button" @click="showProfileModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex flex-col items-center text-center space-y-3 py-2">
                <div class="relative">
                    <img :src="'https://i.pravatar.cc/96?img=' + (selectedNode ? selectedNode.avatar : 1)"
                         class="w-20 h-20 rounded-full object-cover ring-4 ring-emerald-50 shadow-sm" alt="">
                    <div class="absolute bottom-0 right-0 w-5 h-5 bg-gray-500 rounded-full border-2 border-white"></div>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-800" x-text="selectedNode ? selectedNode.name : ''"></h4>
                    <p class="text-sm text-[#0B3D2E] font-medium mt-0.5" x-text="selectedNode ? selectedNode.role : ''"></p>
                    <span class="inline-block mt-2  text-[11px] px-3 py-1 rounded-md bg-gray-100 text-gray-600 font-medium tracking-wide uppercase"
                          x-text="selectedNode ? selectedNode.nip : ''"></span>
                </div>
            </div>

            <div class="space-y-3 text-sm border-t border-b border-gray-100 py-4 bg-gray-50/50 rounded-md px-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-medium">Departemen</span>
                    <span class="font-medium text-gray-800" x-text="selectedNode ? selectedNode.dept : ''"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-medium">Email</span>
                    <span class=" text-gray-600" x-text="selectedNode ? selectedNode.email : ''"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500 font-medium">No. Telepon</span>
                    <span class=" text-gray-600" x-text="selectedNode ? selectedNode.phone : ''"></span>
                </div>
                <div class="flex justify-between items-center pt-2 mt-2 border-t border-gray-200">
                    <span class="text-gray-500 font-medium">Status</span>
                    <span class="font-medium text-[#0B3D2E] bg-gray-50 px-2 py-0.5 rounded text-[11px] uppercase tracking-wider">Tetap</span>
                </div>
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="button" @click="showProfileModal = false"
                        class="px-5 py-2.5 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium shadow-sm transition w-full">
                    Tutup Profil
                </button>
            </div>
        </div>
    </div>


    <div x-show="showEditModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showEditModal = false">
        <div class="bg-white rounded-md max-w-md w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in duration-200 border border-gray-100" x-show="selectedNode">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800">Edit Pegawai</h3>
                </div>
                <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Nama Lengkap</label>
                    <input type="text" x-model="selectedNode.name" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition shadow-sm">
                </div>
                <div>
                    <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Jabatan / Role</label>
                    <input type="text" x-model="selectedNode.role" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition shadow-sm">
                </div>
                <div>
                    <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Departemen</label>
                    <select x-model="selectedNode.dept" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition shadow-sm">
                        <option>Executive Board</option>
                        <option>Human Resources</option>
                        <option>Finance &amp; Accounting</option>
                        <option>Engineering &amp; IT</option>
                        <option>Sales &amp; Marketing</option>
                        <option>Commercial</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="showEditModal = false"
                        class="px-5 py-2.5 rounded-md bg-gray-100 text-sm font-medium text-gray-700 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="button" @click="showEditModal = false; triggerToast('Data jabatan berhasil diperbarui!', 'success')"
                        class="px-6 py-2.5 rounded-md bg-gray-50 text-white text-sm font-medium hover:bg-gray-50 shadow-sm transition">
                    Simpan
                </button>
            </div>
        </div>
    </div>


    <div x-show="showDeptModal" x-cloak
         class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4"
         @click.self="showDeptModal = false">
        <div class="bg-white rounded-md max-w-md w-full p-8 shadow-sm space-y-6 animate-in fade-in zoom-in duration-200 border border-gray-100">
            <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E]">
                        <span class="material-symbols-outlined text-[20px]">add_business</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800">Tambah Departemen Baru</h3>
                </div>
                <button type="button" @click="showDeptModal = false" class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-4 text-sm">
                <div>
                    <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Nama Departemen</label>
                    <input type="text" placeholder="Contoh: Product & Design" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition shadow-sm">
                </div>
                <div>
                    <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Departemen Induk</label>
                    <select class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition shadow-sm">
                        <option>Executive Board (CEO - Tahsan Khan)</option>
                        <option>Commercial Director (Herry Brooks)</option>
                        <option>Engineering &amp; IT (Herry Kane)</option>
                        <option>Finance &amp; Accounting (David Warner)</option>
                        <option>Human Resources (Tim David)</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Kode Divisi</label>
                        <input type="text" placeholder="Contoh: PRD-05" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none  shadow-sm">
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Kepala Unit (Head)</label>
                        <input type="text" placeholder="Nama Manager" class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none shadow-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" @click="showDeptModal = false"
                        class="px-5 py-2.5 rounded-md bg-gray-100 text-sm font-medium text-gray-700 hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="button" @click="showDeptModal = false; triggerToast('Departemen baru berhasil ditambahkan & terhubung ke Org Chart!')"
                        class="px-6 py-2.5 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium shadow-sm transition">
                    Simpan
                </button>
            </div>
        </div>
    </div>


    <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-6 right-6 z-[100] flex items-center gap-3 px-4 py-3 rounded-md shadow-sm text-white font-medium text-sm border border-gray-200/30 "
         :class="{
             'bg-[#0B3D2E] text-white': toast.type === 'success' || toast.type === 'info',
             'bg-gray-50 border-gray-200/30 text-white': toast.type === 'error'
         }"
         style="display: none;">
        <span class="material-symbols-outlined text-[20px]"
              :class="toast.type === 'error' ? 'text-white' : 'text-emerald-100'"
              x-text="toast.type === 'error' ? 'error' : (toast.type === 'info' ? 'info' : 'check_circle')"></span>
        <span x-text="toast.message" class="text-sm font-medium"></span>
    </div>

</div>
@endsection
