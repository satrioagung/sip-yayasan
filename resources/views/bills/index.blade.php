@extends('layouts.app')
@section('title', 'Daftar Tagihan')
@section('page-title', 'Tagihan SPP')
@section('breadcrumb', 'Beranda / Keuangan / Tagihan')

@section('content')
<div class="space-y-5">

    {{-- ========== STAT CARDS ========== --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $cards = [
                ['label'=>'Total Tagihan',   'value'=>$stats['total'],       'color'=>'blue',   'icon'=>'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                ['label'=>'Belum Bayar',     'value'=>$stats['belum_bayar'], 'color'=>'red',    'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Sebagian',        'value'=>$stats['sebagian'],    'color'=>'amber',  'icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['label'=>'Lunas',           'value'=>$stats['lunas'],       'color'=>'green',  'icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ];
        @endphp
        @foreach($cards as $card)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-{{ $card['color'] }}-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-{{ $card['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-xl font-bold text-gray-800">{{ number_format($card['value']) }}</p>
                <p class="text-xs text-gray-500">{{ $card['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Ringkasan Keuangan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-blue-100 text-xs font-medium uppercase tracking-wider mb-1">Total Tagihan</p>
            <p class="text-2xl font-bold">Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl p-5 text-white shadow-lg">
            <p class="text-green-100 text-xs font-medium uppercase tracking-wider mb-1">Total Terbayar</p>
            <p class="text-2xl font-bold">Rp {{ number_format($stats['total_terbayar'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- ========== FILTER TOOLBAR ========== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" action="{{ route('bills.index') }}" class="space-y-3">
            {{-- Baris 1: Search + Generate --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[200px]">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari nama siswa atau NIS..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <a href="{{ route('bills.generate') }}" class="btn-primary text-sm flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Generate Tagihan
                </a>
            </div>

            {{-- Baris 2: Filter dropdown --}}
            <div class="flex flex-wrap gap-2">
                <div class="relative">
                    <select name="payment_type_id"
                            style="-webkit-appearance:none;appearance:none"
                            class="text-sm border border-gray-200 rounded-xl pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Jenis</option>
                        @foreach($paymentTypes as $pt)
                            <option value="{{ $pt->id }}" {{ request('payment_type_id') == $pt->id ? 'selected' : '' }}>
                                {{ $pt->nama }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div class="relative">
                    <select name="school_year_id"
                            style="-webkit-appearance:none;appearance:none"
                            class="text-sm border border-gray-200 rounded-xl pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($schoolYears as $sy)
                            <option value="{{ $sy->id }}" {{ request('school_year_id') == $sy->id ? 'selected' : '' }}>
                                {{ $sy->nama }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div class="relative">
                    <select name="class_id"
                            style="-webkit-appearance:none;appearance:none"
                            class="text-sm border border-gray-200 rounded-xl pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('class_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div class="relative">
                    <select name="status"
                            style="-webkit-appearance:none;appearance:none"
                            class="text-sm border border-gray-200 rounded-xl pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Status</option>
                        @foreach(\App\Models\Bill::$statusConfig as $val => $cfg)
                            <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>
                                {{ $cfg['label'] }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <div class="relative">
                    <select name="bulan"
                            style="-webkit-appearance:none;appearance:none"
                            class="text-sm border border-gray-200 rounded-xl pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="">Semua Bulan</option>
                        @foreach(\App\Models\Bill::$bulanLabels as $num => $label)
                            <option value="{{ $num }}" {{ request('bulan') == $num ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <svg class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

                <input type="number" name="tahun" value="{{ request('tahun') }}"
                       placeholder="Tahun" min="2000" max="2100"
                       class="text-sm border border-gray-200 rounded-xl px-3 py-2 w-24 focus:outline-none focus:ring-2 focus:ring-blue-500">

                <button type="submit" class="btn-secondary text-sm">Terapkan</button>
                @if(request()->hasAny(['search','payment_type_id','school_year_id','class_id','status','bulan','tahun']))
                    <a href="{{ route('bills.index') }}" class="text-sm text-gray-500 hover:text-gray-800 px-3 py-2">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ========== TABEL TAGIHAN ========== --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 text-left">Siswa</th>
                        <th class="px-5 py-3.5 text-left hidden md:table-cell">Jenis Pembayaran</th>
                        <th class="px-5 py-3.5 text-left hidden lg:table-cell">Periode</th>
                        <th class="px-5 py-3.5 text-right hidden sm:table-cell">Nominal</th>
                        <th class="px-5 py-3.5 text-right hidden lg:table-cell">Terbayar</th>
                        <th class="px-5 py-3.5 text-right hidden lg:table-cell">Sisa</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($bills as $bill)
                    @php $sc = $bill->status_config; @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">

                        {{-- Siswa --}}
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <img src="{{ $bill->student->foto_url }}" alt="{{ $bill->student->nama_lengkap }}"
                                     class="w-8 h-8 rounded-lg object-cover flex-shrink-0 shadow-sm">
                                <div>
                                    <p class="font-semibold text-gray-800 leading-tight">{{ $bill->student->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $bill->student->nis }}</p>
                                    @if($bill->student->class)
                                        <span class="text-xs text-blue-600">{{ $bill->student->class->nama_kelas }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Jenis Pembayaran --}}
                        <td class="px-5 py-3.5 hidden md:table-cell">
                            <p class="font-medium text-gray-700 text-xs">{{ $bill->paymentType->nama }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $bill->paymentType->kode }}</p>
                        </td>

                        {{-- Periode --}}
                        <td class="px-5 py-3.5 hidden lg:table-cell text-sm text-gray-600">
                            {{ $bill->periode }}
                        </td>

                        {{-- Nominal --}}
                        <td class="px-5 py-3.5 text-right font-semibold text-gray-800 hidden sm:table-cell">
                            {{ $bill->nominal_format }}
                        </td>

                        {{-- Terbayar --}}
                        <td class="px-5 py-3.5 text-right text-green-600 font-medium hidden lg:table-cell">
                            {{ $bill->nominal_terbayar > 0 ? $bill->nominal_terbayar_format : '—' }}
                        </td>

                        {{-- Sisa --}}
                        <td class="px-5 py-3.5 text-right hidden lg:table-cell">
                            @if($bill->sisa > 0)
                                <span class="text-red-600 font-medium">{{ $bill->sisa_format }}</span>
                            @else
                                <span class="text-green-600">✓ Lunas</span>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full
                                         {{ $sc['bg'] }} {{ $sc['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                {{ $sc['label'] }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex items-center justify-end gap-1">
                                {{-- Hapus hanya jika belum ada pembayaran --}}
                                @if($bill->nominal_terbayar === 0)
                                <form method="POST" action="{{ route('bills.destroy', $bill) }}"
                                      data-confirm="Hapus tagihan &quot;{{ $bill->paymentType->nama }}&quot; untuk {{ $bill->student->nama_lengkap }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <span class="p-1.5 text-gray-200 cursor-not-allowed" title="Sudah ada pembayaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center">
                                    <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-700">Belum ada tagihan</p>
                                    <p class="text-sm text-gray-400">Generate tagihan terlebih dahulu.</p>
                                </div>
                                <a href="{{ route('bills.generate') }}" class="btn-primary text-sm mt-1">
                                    Generate Tagihan Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($bills->total() > 0)
        <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            <p class="text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700">{{ $bills->firstItem() }}–{{ $bills->lastItem() }}</span>
                dari <span class="font-semibold text-gray-700">{{ number_format($bills->total()) }}</span> tagihan
            </p>
            <div class="flex items-center gap-1">
                @if($bills->onFirstPage())
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-xl cursor-not-allowed">‹</span>
                @else
                    <a href="{{ $bills->previousPageUrl() }}"
                       class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors">‹</a>
                @endif

                @foreach($bills->getUrlRange(max(1, $bills->currentPage()-2), min($bills->lastPage(), $bills->currentPage()+2)) as $page => $url)
                    @if($page == $bills->currentPage())
                        <span class="px-3 py-1.5 text-sm font-semibold bg-blue-600 text-white border border-blue-600 rounded-xl">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach

                @if($bills->hasMorePages())
                    <a href="{{ $bills->nextPageUrl() }}"
                       class="px-3 py-1.5 text-sm text-gray-600 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition-colors">›</a>
                @else
                    <span class="px-3 py-1.5 text-sm text-gray-300 border border-gray-200 rounded-xl cursor-not-allowed">›</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
