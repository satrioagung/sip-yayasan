@extends('layouts.app')
@section('title', 'Input Pembayaran')
@section('page-title', 'Input Pembayaran')
@section('breadcrumb', 'Beranda / Pembayaran / Tambah')

@section('content')
<div class="max-w-3xl mx-auto" x-data="paymentForm()">

    <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf

        {{-- ===== PILIH TAGIHAN ===== --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-4">
            <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Pilih Tagihan</h3>

            @if($bill)
                {{-- Pre-filled dari bills.index --}}
                <input type="hidden" name="bill_id" value="{{ $bill->id }}">
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 border-blue-200 bg-blue-50">
                    <img src="{{ $bill->student->foto_url }}" class="w-12 h-12 rounded-xl object-cover shadow" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800">{{ $bill->student->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">{{ $bill->student->nis }} — {{ $bill->student->class?->nama_kelas ?? '-' }}</p>
                        <p class="text-sm font-semibold text-blue-700 mt-1">{{ $bill->paymentType->nama }} — {{ $bill->periode }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-500">Sisa Tagihan</p>
                        <p class="text-lg font-bold text-red-600">{{ $bill->sisa_format }}</p>
                        <p class="text-xs text-gray-400">dari {{ $bill->nominal_format }}</p>
                    </div>
                </div>
            @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Pilih Tagihan <span class="text-red-500">*</span>
                    </label>
                    <select name="bill_id" required @change="onBillChange($event)"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('bill_id') ? 'border-red-400' : '' }}">
                        <option value="">— Pilih Tagihan Siswa —</option>
                        @foreach($bills as $b)
                            <option value="{{ $b->id }}"
                                    data-sisa="{{ $b->nominal - $b->nominal_terbayar }}"
                                    data-nominal="{{ $b->nominal }}"
                                    data-label="{{ $b->student->nama_lengkap }} — {{ $b->paymentType->nama }} ({{ $b->periode }}) — Sisa: Rp {{ number_format($b->nominal - $b->nominal_terbayar, 0, ',', '.') }}">
                                {{ $b->student->nama_lengkap }} — {{ $b->paymentType->nama }} ({{ $b->periode }}) — Sisa: Rp {{ number_format($b->nominal - $b->nominal_terbayar, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('bill_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror

                    {{-- Info sisa tagihan setelah dipilih --}}
                    <div x-show="selectedBill" x-transition class="mt-3 p-3 bg-blue-50 rounded-xl border border-blue-200 text-sm">
                        <span class="text-gray-600">Sisa tagihan: </span>
                        <span class="font-bold text-blue-700" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBill?.sisa ?? 0)"></span>
                        <span class="text-xs text-gray-500 ml-2">
                            (dari <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBill?.nominal ?? 0)"></span>)
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- ===== DETAIL PEMBAYARAN ===== --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-semibold text-gray-700 pb-3 border-b border-gray-100">Detail Pembayaran</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Tanggal Bayar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tanggal Bayar <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="tanggal_bayar"
                           value="{{ old('tanggal_bayar', now()->format('d/m/Y')) }}"
                           x-mask="99/99/9999"
                           placeholder="dd/mm/yyyy" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('tanggal_bayar') ? 'border-red-400' : '' }}">
                    @error('tanggal_bayar') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Metode Bayar --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Metode Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <select name="metode_bayar" required
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Models\Payment::$metodeBayarLabels as $val => $label)
                            <option value="{{ $val }}" {{ old('metode_bayar', 'tunai') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('metode_bayar') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Nominal Bayar --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nominal Pembayaran <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                        <input type="text" id="nominal_display"
                               value="{{ old('nominal_bayar') ? number_format((int)old('nominal_bayar'), 0, ',', '.') : '' }}"
                               placeholder="0"
                               @input="nominalDisplay = formatRupiah($event.target.value); $refs.nominalHidden.value = unformat($event.target.value)"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm text-right font-semibold text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('nominal_bayar') ? 'border-red-400' : '' }}">
                        <input type="hidden" name="nominal_bayar" x-ref="nominalHidden"
                               value="{{ old('nominal_bayar', '') }}">
                    </div>
                    @error('nominal_bayar') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror

                    {{-- Shortcut bayar penuh --}}
                    @if($bill)
                    <button type="button"
                            @click="setNominalPenuh({{ $bill->sisa }})"
                            class="mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium underline">
                        Bayar penuh ({{ $bill->sisa_format }})
                    </button>
                    @else
                    <button type="button" x-show="selectedBill"
                            @click="setNominalPenuh(selectedBill?.sisa ?? 0)"
                            class="mt-2 text-xs text-blue-600 hover:text-blue-800 font-medium underline">
                        Bayar penuh (<span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(selectedBill?.sisa ?? 0)"></span>)
                    </button>
                    @endif
                </div>

                {{-- Bukti Transfer --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Bukti Transfer (Opsional)
                    </label>
                    <div x-data="{ preview: null }">
                        <input type="file" id="bukti_file" name="bukti_file"
                               accept="image/jpeg,image/png,image/webp"
                               class="hidden"
                               @change="
                                   const f = $event.target.files[0];
                                   preview = f ? URL.createObjectURL(f) : null;
                               ">
                        <label for="bukti_file"
                               class="cursor-pointer flex items-center gap-3 border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-blue-400 hover:bg-blue-50 transition-all">
                            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">Klik untuk upload bukti</p>
                                <p class="text-xs text-gray-400">JPG, PNG, WebP — Maks. 2 MB</p>
                            </div>
                        </label>
                        <img x-show="preview" :src="preview" alt="Preview"
                             class="mt-3 h-32 rounded-xl object-contain border border-gray-200">
                    </div>
                    @error('bukti_file') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Keterangan --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                    <input type="text" name="keterangan"
                           value="{{ old('keterangan') }}"
                           placeholder="Keterangan tambahan (opsional)"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('payments.index') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary px-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Simpan Pembayaran
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
function paymentForm() {
    return {
        selectedBill: null,
        nominalDisplay: '',

        onBillChange(e) {
            const opt = e.target.options[e.target.selectedIndex];
            if (opt?.value) {
                this.selectedBill = {
                    sisa:    parseInt(opt.dataset.sisa),
                    nominal: parseInt(opt.dataset.nominal),
                };
            } else {
                this.selectedBill = null;
            }
        },

        setNominalPenuh(sisa) {
            this.nominalDisplay = new Intl.NumberFormat('id-ID').format(sisa);
            document.getElementById('nominal_display').value = this.nominalDisplay;
            this.$refs.nominalHidden.value = sisa;
        },

        formatRupiah(val) {
            const raw = val.replace(/\D/g, '');
            return raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw)) : '';
        },

        unformat(val) {
            return val.replace(/\D/g, '');
        },
    }
}
</script>
@endpush
