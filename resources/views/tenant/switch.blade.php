@extends('layouts.app')

@section('title', 'Pilih Lembaga Aktif')
@section('page-title', 'Pilih Lembaga Aktif')
@section('breadcrumb', 'Super Admin / Pilih Lembaga')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">

    {{-- Header info --}}
    <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-2xl p-6 text-white">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-lg">Pilih Lembaga Aktif</h2>
                <p class="text-purple-200 text-sm">Pilih lembaga untuk mengelola data siswa, kelas, dan keuangan.</p>
            </div>
        </div>
        @if($activeTenantId)
            <div class="mt-4 flex items-center justify-between bg-white/10 rounded-xl px-4 py-2.5">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full"></div>
                    <span class="text-sm font-medium">
                        Lembaga aktif sekarang: <strong>{{ $lembaga->firstWhere('id', $activeTenantId)?->nama_lengkap ?? 'Tidak diketahui' }}</strong>
                    </span>
                </div>
                <form method="POST" action="{{ route('tenant.switch.clear') }}">
                    @csrf
                    <button type="submit" class="text-xs text-purple-200 hover:text-white underline">
                        Hapus pilihan
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- Daftar Lembaga --}}
    <div class="space-y-3">
        @forelse($lembaga as $item)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden
                    {{ $activeTenantId == $item->id ? 'ring-2 ring-blue-500' : '' }}">
            <div class="flex items-center gap-4 p-4">
                {{-- Logo --}}
                @if($item->logo)
                    <img src="{{ $item->logo_url }}" alt="Logo" class="w-14 h-14 rounded-xl object-cover flex-shrink-0 shadow-sm">
                @else
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-xl flex-shrink-0 shadow-sm"
                         style="background-color: {{ $item->warna_tema ?? '#2563eb' }}">
                        {{ strtoupper(substr($item->name, 0, 1)) }}
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-gray-800 text-sm">{{ $item->nama_lengkap }}</p>
                        @if($activeTenantId == $item->id)
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 bg-blue-500 rounded-full"></span>Aktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Kode: <span class="font-mono">{{ $item->code }}</span>
                        @if($item->email) · {{ $item->email }} @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $item->users_count ?? $item->users()->count() }} pengguna
                    </p>
                </div>

                {{-- Tombol --}}
                <div class="flex flex-col gap-2 flex-shrink-0">
                    <form method="POST" action="{{ route('tenant.switch', $item->id) }}">
                        @csrf
                        <input type="hidden" name="redirect" value="{{ route('dashboard') }}">
                        <button type="submit"
                                class="w-full text-sm font-medium px-4 py-2 rounded-xl transition-colors
                                       {{ $activeTenantId == $item->id
                                          ? 'bg-blue-600 text-white hover:bg-blue-700'
                                          : 'bg-gray-100 text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            {{ $activeTenantId == $item->id ? '✓ Sedang Aktif' : 'Pilih' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">Belum ada lembaga aktif</p>
            <a href="{{ route('institutions.create') }}"
               class="mt-3 inline-block text-sm text-blue-600 hover:underline">Tambah lembaga baru</a>
        </div>
        @endforelse
    </div>

    <div class="flex justify-between">
        <a href="{{ route('dashboard') }}" class="btn-secondary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Beranda
        </a>
        <a href="{{ route('institutions.create') }}" class="btn-primary text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Lembaga
        </a>
    </div>
</div>
@endsection
