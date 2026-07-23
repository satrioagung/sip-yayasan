@extends('layouts.app')
@section('title', 'Generate Tagihan')
@section('page-title', 'Generate Tagihan')
@section('breadcrumb', 'Beranda / Tagihan / Generate')

@section('content')
<div class="max-w-3xl mx-auto space-y-5"
     x-data="generateForm()"
     x-init="init()">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
        <div>
            <h2 class="text-base font-bold text-gray-800">Generate Tagihan Massal</h2>
            <p class="text-sm text-gray-500 mt-0.5">Buat tagihan untuk siswa aktif berdasarkan jenis pembayaran yang dipilih.</p>
        </div>

        <form method="POST" action="{{ route('bills.generate.store') }}" id="generate-form" class="space-y-5">
            @csrf

            {{-- Jenis Pembayaran --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jenis Pembayaran <span class="text-red-500">*</span>
                </label>
                <select name="payment_type_id" id="payment_type_id" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="onPaymentTypeChange(); triggerPreview()">
                    <option value="">— Pilih Jenis Pembayaran —</option>
                    @foreach($paymentTypes as $pt)
                        <option value="{{ $pt->id }}"
                                data-tipe="{{ $pt->tipe }}"
                                data-nominal="{{ $pt->nominal_default }}">
                            {{ $pt->nama }} ({{ $pt->tipe_label }} — {{ $pt->nominal_format }})
                        </option>
                    @endforeach
                </select>
                @error('payment_type_id') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                {{-- Tahun Ajaran --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Ajaran</label>
                    <select name="school_year_id"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Pilih Tahun Ajaran —</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy->id }}" {{ $sy->aktif ? 'selected' : '' }}>
                                {{ $sy->nama }}{{ $sy->aktif ? ' (Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="tahun" id="tahun" required
                           value="{{ now()->year }}" min="2000" max="2100"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           @change="triggerPreview()">
                    @error('tahun') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Bulan (tampil jika tipe = bulanan) --}}
                <div x-show="isBulanan" x-transition>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Bulan <span class="text-red-500" x-show="isBulanan">*</span>
                    </label>
                    <select name="bulan" id="bulan"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                            @change="triggerPreview()">
                        <option value="">— Pilih Bulan —</option>
                        @foreach(\App\Models\Bill::$bulanLabels as $num => $label)
                            <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('bulan') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Nominal --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nominal <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500 font-medium">Rp</span>
                        <input type="text" id="nominal_display"
                               placeholder="0"
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                               x-model="nominalDisplay"
                               @input="nominalDisplay = formatRupiah($event.target.value); $refs.nominalHidden.value = unformat($event.target.value)">
                        <input type="hidden" name="nominal" x-ref="nominalHidden" :value="nominalRaw">
                    </div>
                    @error('nominal') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Jatuh Tempo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jatuh Tempo</label>
                    <input type="date" name="jatuh_tempo"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('jatuh_tempo') <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Cakupan Generate --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cakupan Generate</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" x-data>
                    @foreach(['semua' => 'Semua Siswa Aktif', 'kelas' => 'Per Kelas', 'siswa' => 'Per Siswa'] as $val => $label)
                    <label class="flex items-center gap-3 border rounded-xl p-3.5 cursor-pointer transition-all"
                           :class="scope === '{{ $val }}' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-blue-200'">
                        <input type="radio" name="scope" value="{{ $val }}"
                               x-model="scope" @change="triggerPreview()"
                               class="text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Filter per Kelas --}}
            <div x-show="scope === 'kelas'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Kelas</label>
                <select name="class_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="triggerPreview()">
                    <option value="">— Pilih Kelas —</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter per Siswa --}}
            <div x-show="scope === 'siswa'" x-transition>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Siswa</label>
                <select name="student_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        @change="triggerPreview()">
                    <option value="">— Pilih Siswa —</option>
                    @foreach($students as $s)
                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }} — {{ $s->nis }} ({{ $s->class?->nama_kelas ?? 'Tanpa Kelas' }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Keterangan --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan (Opsional)</label>
                <input type="text" name="keterangan"
                       placeholder="contoh: SPP bulan Juli 2024"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Preview Panel --}}
            <div x-show="previewData" x-transition class="rounded-xl border overflow-hidden"
                 :class="previewData?.akan_dibuat > 0 ? 'border-blue-200 bg-blue-50' : 'border-amber-200 bg-amber-50'">
                <div class="p-4">
                    <p class="text-sm font-semibold mb-3"
                       :class="previewData?.akan_dibuat > 0 ? 'text-blue-800' : 'text-amber-800'">
                        Preview Tagihan
                    </p>
                    <div class="grid grid-cols-3 gap-3 mb-3">
                        <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                            <p class="text-xl font-bold text-gray-800" x-text="previewData?.total ?? 0"></p>
                            <p class="text-xs text-gray-500">Total Siswa</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                            <p class="text-xl font-bold text-green-600" x-text="previewData?.akan_dibuat ?? 0"></p>
                            <p class="text-xs text-gray-500">Akan Dibuat</p>
                        </div>
                        <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                            <p class="text-xl font-bold text-amber-600" x-text="previewData?.sudah_ada ?? 0"></p>
                            <p class="text-xs text-gray-500">Sudah Ada</p>
                        </div>
                    </div>

                    {{-- Daftar siswa preview (max 10) --}}
                    <div class="space-y-1 max-h-48 overflow-y-auto">
                        <template x-for="s in previewData?.siswa ?? []" :key="s.nis">
                            <div class="flex items-center gap-2 bg-white rounded-lg px-3 py-2 text-xs"
                                 :class="s.is_dup ? 'opacity-50' : ''">
                                <span class="w-1.5 h-1.5 rounded-full flex-shrink-0"
                                      :class="s.is_dup ? 'bg-amber-400' : 'bg-green-500'"></span>
                                <span class="font-medium text-gray-700 flex-1" x-text="s.nama"></span>
                                <span class="text-gray-400 font-mono" x-text="s.nis"></span>
                                <span class="text-gray-400" x-text="s.kelas"></span>
                                <span x-show="s.is_dup" class="text-amber-600 font-semibold">Sudah Ada</span>
                            </div>
                        </template>
                        <p x-show="(previewData?.total ?? 0) > 10"
                           class="text-xs text-center text-gray-400 pt-1">
                            ... dan <span x-text="(previewData?.total ?? 0) - 10"></span> siswa lainnya
                        </p>
                    </div>
                </div>
            </div>

            {{-- Loading indicator --}}
            <div x-show="loading" class="flex items-center gap-2 text-sm text-blue-600">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Menghitung preview...
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('bills.index') }}" class="btn-secondary">Batal</a>
                <button type="submit"
                        :disabled="!previewData || previewData.akan_dibuat === 0"
                        class="btn-primary disabled:opacity-40 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generate <span x-text="previewData?.akan_dibuat ? `(${previewData.akan_dibuat} tagihan)` : ''"></span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function generateForm() {
    return {
        scope: 'semua',
        isBulanan: false,
        nominalDisplay: '',
        nominalRaw: 0,
        previewData: null,
        loading: false,
        debounceTimer: null,

        init() {
            // Auto trigger preview when scope changes
        },

        onPaymentTypeChange() {
            const sel = document.getElementById('payment_type_id');
            const opt = sel.options[sel.selectedIndex];
            const tipe    = opt?.dataset?.tipe;
            const nominal = parseInt(opt?.dataset?.nominal ?? 0);

            this.isBulanan   = tipe === 'bulanan';
            this.nominalRaw  = nominal;
            this.nominalDisplay = nominal ? new Intl.NumberFormat('id-ID').format(nominal) : '';
            document.querySelector('[x-ref="nominalHidden"]').value = nominal;
        },

        triggerPreview() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => this.doPreview(), 400);
        },

        async doPreview() {
            const ptId = document.getElementById('payment_type_id')?.value;
            const tahun = document.getElementById('tahun')?.value;
            if (!ptId || !tahun) return;

            const formEl   = document.getElementById('generate-form');
            const formData = new FormData(formEl);
            const params   = new URLSearchParams();

            params.set('payment_type_id', ptId);
            params.set('tahun', tahun);

            const bulan = document.getElementById('bulan')?.value;
            if (bulan) params.set('bulan', bulan);

            const scope = formData.get('scope') ?? 'semua';
            if (scope === 'kelas')  params.set('class_id',   formData.get('class_id')   ?? '');
            if (scope === 'siswa')  params.set('student_id', formData.get('student_id') ?? '');

            this.loading     = true;
            this.previewData = null;

            try {
                const res = await fetch('{{ route("bills.preview") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json',
                    },
                    body: params.toString(),
                });

                if (res.ok) {
                    this.previewData = await res.json();
                }
            } catch(e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },

        formatRupiah(val) {
            const raw = val.replace(/\D/g, '');
            this.nominalRaw = parseInt(raw || '0');
            return raw ? new Intl.NumberFormat('id-ID').format(parseInt(raw)) : '';
        },

        unformat(val) {
            return val.replace(/\D/g, '');
        },
    }
}
</script>
@endpush
