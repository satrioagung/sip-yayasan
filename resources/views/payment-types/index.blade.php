@extends('layouts.app')
@section('title', 'Jenis Pembayaran')
@section('page-title', 'Jenis Pembayaran')
@section('breadcrumb', 'Beranda / Keuangan / Jenis Pembayaran')

@section('content')
<div class="space-y-5">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Jenis Pembayaran</h2>
            <p class="text-sm text-gray-500">Kelola jenis pembayaran seperti SPP, Ujian, Seragam, dll.</p>
        </div>
        <a href="{{ route('payment-types.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jenis Pembayaran
        </a>
    </div>

    {{-- Search --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <form method="GET" action="{{ route('payment-types.index') }}" class="flex gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau kode pembayaran..."
                       class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="btn-secondary">Cari</button>
            @if(request('search'))
                <a href="{{ route('payment-types.index') }}" class="btn-secondary">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($types->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3.5 text-left">Jenis Pembayaran</th>
                        <th class="px-5 py-3.5 text-left hidden md:table-cell">Tipe</th>
                        <th class="px-5 py-3.5 text-right hidden sm:table-cell">Nominal Default</th>
                        <th class="px-5 py-3.5 text-center hidden lg:table-cell">Cicil</th>
                        <th class="px-5 py-3.5 text-center hidden lg:table-cell">Wajib</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($types as $type)
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xs font-bold text-blue-700">{{ substr($type->kode, 0, 3) }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $type->nama }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $type->kode }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            @php
                                $tipeColor = ['bulanan'=>'blue','tahunan'=>'purple','sekali'=>'orange','bebas'=>'gray'];
                                $c = $tipeColor[$type->tipe] ?? 'gray';
                            @endphp
                            <span class="inline-flex items-center text-xs font-medium bg-{{ $c }}-50 text-{{ $c }}-700 px-2.5 py-1 rounded-lg">
                                {{ $type->tipe_label }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right font-semibold text-gray-700 hidden sm:table-cell">
                            {{ $type->nominal_format }}
                        </td>
                        <td class="px-5 py-4 text-center hidden lg:table-cell">
                            @if($type->bisa_cicil)
                                <span class="text-green-600">✓</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center hidden lg:table-cell">
                            @if($type->wajib)
                                <span class="text-xs font-medium bg-red-50 text-red-600 px-2 py-0.5 rounded-full">Wajib</span>
                            @else
                                <span class="text-xs text-gray-400">Opsional</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($type->aktif)
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-full px-2.5 py-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('payment-types.edit', $type) }}"
                                   class="p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Ubah">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form method="POST" action="{{ route('payment-types.destroy', $type) }}"
                                      data-confirm="Hapus jenis pembayaran &quot;{{ $type->nama }}&quot;? Tidak dapat dihapus jika sudah ada tagihan terkait.">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($types->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
            <p class="text-sm text-gray-500">{{ $types->firstItem() }}–{{ $types->lastItem() }} dari {{ $types->total() }}</p>
            {{ $types->links() }}
        </div>
        @endif
        @else
        <div class="text-center py-16">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Belum ada jenis pembayaran</p>
            <a href="{{ route('payment-types.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">
                Tambah jenis pembayaran pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
