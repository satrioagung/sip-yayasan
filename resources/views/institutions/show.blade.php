@extends('layouts.app')

@section('title', 'Detail Lembaga — ' . $institution->name)
@section('page-title', 'Detail Lembaga')
@section('breadcrumb', 'Beranda / Lembaga / Detail')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    {{-- Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Banner warna tema --}}
        <div class="h-3" style="background-color: {{ $institution->warna_tema ?? '#2563eb' }}"></div>
        <div class="p-6 flex items-start gap-5">
            {{-- Logo --}}
            @if($institution->logo)
                <img src="{{ $institution->logo_url }}" alt="Logo" class="w-20 h-20 rounded-2xl object-cover shadow-md flex-shrink-0">
            @else
                <div class="w-20 h-20 rounded-2xl flex items-center justify-center text-white font-bold text-2xl shadow-md flex-shrink-0"
                     style="background-color: {{ $institution->warna_tema ?? '#2563eb' }}">
                    {{ strtoupper(substr($institution->name, 0, 1)) }}
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">{{ $institution->name }}</h2>
                        <p class="text-sm text-gray-500 mt-0.5">{{ $institution->jenjang ?? '' }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @can('update', $institution)
                        <a href="{{ route('institutions.edit', $institution) }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium rounded-xl transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Ubah
                        </a>
                        @endcan
                    </div>
                </div>
                {{-- Badge status --}}
                <div class="flex flex-wrap items-center gap-2 mt-3">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                 {{ $institution->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $institution->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $institution->status_label }}
                    </span>
                    <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg">{{ $institution->code }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Detail --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- Kontak --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Informasi Kontak</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="text-sm text-gray-800">{{ $institution->email ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-400">Telepon</p>
                        <p class="text-sm text-gray-800">{{ $institution->phone ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <div>
                        <p class="text-xs text-gray-400">Alamat</p>
                        <p class="text-sm text-gray-800">{{ $institution->address ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kepala Sekolah --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Kepala Sekolah</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Nama</p>
                    <p class="text-sm font-medium text-gray-800">{{ $institution->principal_name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400">NIP</p>
                    <p class="text-sm font-mono text-gray-800">{{ $institution->nip_kepala ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Konfigurasi Struk --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Konfigurasi Struk</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-400">Prefix Nomor Struk</p>
                    <p class="text-sm font-mono font-semibold text-gray-800">{{ $institution->prefix_nomor_struk ?? 'SPP' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Warna Tema</p>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-lg shadow-sm border border-gray-200"
                             style="background-color: {{ $institution->warna_tema ?? '#2563eb' }}"></div>
                        <span class="text-sm font-mono text-gray-700">{{ $institution->warna_tema ?? '#2563eb' }}</span>
                    </div>
                </div>
                @if($institution->footer_struk)
                <div>
                    <p class="text-xs text-gray-400 mb-1">Footer Struk</p>
                    <p class="text-sm text-gray-700 bg-gray-50 rounded-lg p-2.5 italic">{{ $institution->footer_struk }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Statistik Pengguna --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Statistik</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Total Pengguna</span>
                    <span class="text-sm font-bold text-gray-800">{{ $institution->users->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Dibuat</span>
                    <span class="text-sm text-gray-700">{{ $institution->created_at->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">Diperbarui</span>
                    <span class="text-sm text-gray-700">{{ $institution->updated_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi Bawah --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('institutions.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Daftar
        </a>

        <div class="flex items-center gap-2">
            {{-- Toggle Aktif --}}
            @can('update', $institution)
            <form method="POST" action="{{ route('institutions.toggleAktif', $institution) }}">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-xl transition-colors
                               {{ $institution->is_active ? 'text-red-700 bg-red-50 hover:bg-red-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }}">
                    @if($institution->is_active)
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                        </svg>
                        Nonaktifkan
                    @else
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Aktifkan
                    @endif
                </button>
            </form>
            @endcan
        </div>
    </div>

</div>
@endsection
