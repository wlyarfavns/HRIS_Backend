@extends('layouts.hr')

@section('title', 'Payroll')
@section('page-title', 'Payroll')
@section('page-desc', 'Komponen gaji, eksekusi engine, dan tinjau hasil kalkulasi periode berjalan.')

@php
    $categoryColor = [
        'Pendapatan Tetap' => ['pill' => 'bg-gray-50 text-gray-700 border border-gray-200', 'dot' => 'bg-gray-50'],
        'Pendapatan Variabel' => ['pill' => 'bg-gray-50 text-gray-700 border border-gray-200', 'dot' => 'bg-gray-50'],
        'Potongan' => ['pill' => 'bg-gray-50 text-gray-700 border border-gray-200', 'dot' => 'bg-gray-50'],
    ];
@endphp

@section('content')
    <div x-data="{

                    view: 'rules',


                    showAddModal: false,
                    showEditModal: false,
                    editItem: {},
                    openEdit(item) { 
                        this.editItem = { ...item }; 
                        this.showEditModal = true; 
                    },


                    showDetail: false,
                    selectedEmp: null,
                    openDetail(item) { 
                        this.selectedEmp = {
                            name: item.employee?.full_name || '-',
                            nip: item.employee?.employee_id || '-',
                            dept: item.employee?.department?.name || '-',
                            basic: parseFloat(item.basic_salary) || 0,
                            allowance: parseFloat(item.total_allowances) || 0,
                            deduction: parseFloat(item.total_deductions) || 0,
                            net: parseFloat(item.net_salary) || 0,
                            status: item.status
                        };
                        this.showDetail = true; 
                    },


                    showSubmitConfirm: false,
                    submitted: false,
                    submitToFinance() { 
                        this.showSubmitConfirm = false; 
                        document.getElementById('approveHrForm').submit();
                    },


                    toast: { show: false, message: '', type: 'success' },
                    showToast(msg, type = 'success') { 
                        this.toast.message = msg; 
                        this.toast.type = type;
                        this.toast.show = true; 
                        setTimeout(() => this.toast.show = false, 3000); 
                    }
                }" x-init="let sessionMsg = @json(session('success')); if (sessionMsg) showToast(sessionMsg)">




        <div x-show="view === 'rules'">
            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
                    <div>
                        <h2 class="text-base font-medium text-gray-800">Komponen & Master Gaji</h2>
                        <p class="text-xs text-gray-500 mt-1">Ketentuan kalkulasi dan komponen default yang
                            fleksibel</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" @click="showAddModal = true"
                            class="border border-gray-200 bg-white hover:bg-gray-50 px-4 py-2.5 rounded-md text-sm font-medium text-gray-700 flex items-center gap-2 transition shadow-sm">
                            <span class="material-symbols-outlined text-[18px] text-[#0B3D2E]">add_circle</span>
                            Tambah Aturan
                        </button>
                        @if($payrolls->count() > 0)
                            <button type="button" @click="view = 'review'"
                                class="border border-[#0B3D2E] bg-white text-[#0B3D2E] hover:bg-[#0B3D2E] hover:text-white text-sm font-medium px-5 py-2.5 rounded-md flex items-center gap-2 shadow-sm transition">
                                <span class="material-symbols-outlined text-[18px]">fact_check</span>
                                Lihat Draft Payroll
                            </button>
                        @endif
                        <a href="{{ route('hr.payroll.showRun') }}"
                            class="bg-[#0B3D2E] text-white text-sm font-medium px-5 py-2.5 rounded-md flex items-center gap-2 hover:bg-[#043927] shadow-sm transition">
                            <span class="material-symbols-outlined text-[18px]">play_circle</span>
                            Mulai Proses Payroll
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr
                                class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-6 py-4">Komponen</th>
                                <th class="px-6 py-4">Kategori</th>
                                <th class="px-6 py-4">Tipe</th>
                                <th class="px-6 py-4">Nominal / Rumus</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 text-center w-24">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach ($components as $c)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2.5">
                                            <span
                                                class="w-2 h-2 rounded-full flex-shrink-0 {{ $categoryColor[$c->category]['dot'] ?? 'bg-gray-300' }}"></span>
                                            <span class="font-medium text-gray-800 text-sm">{{ $c->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-[10px] font-medium px-2.5 py-1 rounded-lg uppercase tracking-wider {{ $categoryColor[$c->category]['pill'] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $c->category }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs ">
                                        {{ $c->calculation_type ?? $c->type }}
                                    </td>
                                    <td class="px-6 py-4  text-sm text-gray-800 font-semibold">
                                        {{ $c->formula_note ?? 'Rp ' . number_format($c->default_amount, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 text-xs">{{ $c->description ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button" @click='openEdit(@json($c))'
                                                class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-[#0B3D2E] hover:bg-gray-50 transition"
                                                title="Edit">
                                                <span class="material-symbols-outlined text-[18px]">edit</span>
                                            </button>
                                            <form method="POST" action="{{ route('hr.payroll.components.destroy', $c->id) }}"
                                                onsubmit="return confirm('Hapus komponen {{ $c->name }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-50 transition"
                                                    title="Hapus">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>




        <div x-show="view === 'review'" x-cloak>
            <button type="button" @click="view = 'rules'"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-[#0B3D2E] transition mb-6">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                Kembali ke Komponen Gaji
            </button>

            <div class="bg-white rounded-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap bg-gray-50/50">
                    <div>
                        <h2 class="text-base font-medium text-gray-800">Pratinjau Payroll
                            {{ $start->translatedFormat('F Y') }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">Klik ikon mata untuk melihat detail per
                            karyawan</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr
                                class="bg-gray-50 text-[11px] font-medium text-gray-500 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-6 py-4">Karyawan</th>
                                <th class="px-6 py-4">Departemen</th>
                                <th class="px-6 py-4 text-right">Pendapatan</th>
                                <th class="px-6 py-4 text-right">Potongan</th>
                                <th class="px-6 py-4 text-right">Take Home Pay</th>
                                <th class="px-6 py-4 text-center w-24">Slip</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach ($payrolls as $p)
                                @php
                                    $gross = $p->basic_salary + $p->total_allowances;
                                    $deduct = $p->total_deductions;
                                    $initials = strtoupper(substr($p->employee->full_name ?? 'U', 0, 2));
                                @endphp
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-9 h-9 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center font-medium text-xs text-[#0B3D2E] shrink-0">
                                                {{ $initials }}
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800 text-sm leading-tight">
                                                    {{ $p->employee->full_name }}
                                                </p>
                                                <p class="text-[11px]  text-gray-500 mt-0.5">
                                                    {{ $p->employee->employee_id }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $p->employee->department->name ?? '-' }}
                                    </td>
                                    <td
                                        class="px-6 py-4 text-right  text-sm text-gray-600 font-semibold">
                                        Rp{{ number_format($gross, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right  text-sm text-gray-700 font-semibold">
                                        -Rp{{ number_format($deduct, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right  font-semibold text-sm text-[#0B3D2E]">
                                        Rp{{ number_format($p->net_salary, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button type="button" @click='openDetail(@json($p))'
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-gray-400 hover:text-[#0B3D2E] hover:bg-gray-50 transition">
                                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            @if($payrolls->isEmpty())
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500">Belum ada
                                        data kalkulasi payroll.</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td class="px-6 py-4 text-sm font-semibold text-gray-800">TOTAL
                                    ({{ $payrolls->count() }} Karyawan)</td>
                                <td></td>
                                <td class="px-6 py-4 text-right  text-sm font-semibold text-gray-800">
                                    Rp{{ number_format($totalGross, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right  text-sm font-semibold text-gray-700">
                                    -Rp{{ number_format($totalDeduct, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right  font-semibold text-[#0B3D2E]">
                                    Rp{{ number_format($totalNet, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 px-6 pb-6">
                    {{ $payrolls->links() }}
                </div>

                <div
                    class="px-6 py-5 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between gap-4 flex-wrap">
                    <p class="text-xs text-gray-500">
                        Data yang disubmit akan di-lock dan diteruskan ke Finance untuk Persetujuan & Pencairan Dana.
                    </p>
                    <div class="flex items-center gap-3">
                        <form method="GET" action="{{ route('hr.payroll.export') }}" target="_blank">
                            <input type="hidden" name="period" value="{{ $end->format('Y-m') }}">

                            <button type="submit"
                                class="px-5 py-2.5 rounded-md text-sm font-medium flex items-center gap-2 transition bg-white hover:bg-gray-50 text-gray-700 border border-gray-200 shadow-sm">
                                <span class="material-symbols-outlined text-[18px] text-[#0B3D2E]">download</span>
                                Unduh XLSX
                            </button>
                        </form>

                        <form id="approveHrForm" method="POST" action="{{ route('hr.payroll.approveHr') }}">
                            @csrf
                            <input type="hidden" name="period" value="{{ $start->format('Y-m') }}">
                        </form>

                        <button type="button" @click="showSubmitConfirm = true" {{ $payrolls->isEmpty() ? 'disabled' : '' }}
                            class="text-sm font-medium px-6 py-2.5 rounded-md flex items-center gap-2 transition {{ $payrolls->isEmpty() ? 'opacity-50 cursor-not-allowed bg-gray-100 text-gray-500' : 'bg-[#0B3D2E] hover:bg-[#043927] text-white shadow-sm' }}">
                            <span class="material-symbols-outlined text-[18px]">send</span>
                            Kirim ke Finance
                        </button>
                    </div>
                </div>
            </div>
        </div>




        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
            @click.self="showAddModal = false">
            <div class="bg-white rounded-md max-w-md w-full p-8 shadow-sm space-y-6 border border-gray-100">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-[#0B3D2E]">
                            <span class="material-symbols-outlined text-[20px]">add_circle</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800">Tambah Komponen Gaji</h3>
                    </div>
                    <button type="button" @click="showAddModal = false"
                        class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form method="POST" action="{{ route('hr.payroll.components.store') }}" class="space-y-4 text-sm">
                    @csrf
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Nama Komponen</label>
                        <input type="text" name="name" required placeholder="Contoh: Tunjangan Komunikasi"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Kategori</label>
                            <select name="category" required
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                                <option value="Pendapatan Tetap">Pendapatan Tetap</option>
                                <option value="Pendapatan Variabel">Pendapatan Variabel</option>
                                <option value="Potongan">Potongan</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Tipe
                                (kalkulasi)</label>
                            <select name="type" required
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                                <option value="earning">Earning (Penambah)</option>
                                <option value="deduction">Deduction (Pengurang)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Tipe Kalkulasi
                            (tampilan)</label>
                        <input type="text" name="calculation_type" placeholder="Contoh: Fixed Nominal, Harian, Persentase"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Nominal Default</label>
                            <input type="number" step="0.01" name="default_amount" placeholder="250000"
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                        </div>
                        <div class="flex items-end pb-2.5">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-600 cursor-pointer">
                                <input type="checkbox" name="is_taxable" value="1" class="rounded border-gray-300 text-[#0B3D2E] focus:ring-[#0B3D2E]/20 w-4 h-4 transition">
                                Kena Pajak (PPh21)
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Catatan Rumus</label>
                        <input type="text" name="formula_note" placeholder="Contoh: 1/173 × Gaji Pokok × Jam"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Keterangan
                            (opsional)</label>
                        <input type="text" name="description" placeholder="Contoh: Diperhitungkan jika SPL diapprove"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-[#0B3D2E]/20 focus:border-[#0B3D2E] focus:outline-none transition">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showAddModal = false"
                            class="px-5 py-2.5 rounded-md bg-gray-100 text-sm font-medium text-gray-700 hover:bg-gray-200 transition">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-md bg-[#0B3D2E] text-white text-sm font-medium hover:bg-[#043927] shadow-sm transition">Simpan
                            Komponen</button>
                    </div>
                </form>
            </div>
        </div>




        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-gray-900/60 z-50 flex items-center justify-center p-4 "
            @click.self="showEditModal = false">
            <div class="bg-white rounded-md max-w-md w-full p-8 shadow-sm space-y-6 border border-gray-100" x-show="editItem?.id">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-700">
                            <span class="material-symbols-outlined text-[20px]">edit</span>
                        </div>
                        <h3 class="text-lg font-medium text-gray-800">Edit Komponen Gaji</h3>
                    </div>
                    <button type="button" @click="showEditModal = false"
                        class="text-gray-400 hover:text-gray-800 w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="editItem?.id ? '{{ url('hr/penggajian/komponen') }}/' + editItem.id : '#'" method="POST"
                    class="space-y-4 text-sm">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Nama Komponen</label>
                        <input type="text" name="name" required x-bind:value="editItem?.name"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Kategori</label>
                            <select name="category" required
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                                <template x-for="opt in ['Pendapatan Tetap','Pendapatan Variabel','Potongan']" :key="opt">
                                    <option :value="opt" :selected="editItem?.category === opt" x-text="opt"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Tipe</label>
                            <select name="type" required
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                                <option value="earning" :selected="editItem?.type === 'earning'">Earning (Penambah)</option>
                                <option value="deduction" :selected="editItem?.type === 'deduction'">Deduction (Pengurang)
                                </option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Tipe Kalkulasi</label>
                        <input type="text" name="calculation_type" x-bind:value="editItem?.calculation_type"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Nominal Default</label>
                            <input type="number" step="0.01" name="default_amount" x-bind:value="editItem?.default_amount"
                                class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                        </div>
                        <div class="flex items-end pb-2.5">
                            <label class="flex items-center gap-2 text-sm font-medium text-gray-600 cursor-pointer">
                                <input type="checkbox" name="is_taxable" value="1" x-bind:checked="editItem?.is_taxable"
                                    class="rounded border-gray-300 text-gray-700 focus:ring-blue-500/20 w-4 h-4 transition">
                                Kena Pajak (PPh21)
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Catatan Rumus</label>
                        <input type="text" name="formula_note" x-bind:value="editItem?.formula_note"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                    </div>
                    <div>
                        <label class="font-medium text-gray-400 uppercase text-[11px] block mb-1.5 tracking-wide">Keterangan
                            (opsional)</label>
                        <input type="text" name="description" x-bind:value="editItem?.description"
                            class="w-full border border-gray-200 rounded-md px-4 py-2.5 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500/20 focus:border-gray-200 focus:outline-none transition">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" @click="showEditModal = false"
                            class="px-5 py-2.5 rounded-md bg-gray-100 text-sm font-medium text-gray-700 hover:bg-gray-200 transition">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-md bg-gray-50 text-white text-sm font-medium hover:bg-gray-50 shadow-sm transition">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>


        <div x-show="showDetail" x-cloak class="fixed inset-0 bg-gray-900/60  z-40"
            @click="showDetail = false"></div>
        <div x-show="showDetail" x-cloak
            class="fixed right-0 top-0 h-full w-full max-w-md bg-white shadow-sm z-50 flex flex-col border-l border-gray-200"
            x-transition:enter="transition ease-out duration-300 translate-x-full"
            x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-full">

            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between flex-shrink-0 bg-gray-50/50">
                <div class="flex items-center gap-4" x-show="selectedEmp">
                    <div
                        class="w-12 h-12 rounded-full bg-gray-50 border border-gray-200 flex items-center justify-center font-medium text-[#0B3D2E] text-lg">
                        <span x-text="selectedEmp?.name.substring(0, 2).toUpperCase()"></span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-base" x-text="selectedEmp?.name"></p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span x-text="selectedEmp?.nip"></span> · <span x-text="selectedEmp?.dept"></span>
                        </p>
                    </div>
                </div>
                <button @click="showDetail = false"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-800 hover:bg-gray-100 transition">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto" x-show="selectedEmp">
                <div class="px-6 pt-6 pb-4">
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-widest mb-4">Pendapatan
                    </p>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-gray-600">Gaji Pokok & Tunjangan Tetap</span>
                        <span class=" text-sm font-medium text-gray-800"
                            x-text="'Rp' + (selectedEmp?.basic || 0).toLocaleString('id-ID')"></span>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-sm text-gray-600">Tunjangan Lainnya / Lembur</span>
                        <span class=" text-sm font-medium text-gray-800"
                            x-text="'Rp' + (selectedEmp?.allowance || 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-between items-center bg-gray-50 border-y border-gray-100">
                    <span class="text-sm font-medium text-gray-800">Total Pendapatan</span>
                    <span class=" text-sm font-medium text-gray-800"
                        x-text="'Rp' + (selectedEmp?.basic + selectedEmp?.allowance).toLocaleString('id-ID')"></span>
                </div>

                <div class="px-6 pt-6 pb-4">
                    <p class="text-[11px] font-medium text-gray-400 uppercase tracking-widest mb-4">Potongan</p>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Total Potongan (BPJS/PPh21)</span>
                        <span class=" text-sm font-medium text-gray-700"
                            x-text="'-Rp' + (selectedEmp?.deduction).toLocaleString('id-ID')"></span>
                    </div>
                </div>
                <div class="px-6 py-4 flex justify-between items-center bg-gray-50 border-y border-gray-100">
                    <span class="text-sm font-medium text-gray-800">Total Potongan</span>
                    <span class=" text-sm font-medium text-gray-700"
                        x-text="'-Rp' + (selectedEmp?.deduction).toLocaleString('id-ID')"></span>
                </div>

                <div class="mx-6 my-6 rounded-md border border-gray-200 bg-gray-50 px-6 py-5 shadow-sm">
                    <div class="flex flex-col gap-2 text-center">
                        <p class="text-sm font-medium text-gray-600">Take Home Pay</p>
                        <p class=" text-3xl font-semibold text-[#0B3D2E]"
                            x-text="'Rp' + (selectedEmp?.net).toLocaleString('id-ID')"></p>
                    </div>
                </div>
            </div>
        </div>


        <div x-show="showSubmitConfirm" x-cloak
            class="fixed inset-0 bg-gray-900/60  z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-md max-w-sm w-full shadow-sm overflow-hidden p-8 text-center border border-gray-100">
                <div class="w-16 h-16 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-5 border border-gray-200">
                    </div>
                <h3 class="text-lg font-medium text-gray-800 mb-2">Konfirmasi Kirim ke Finance</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    Data payroll ini akan di-<strong>lock</strong> dan diteruskan ke Finance untuk Persetujuan & Pencairan
                    Dana.
                </p>
                <div class="flex items-center gap-3">
                    <button type="button" @click="showSubmitConfirm = false"
                        class="flex-1 px-5 py-3 rounded-md border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">Batal</button>
                    <button type="button" @click="submitToFinance()"
                        class="flex-1 px-5 py-3 rounded-md bg-[#0B3D2E] hover:bg-[#043927] text-white text-sm font-medium shadow-sm flex items-center justify-center gap-2 transition">
                        <span class="material-symbols-outlined text-[18px]">send</span> Kirim
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
            class="fixed bottom-6 right-6 z-[70] flex items-center gap-3 px-4 py-3 rounded-md shadow-sm text-white font-medium text-sm "
            :class="toast.type === 'error' ? 'bg-gray-50 border border-gray-200/30' : 'bg-[#0B3D2E] border border-gray-200/30'">
            <span class="material-symbols-outlined text-[20px]"
                :class="toast.type === 'error' ? 'text-white' : 'text-emerald-100'"
                x-text="toast.type === 'error' ? 'error' : 'check_circle'"></span>
            <span x-text="toast.message" class="text-sm font-medium"></span>
        </div>

    </div>
@endsection
