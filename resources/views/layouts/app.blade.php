<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'SIP-SPP') }}</title>
    <meta name="description" content="@yield('description', 'Sistem Informasi Pembayaran SPP')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-gray-50" x-data="sidebarApp()" x-cloak>

<div class="flex h-screen overflow-hidden">

    {{-- ======= SIDEBAR OVERLAY (mobile) ======= --}}
    <div x-show="open"
         x-transition:enter="transition-opacity duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="open = false"
         class="fixed inset-0 bg-gray-900/60 z-20 lg:hidden"></div>

    {{-- ======= SIDEBAR ======= --}}
    <aside :class="open ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col bg-gradient-to-b from-blue-950 via-blue-900 to-indigo-950 shadow-2xl
                  transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-auto">

        {{-- Logo / Nama Lembaga --}}
        <div class="flex items-center gap-3 h-16 px-5 border-b border-white/10 flex-shrink-0">
            @if(isset($activeTenant) && $activeTenant?->logo)
                <img src="{{ $activeTenant->logo_url }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover shadow">
            @else
                <div class="flex items-center justify-center w-9 h-9 rounded-xl shadow-lg flex-shrink-0"
                     style="background-color: {{ config('tenant.warna_tema', '#2563eb') }}">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-white font-bold text-sm leading-tight truncate">
                    {{ $activeTenant?->name ?? config('app.name', 'SIP-SPP') }}
                </p>
                <p class="text-blue-300 text-xs leading-tight">
                    {{ $activeTenant?->jenjang ?? 'Pembayaran SPP' }}
                </p>
            </div>
            <button @click="open = false" class="lg:hidden text-blue-300 hover:text-white ml-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ======= NAVIGASI ======= --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

            {{-- Beranda --}}
            <a href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Beranda</span>
            </a>

            {{-- ======================================================
                 BLOK SUPER ADMIN
                 Punya: Pilih Lembaga + Manajemen Lembaga.
                 Data Master hanya muncul jika sudah ada lembaga aktif.
            ====================================================== --}}
            @hasrole('Super Admin')

            {{-- Banner lembaga aktif Super Admin --}}
            <div class="mt-3 mb-1 mx-0.5">
                @if($activeTenant)
                    <div class="bg-white/10 rounded-xl px-3 py-2">
                        <p class="text-blue-300 text-xs font-semibold uppercase tracking-wider mb-1">Lembaga Aktif</p>
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-white text-xs font-bold truncate leading-tight">{{ $activeTenant->nama_lengkap }}</p>
                            <a href="{{ route('tenant.switch.index') }}" title="Ganti lembaga"
                               class="text-blue-300 hover:text-white flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @else
                    <a href="{{ route('tenant.switch.index') }}"
                       class="flex items-center gap-2 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-400/30 rounded-xl px-3 py-2.5 transition-colors">
                        <svg class="w-4 h-4 text-amber-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1 min-w-0">
                            <p class="text-amber-200 text-xs font-semibold leading-tight">Belum pilih lembaga</p>
                            <p class="text-amber-300/70 text-xs leading-tight">Klik untuk memilih</p>
                        </div>
                    </a>
                @endif
            </div>

            {{-- Administrasi --}}
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Administrasi</p>
            </div>
            <a href="{{ route('tenant.switch.index') }}"
               class="nav-link {{ request()->routeIs('tenant.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span>Pilih Lembaga</span>
            </a>
            <a href="{{ route('institutions.index') }}"
               class="nav-link {{ request()->routeIs('institutions.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                </svg>
                <span>Manajemen Lembaga</span>
            </a>

            {{-- Data Master Super Admin — hanya jika sudah pilih lembaga --}}
            @if($activeTenant)
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Data Master</p>
            </div>
            <a href="{{ route('school-years.index') }}"
               class="nav-link {{ request()->routeIs('school-years.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Tahun Ajaran</span>
            </a>
            <a href="{{ route('classes.index') }}"
               class="nav-link {{ request()->routeIs('classes.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Data Kelas</span>
            </a>
            <a href="{{ route('students.index') }}"
               class="nav-link {{ request()->routeIs('students.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Data Siswa</span>
            </a>
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Keuangan</p>
            </div>
            <a href="{{ route('payment-types.index') }}"
               class="nav-link {{ request()->routeIs('payment-types.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Jenis Pembayaran</span>
            </a>
            <a href="{{ route('bills.index') }}" class="nav-link {{ request()->routeIs('bills.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Tagihan SPP</span>
            </a>
            <a href="#" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Pembayaran</span>
            </a>
            <a href="#" class="nav-link {{ request()->routeIs('kas.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Kas</span>
            </a>
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Laporan</p>
            </div>
            <a href="#" class="nav-link {{ request()->routeIs('laporan.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Laporan & Rekap</span>
            </a>
            @endif {{-- end $activeTenant --}}

            @endhasrole
            {{-- ====== END SUPER ADMIN BLOCK ====== --}}

            {{-- ======================================================
                 BLOK NON-SUPER ADMIN (Admin Sekolah, Bendahara, Siswa)
            ====================================================== --}}
            @unlessrole('Super Admin')

            @hasanyrole('Admin Sekolah|Bendahara')
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Data Master</p>
            </div>
            @hasrole('Admin Sekolah')
            <a href="{{ route('school-years.index') }}"
               class="nav-link {{ request()->routeIs('school-years.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Tahun Ajaran</span>
            </a>
            <a href="{{ route('classes.index') }}"
               class="nav-link {{ request()->routeIs('classes.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>Data Kelas</span>
            </a>
            <a href="{{ route('students.index') }}"
               class="nav-link {{ request()->routeIs('students.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Data Siswa</span>
            </a>
            @endrole
            @endhasanyrole

            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Keuangan</p>
            </div>
            <a href="#" class="nav-link {{ request()->routeIs('tagihan.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span>Tagihan SPP</span>
            </a>
            <a href="#" class="nav-link {{ request()->routeIs('pembayaran.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span>Pembayaran</span>
            </a>
            @hasanyrole('Admin Sekolah|Bendahara')
            <a href="#" class="nav-link {{ request()->routeIs('kas.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                <span>Kas</span>
            </a>
            <div class="pt-3 pb-1 px-2">
                <p class="text-xs font-semibold text-blue-400/80 uppercase tracking-wider">Laporan</p>
            </div>
            <a href="#" class="nav-link {{ request()->routeIs('laporan.*') ? 'nav-active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Laporan & Rekap</span>
            </a>
            @endhasanyrole

            @endunlessrole
            {{-- ====== END NON-SUPER ADMIN BLOCK ====== --}}

        </nav>

        {{-- User info di bawah sidebar --}}
        <div class="border-t border-white/10 p-3 flex-shrink-0" x-data="{ dropup: false }" @click.outside="dropup = false">
            <div class="relative">
                <button @click="dropup = !dropup"
                        class="flex items-center gap-3 w-full p-2 rounded-xl hover:bg-white/10 transition-colors text-left">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-white font-semibold text-sm shadow"
                         style="background-color: {{ config('tenant.warna_tema', '#2563eb') }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate leading-tight">{{ auth()->user()->name }}</p>
                        <p class="text-blue-300 text-xs truncate leading-tight">
                            {{ auth()->user()->getRoleNames()->first() ?? 'Pengguna' }}
                        </p>
                    </div>
                    <svg class="w-4 h-4 text-blue-300 flex-shrink-0 transition-transform" :class="dropup ? 'rotate-180' : ''"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropup menu --}}
                <div x-show="dropup" x-transition
                     class="absolute bottom-full left-0 right-0 mb-1 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar dari Sistem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    {{-- ======= MAIN CONTENT ======= --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Top Header --}}
        <header class="h-16 bg-white border-b border-gray-200 flex items-center gap-3 px-4 lg:px-6 shadow-sm flex-shrink-0">
            {{-- Hamburger --}}
            <button @click="open = !open"
                    class="lg:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Judul halaman --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-sm font-semibold text-gray-800 truncate">@yield('page-title', 'Beranda')</h1>
                @hasSection('breadcrumb')
                    <p class="text-xs text-gray-400 truncate">@yield('breadcrumb')</p>
                @endif
            </div>

            {{-- Lembaga aktif + tanggal di header --}}
            <div class="hidden md:flex items-center gap-3">
                @if(isset($activeTenant) && $activeTenant)
                    <div class="flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-xl px-3 py-1.5">
                        <div class="w-2 h-2 rounded-full bg-green-500 flex-shrink-0"></div>
                        <span class="text-xs font-medium text-blue-700 truncate max-w-[180px]">
                            {{ $activeTenant->nama_lengkap }}
                        </span>
                        @hasrole('Super Admin')
                        <a href="{{ route('tenant.switch.index') }}"
                           class="text-blue-400 hover:text-blue-700 ml-1" title="Ganti lembaga">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                      d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </a>
                        @endhasrole
                    </div>
                @elseif(auth()->user()?->hasRole('Super Admin'))
                    <a href="{{ route('tenant.switch.index') }}"
                       class="flex items-center gap-2 bg-amber-50 border border-amber-200 rounded-xl px-3 py-1.5 hover:bg-amber-100 transition-colors">
                        <div class="w-2 h-2 rounded-full bg-amber-400 flex-shrink-0"></div>
                        <span class="text-xs font-medium text-amber-700">Pilih Lembaga</span>
                    </a>
                @endif

                {{-- Tanggal --}}
                <div class="flex items-center gap-1.5 text-xs text-gray-500 bg-gray-50 border border-gray-200 rounded-xl px-3 py-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="mx-4 lg:mx-6 mt-4" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center gap-3 p-3.5 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-green-400 hover:text-green-600 ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-4 lg:mx-6 mt-4" x-data="{ show: true }" x-show="show" x-transition>
                <div class="flex items-center gap-3 p-3.5 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-400 hover:text-red-600 ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Konten Utama --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6">
            @yield('content')
        </main>
    </div>
</div>

<script>
    function sidebarApp() {
        return {
            open: window.innerWidth >= 1024,
            init() {
                window.addEventListener('resize', () => {
                    if (window.innerWidth >= 1024) this.open = true;
                });
            }
        }
    }
</script>

@stack('scripts')
</body>
</html>
