@extends('layouts.app')

@section('title', 'Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')
@section('breadcrumb', 'Beranda / Data Master / Tahun Ajaran')

@section('content')
<div class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-gray-800">Daftar Tahun Ajaran</h2>
            <p class="text-sm text-gray-500">Kelola tahun ajaran untuk lembaga ini.</p>
        </div>
        <a href="{{ route('school-years.create') }}" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Tahun Ajaran
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if($tahunAjaran->count())
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Periode</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($tahunAjaran as $item)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-gray-800 text-sm">{{ $item->nama }}</p>
                    </td>
                    <td class="px-5 py-4 hidden md:table-cell">
                        <p class="text-sm text-gray-600">
                            {{ $item->tanggal_mulai->translatedFormat('d F Y') }}
                            — {{ $item->tanggal_selesai->translatedFormat('d F Y') }}
                        </p>
                    </td>
                    <td class="px-5 py-4 text-center">
                        @if($item->aktif)
                            <span class="badge-green">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Aktif
                            </span>
                        @else
                            <span class="badge-gray">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if(!$item->aktif)
                            <form method="POST" action="{{ route('school-years.setAktif', $item) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-3 py-1.5 bg-green-50 hover:bg-green-100 text-green-700 rounded-lg font-medium transition-colors">
                                    Set Aktif
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('school-years.edit', $item) }}"
                               class="p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(!$item->aktif)
                            <form method="POST" action="{{ route('school-years.destroy', $item) }}"
                                  data-confirm="Hapus tahun ajaran &quot;{{ $item->nama }}&quot;? Kelas yang terhubung akan kehilangan referensi tahun ajaran.">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-5 py-3 border-t border-gray-100">
            {{ $tahunAjaran->links() }}
        </div>
        @else
        <div class="text-center py-16">
            <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Belum ada tahun ajaran</p>
            <a href="{{ route('school-years.create') }}" class="mt-3 inline-block text-sm text-blue-600 hover:underline">Tambah sekarang</a>
        </div>
        @endif
    </div>

</div>
@endsection
