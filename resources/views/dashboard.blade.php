@extends('layouts.app')

@section('title', 'Beranda')
@section('page-title', 'Beranda')
@section('breadcrumb', config('app.name') . ' / Beranda')

@section('content')
<div class="space-y-6">

    <!-- Selamat Datang -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 text-white shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-blue-200 text-sm font-medium">Selamat datang kembali,</p>
                <h2 class="text-2xl font-bold mt-0.5">{{ $user->name }}</h2>
                <div class="flex items-center gap-2 mt-2">
                    <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm border border-white/30 rounded-full px-3 py-1 text-xs font-medium">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full"></span>
                        {{ $roleName }}
                    </span>
                    @if($institution)
                        <span class="inline-flex items-center gap-1 text-blue-200 text-xs">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            {{ $institution->name }}
                        </span>
                    @endif
                </div>
            </div>
            <div class="hidden sm:flex items-center justify-center w-16 h-16 bg-white/10 rounded-2xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
        </div>
        <p class="text-blue-200 text-sm mt-4">
            {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('l, d F Y') }} &bull; {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }} WIB
        </p>
    </div>

    <!-- Kartu Statistik -->
    @hasanyrole('Super Admin|Admin Sekolah|Bendahara')
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Total Siswa -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Aktif</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">—</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Siswa</p>
        </div>

        <!-- Tagihan Bulan Ini -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full">Bulan Ini</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">—</p>
            <p class="text-sm text-gray-500 mt-0.5">Tagihan SPP</p>
        </div>

        <!-- Pembayaran Lunas -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Lunas</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp —</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Pembayaran</p>
        </div>

        <!-- Tunggakan -->
        <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-0.5 rounded-full">Belum Bayar</span>
            </div>
            <p class="text-2xl font-bold text-gray-800">Rp —</p>
            <p class="text-sm text-gray-500 mt-0.5">Total Tunggakan</p>
        </div>

    </div>
    @endhasanyrole

    <!-- Info Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <!-- Info Sistem -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Informasi Sistem</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Nama Aplikasi</span>
                    <span class="text-sm font-medium text-gray-800">{{ config('app.name') }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Versi Laravel</span>
                    <span class="text-sm font-medium text-gray-800">{{ app()->version() }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Zona Waktu</span>
                    <span class="text-sm font-medium text-gray-800">{{ config('app.timezone') }}</span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-gray-500">Lingkungan</span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                 {{ app()->environment('production') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ app()->environment() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Akun Saya -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-4">Akun Saya</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Nama Lengkap</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user->name }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Email</span>
                    <span class="text-sm font-medium text-gray-800">{{ $user->email }}</span>
                </div>
                <div class="flex items-center justify-between py-2 border-b border-gray-50">
                    <span class="text-sm text-gray-500">Peran</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        {{ $roleName }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-2">
                    <span class="text-sm text-gray-500">Institusi</span>
                    <span class="text-sm font-medium text-gray-800">{{ $institution?->name ?? 'Super Admin' }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
