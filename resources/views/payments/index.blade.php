@extends('layouts.app')
@section('title', 'Riwayat Pembayaran')
@section('page-title', 'Riwayat Pembayaran')
@section('breadcrumb', 'Beranda / Keuangan / Pembayaran')

@section('content')
<div class="space-y-5">

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-blue-200 text-xs uppercase tracking-wider font-medium mb-1">Hari Ini</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
            <p class="text-blue-200 text-xs mt-1">{{ today()->translatedFormat('d F Y') }}</p>
        </div>
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-indigo-200 text-xs uppercase tracking-wider font-medium mb-1">Bulan Ini</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
            <p class="text-indigo-200 text-xs mt-1">{{ now()->translatedFormat('F Y') }}</p>
        </div>
        <div class="bg-gradient-to-br from-violet-600 to-violet-700 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-violet-200 text-xs uppercase tracking-wider font-medium mb-1">Total Keseluruhan</p>
            <p class="text-2xl font-bold">Rp {{ number_format($totalKeseluruhan, 0, ',', '.') }}</p>
            <p class="text-violet-200 text-xs mt-1">Semua waktu</p>
        </div>
    </div>

    {{-- FILTER TOOLBAR --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" action="{{ route('payments.index') }}" class="space-y-3">
            <div class="flex flex-wrap items-center gap-3">
                {{-- Search --}}
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama siswa, NIS, atau no. transaksi..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <a href="{{ route('payments.create') }}" class="btn-primary text-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Input Pembayaran
                </a>
            </div>

            <div class="flex flex-wrap gap-2">
                {{-- Metode --}}
                <select name="metode_bayar"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Metode</option>
                    @foreach(\App\Models\Payment::$metodeBayarLabels as $val => $label)
                        <option value="{{ $val }}" {{ request('metode_bayar') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                {{-- Kelas --}}
                <select name="class_id"
                        class="text-sm border border-gray-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('class_id') == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                {{-- Tanggal Dari --}}
                <input type="text" name="tanggal_dari" value="{{ request('tanggal_dari') }}"
                       x-mask="99/99/9999" placeholder="Dari: dd/mm/yyyy"
                       class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="text" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}"
                       x-mask="99/99/9999" placeholder="Sampai: dd/mm/yyyy"
                       class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-40 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button type="submit" class="btn-secondary text-sm">Terapkan</button>
                @if(request()->hasAny(['search','metode_bayar','class_id','tanggal_dari','tanggal_sampai']))
                    <a href="{{ route('payments.index') }}" class="text-sm text-gray-500 hover:text-gray-800 px-3 py-2">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- TABEL --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 text-left">No. Transaksi</th>
                        <th class="px-5 py-3.5 text-left">Siswa</th>
                        <th class="px-5 py-3.5 text-left hidden md:table-cell">Jenis Pembayaran</th>
                        <th class="px-5 py-3.5 text-left hidden lg:table-cell">Metode</th>
                        <th class="px-5 py-3.5 text-left hidden sm:table-cell">Tanggal</th>
                        <th class="px-5 py-3.5 text-right">Nominal</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $pay)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="font-mono text-xs font-semibold text-blue-700">{{ $pay->nomor_transaksi }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $pay->created_at->diffForHumans() }}</p>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $pay->student?->foto_url }}" class="w-7 h-7 rounded-lg object-cover flex-shrink-0" alt="">
                                <div>
                                    <p class="font-semibold text-gray-800 text-xs">{{ $pay->student?->nama_lengkap }}</p>
                                    @if($pay->student?->class)
                                        <p class="text-xs text-blue-600">{{ $pay->student->class->nama_kelas }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 hidden md:table-cell">
                            <p class="text-xs font-medium text-gray-700">{{ $pay->bill?->paymentType?->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-400">{{ $pay->bill?->periode ?? '-' }}</p>
                        </td>
                        <td class="px-5 py-3.5 hidden lg:table-cell">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                                {{ $pay->metode_bayar_label }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 hidden sm:table-cell text-xs text-gray-600">
                            {{ $pay->tanggal_bayar_format }}
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <p class="font-bold text-green-600">{{ $pay->nominal_bayar_format }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('payments.show', $pay) }}"
                                   class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm0 0c0 5-5 9-9 9s-9-4-9-9M3 12a9 9 0 1118 0"/>
                                    </svg>
                                </a>
                                <a href="{{ route('payments.struk', $pay) }}" target="_blank"
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Unduh Struk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('payments.destroy', $pay) }}"
                                      data-confirm="Batalkan transaksi {{ $pay->nomor_transaksi }}? Status tagihan akan di-rollback.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Batalkan">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <p class="font-semibold text-gray-700">Belum ada transaksi pembayaran</p>
                                <a href="{{ route('payments.create') }}" class="btn-primary text-sm mt-1">Input Pembayaran</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($payments->total() > 0)
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700">{{ $payments->firstItem() }}–{{ $payments->lastItem() }}</span>
                dari <span class="font-semibold text-gray-700">{{ number_format($payments->total()) }}</span> transaksi
            </p>
            <div class="flex items-center gap-1">
                @if($payments->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-xl">‹</span>
                @else
                    <a href="{{ $payments->previousPageUrl() }}"
                       class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition-colors">‹</a>
                @endif
                @foreach($payments->getUrlRange(max(1, $payments->currentPage()-2), min($payments->lastPage(), $payments->currentPage()+2)) as $page => $url)
                    @if($page == $payments->currentPage())
                        <span class="px-3 py-1.5 text-sm font-semibold bg-blue-600 text-white border border-blue-600 rounded-xl">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach
                @if($payments->hasMorePages())
                    <a href="{{ $payments->nextPageUrl() }}"
                       class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:text-blue-700 transition-colors">›</a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-xl">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
