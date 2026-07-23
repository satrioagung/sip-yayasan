@extends('layouts.app')
@section('title', 'Template Struk')
@section('page-title', 'Template Struk')
@section('breadcrumb', 'Beranda / Keuangan / Template Struk')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Template Struk</h2>
            <p class="text-sm text-gray-500">Kelola template cetak struk pembayaran (A4 / Thermal).</p>
        </div>
        <a href="{{ route('receipt-templates.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Template
        </a>
    </div>

    {{-- Daftar placeholder --}}
    <details class="bg-blue-50 border border-blue-200 rounded-2xl">
        <summary class="px-5 py-3.5 text-sm font-semibold text-blue-800 cursor-pointer select-none">
            📝 Daftar Placeholder yang Tersedia (klik untuk buka)
        </summary>
        <div class="px-5 pb-4 pt-2 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2">
            @foreach([
                '{{ school_name }}' => 'Nama Lembaga',
                '{{ student_name }}' => 'Nama Siswa',
                '{{ student_nis }}' => 'NIS Siswa',
                '{{ student_class }}' => 'Kelas',
                '{{ payment_type }}' => 'Jenis Pembayaran',
                '{{ amount }}' => 'Nominal Dibayar',
                '{{ total_amount }}' => 'Total Tagihan',
                '{{ amount_paid }}' => 'Total Terbayar',
                '{{ remaining }}' => 'Sisa Tagihan',
                '{{ payment_date }}' => 'Tanggal Bayar',
                '{{ nomor_transaksi }}' => 'No. Transaksi',
                '{{ metode_bayar }}' => 'Metode Bayar',
                '{{ periode }}' => 'Periode Tagihan',
                '{{ petugas }}' => 'Nama Petugas',
                '{{ keterangan }}' => 'Keterangan',
                '{{ print_date }}' => 'Tgl Cetak',
            ] as $ph => $desc)
            <div class="bg-white rounded-lg p-2.5 border border-blue-100">
                <code class="text-xs font-mono text-blue-700">{{ $ph }}</code>
                <p class="text-xs text-gray-500 mt-0.5">{{ $desc }}</p>
            </div>
            @endforeach
        </div>
    </details>

    {{-- Grid template --}}
    @if($templates->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($templates as $t)
        <div class="bg-white rounded-2xl border {{ $t->is_default ? 'border-blue-400 ring-2 ring-blue-100' : 'border-gray-100' }} shadow-sm overflow-hidden group hover:shadow-md transition-shadow">
            <div class="p-5">
                <div class="flex items-start justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-xl {{ $t->is_default ? 'bg-blue-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 {{ $t->is_default ? 'text-blue-600' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ $t->nama_template }}</p>
                            <p class="text-xs text-gray-400">{{ $t->ukuran_label }}</p>
                        </div>
                    </div>
                    @if($t->is_default)
                        <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full flex-shrink-0">Default</span>
                    @endif
                </div>

                <div class="flex flex-wrap gap-1.5 text-xs mb-4">
                    @if($t->show_logo)
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">🖼 Logo</span>
                    @endif
                    @if($t->show_qr)
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">📱 QR</span>
                    @endif
                    @if($t->header)
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">📄 Header Custom</span>
                    @endif
                    @if($t->footer)
                        <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">📄 Footer Custom</span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('receipt-templates.preview', $t) }}" target="_blank"
                       class="flex-1 text-center text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 py-2 rounded-xl transition-colors">
                        Preview
                    </a>
                    <a href="{{ route('receipt-templates.edit', $t) }}"
                       class="flex-1 text-center text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 py-2 rounded-xl transition-colors">
                        Ubah
                    </a>
                    <form method="POST" action="{{ route('receipt-templates.destroy', $t) }}"
                          data-confirm="Hapus template &quot;{{ $t->nama_template }}&quot;?">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm text-center py-16">
        <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <p class="font-semibold text-gray-700">Belum ada template struk</p>
        <p class="text-sm text-gray-400 mt-1">Buat template untuk cetak struk pembayaran.</p>
        <a href="{{ route('receipt-templates.create') }}" class="btn-primary mt-4 inline-flex">Buat Template</a>
    </div>
    @endif
</div>
@endsection
