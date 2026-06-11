@extends('layouts.app')

@section('title', 'Beranda')
@section('page-title', 'Beranda')
@section('breadcrumb', config('app.name') . ' / Beranda')

@section('content')
<div class="space-y-5">

    {{-- ===== BANNER SELAMAT DATANG ===== --}}
    <div class="relative overflow-hidden rounded-2xl shadow-xl"
         style="background: linear-gradient(135deg, {{ config('tenant.warna_tema', '#1d4ed8') }} 0%, #4f46e5 100%)">
        {{-- Dekorasi --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/5 rounded-full"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-white/5 rounded-full"></div>

        <div class="relative p-6 flex items-center justify-between">
            <div>
                <p class="text-white/70 text-sm font-medium">
                    @php
                        $jam = now('Asia/Jakarta')->hour;
                        $salam = $jam < 11 ? 'Selamat Pagi' : ($jam < 15 ? 'Selamat Siang' : ($jam < 18 ? 'Selamat Sore' : 'Selamat Malam'));
                    @endphp
                    {{ $salam }},
                </p>
                <h2 class="text-white text-xl font-bold mt-0.5">{{ $user->name }}</h2>
                <div class="flex flex-wrap items-center gap-2 mt-2.5">
                    <span class="inline-flex items-center gap-1.5 bg-white/20 border border-white/30 rounded-full px-3 py-1 text-xs font-semibold text-white">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                        {{ $roleName }}
                    </span>
                    @if($institution)
                        <span class="inline-flex items-center gap-1.5 text-white/80 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                            {{ $institution->nama_lengkap }}
                        </span>
                    @endif
                </div>
                <p class="text-white/60 text-xs mt-3">
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }} &bull;
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }} WIB
                </p>
            </div>
            <div class="hidden sm:flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ===== KARTU STATISTIK ===== --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

        {{-- Super Admin: Total Lembaga --}}
        @hasrole('Super Admin')
        <a href="{{ route('institutions.index') }}"
           class="group bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md hover:border-purple-200 transition-all">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center group-hover:bg-purple-100 transition-colors">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ $totalLembaga ?? '—' }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Lembaga</p>
        </a>
        @endhasrole

        {{-- Total Siswa --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($statistik['total_siswa']) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Siswa</p>
        </div>

        {{-- Total Tagihan --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($statistik['total_tagihan']) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Tagihan</p>
        </div>

        {{-- Total Pembayaran --}}
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Lunas</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($statistik['total_pembayaran']) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Pembayaran</p>
        </div>

        {{-- Saldo Kas --}}
        @hasanyrole('Super Admin|Admin Sekolah|Bendahara')
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-teal-600 bg-teal-50 px-2 py-0.5 rounded-full">Saldo</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp {{ number_format($statistik['saldo_kas']) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Saldo Kas</p>
        </div>
        @endhasanyrole

        {{-- Total Tunggakan --}}
        @hasanyrole('Super Admin|Admin Sekolah|Bendahara')
        <div class="bg-white rounded-2xl p-5 border border-red-50 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Belum Bayar</span>
            </div>
            <p class="text-2xl font-bold text-red-700">Rp {{ number_format($statistik['total_tunggakan']) }}</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Tunggakan</p>
        </div>
        @endhasanyrole

    </div>

    {{-- ===== PANEL BAWAH ===== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Info Lembaga --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Informasi Lembaga</h3>
                @hasrole('Super Admin')
                    <a href="{{ route('institutions.index') }}"
                       class="text-xs text-blue-600 hover:text-blue-800 font-medium">Kelola →</a>
                @endhasrole
            </div>
            @if($institution)
                <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-50">
                    @if($institution->logo)
                        <img src="{{ $institution->logo_url }}" alt="Logo" class="w-12 h-12 rounded-xl object-cover shadow">
                    @else
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow"
                             style="background-color: {{ $institution->warna_tema ?? '#2563eb' }}">
                            {{ strtoupper(substr($institution->name, 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ $institution->nama_lengkap }}</p>
                        <p class="text-xs text-gray-500">Kode: {{ $institution->code }}</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    @if($institution->email)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            {{ $institution->email }}
                        </div>
                    @endif
                    @if($institution->phone)
                        <div class="flex items-center gap-2 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $institution->phone }}
                        </div>
                    @endif
                    @if($institution->address)
                        <div class="flex items-start gap-2 text-gray-600">
                            <svg class="w-4 h-4 text-gray-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $institution->address }}
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                    </svg>
                    <p class="text-sm">Tidak ada lembaga aktif</p>
                    @hasrole('Super Admin')
                        <a href="{{ route('institutions.create') }}"
                           class="mt-2 inline-block text-sm text-blue-600 hover:underline">Tambah Lembaga</a>
                    @endhasrole
                </div>
            @endif
        </div>

        {{-- Akun Saya --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-800">Akun Saya</h3>
                <a href="{{ route('profile.edit') }}" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Ubah →</a>
            </div>
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-50">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-lg shadow"
                     style="background-color: {{ config('tenant.warna_tema', '#2563eb') }}">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
            </div>
            <div class="space-y-2.5 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Peran</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        {{ $roleName }}
                    </span>
                </div>
                @if($user->nis)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">NIS</span>
                        <span class="text-gray-800 font-medium">{{ $user->nis }}</span>
                    </div>
                @endif
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Status</span>
                    <span class="inline-flex items-center gap-1 text-xs font-medium {{ $user->is_active ? 'text-green-700' : 'text-red-700' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-500">Bergabung</span>
                    <span class="text-gray-800">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
