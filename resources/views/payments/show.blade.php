@extends('layouts.app')
@section('title', 'Detail Pembayaran — ' . $payment->nomor_transaksi)
@section('page-title', 'Detail Pembayaran')
@section('breadcrumb', 'Beranda / Pembayaran / Detail')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- ===== BANNER SUKSES ===== --}}
    @if(session('success'))
    <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-2xl px-5 py-4">
        <div class="w-9 h-9 bg-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-green-800">Pembayaran Berhasil Dicatat!</p>
            <p class="text-sm text-green-600">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- ===== INFO TRANSAKSI ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Header card --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-5 text-white">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <p class="text-blue-200 text-xs uppercase tracking-wider font-medium">Nomor Transaksi</p>
                    <p class="text-2xl font-bold tracking-wide mt-0.5">{{ $payment->nomor_transaksi }}</p>
                </div>
                <div class="text-right">
                    <p class="text-blue-200 text-xs uppercase tracking-wider font-medium">Nominal Dibayar</p>
                    <p class="text-2xl font-bold mt-0.5">{{ $payment->nominal_bayar_format }}</p>
                </div>
            </div>
        </div>

        {{-- Detail grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">

            {{-- Kiri: Siswa & Tagihan --}}
            <div class="p-6 space-y-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Siswa & Tagihan</h4>

                <div class="flex items-center gap-3">
                    <img src="{{ $payment->student?->foto_url }}" class="w-12 h-12 rounded-xl object-cover shadow-sm" alt="">
                    <div>
                        <p class="font-bold text-gray-800">{{ $payment->student?->nama_lengkap }}</p>
                        <p class="text-xs text-gray-400">{{ $payment->student?->nis }} — {{ $payment->student?->class?->nama_kelas ?? '-' }}</p>
                    </div>
                </div>

                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Jenis Pembayaran</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->bill?->paymentType?->nama ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Periode</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->bill?->periode ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total Tagihan</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->bill?->nominal_format ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total Terbayar</dt>
                        <dd class="font-medium text-green-600">{{ $payment->bill?->nominal_terbayar_format ?? '-' }}</dd>
                    </div>
                    @if(($payment->bill?->sisa ?? 0) > 0)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sisa Tagihan</dt>
                        <dd class="font-semibold text-red-600">{{ $payment->bill?->sisa_format }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between items-center pt-1">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            @php $sc = $payment->bill?->status_config ?? \App\Models\Bill::$statusConfig['belum_bayar']; @endphp
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                {{ $sc['label'] }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Kanan: Detail Transaksi --}}
            <div class="p-6 space-y-4">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Detail Transaksi</h4>

                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tanggal Bayar</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->tanggal_bayar_format }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Metode</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->metode_bayar_label }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Petugas</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->petugas?->name ?? 'Sistem' }}</dd>
                    </div>
                    @if($payment->keterangan)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Keterangan</dt>
                        <dd class="font-medium text-gray-800">{{ $payment->keterangan }}</dd>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Dicatat</dt>
                        <dd class="font-medium text-gray-600 text-xs">{{ $payment->created_at->translatedFormat('d F Y, H:i') }}</dd>
                    </div>
                </dl>

                {{-- Bukti Transfer --}}
                @if($payment->bukti_url)
                <div>
                    <p class="text-xs text-gray-500 mb-1.5">Bukti Transfer</p>
                    <a href="{{ $payment->bukti_url }}" target="_blank">
                        <img src="{{ $payment->bukti_url }}" alt="Bukti Transfer"
                             class="w-full h-36 object-cover rounded-xl border border-gray-200 hover:opacity-90 transition-opacity">
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ===== CETAK STRUK ===== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <h4 class="text-sm font-semibold text-gray-700 mb-4">Cetak Struk Pembayaran</h4>
        <form action="{{ route('payments.struk', $payment) }}" method="GET" target="_blank" class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs text-gray-500 mb-1.5">Template Struk</label>
                <select name="template_id"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Template Default —</option>
                    @foreach($templates as $t)
                        <option value="{{ $t->id }}" {{ $t->is_default ? 'selected' : '' }}>
                            {{ $t->nama_template }} ({{ $t->ukuran_label }}){{ $t->is_default ? ' ✓ Default' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Unduh Struk PDF
            </button>
        </form>
    </div>

    {{-- ===== AKSI BAWAH ===== --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('payments.index') }}"
           class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali ke Riwayat
        </a>
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.create', ['bill_id' => $payment->bill_id]) }}"
               class="btn-primary text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Bayar Lagi (Cicilan)
            </a>
            <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                  data-confirm="Batalkan pembayaran {{ $payment->nomor_transaksi }}? Status tagihan akan di-rollback.">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batalkan
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
