<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'SIP-SPP') }}</title>
    <meta name="description" content="@yield('description', 'Sistem Informasi Pembayaran SPP')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;1,14..32,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-link.active { @apply bg-blue-600 text-white; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full bg-gray-50" x-data="sidebarApp()" x-cloak>

    <div class="flex h-screen overflow-hidden">

        <!-- ===================== SIDEBAR ===================== -->
        <!-- Overlay (mobile) -->
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-gray-900/60 z-20 lg:hidden"
        ></div>

        <!-- Sidebar Panel -->
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-30 w-64 bg-gradient-to-b from-blue-950 to-blue-900 shadow-2xl
                   transform transition-transform duration-300 ease-in-out
                   lg:translate-x-0 lg:static lg:inset-auto lg:flex lg:flex-col"
        >
            <!-- Logo -->
            <div class="flex items-center gap-3 h-16 px-5 border-b border-blue-800/50">
                <div class="flex items-center justify-center w-9 h-9 bg-blue-500 rounded-xl shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <p class="text-white font-bold text-sm leading-tight">SIP-SPP</p>
                    <p class="text-blue-300 text-xs leading-tight">Pembayaran SPP</p>
                </div>
                <!-- Close button (mobile) -->
                <button @click="sidebarOpen = false" class="ml-auto lg:hidden text-blue-300 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Navigasi -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Beranda</span>
                </a>

                @hasanyrole('Super Admin|Admin Sekolah|Bendahara')
                <!-- Grup: Data Master -->
                <div class="pt-4 pb-1 px-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Data Master</p>
                </div>

                @hasanyrole('Super Admin|Admin Sekolah')
                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('siswa.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Data Siswa</span>
                </a>
                @endhasanyrole

                @hasanyrole('Super Admin|Admin Sekolah')
                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('kelas.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Data Kelas</span>
                </a>
                @endhasanyrole

                <!-- Grup: Keuangan -->
                <div class="pt-4 pb-1 px-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Keuangan</p>
                </div>

                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('tagihan.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span>Tagihan SPP</span>
                </a>

                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('pembayaran.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Pembayaran</span>
                </a>
                @endhasanyrole

                @hasanyrole('Super Admin|Admin Sekolah|Bendahara')
                <!-- Grup: Laporan -->
                <div class="pt-4 pb-1 px-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Laporan</p>
                </div>

                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('laporan.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Laporan & Rekap</span>
                </a>
                @endhasanyrole

                @hasrole('Super Admin')
                <!-- Grup: Administrasi -->
                <div class="pt-4 pb-1 px-3">
                    <p class="text-xs font-semibold text-blue-400 uppercase tracking-wider">Administrasi</p>
                </div>

                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('institusi.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                    </svg>
                    <span>Institusi</span>
                </a>

                <a href="#"
                   class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-150
                          {{ request()->routeIs('pengguna.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-blue-200 hover:bg-blue-800/50 hover:text-white' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Manajemen Pengguna</span>
                </a>
                @endhasrole

            </nav>

            <!-- User Profile (bottom) -->
            <div class="border-t border-blue-800/50 p-3">
                <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-blue-800/30 transition-colors" x-data="{ open: false }" @click.outside="open = false">
                    <div class="w-9 h-9 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0 shadow">
                        <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-white text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                        <p class="text-blue-300 text-xs truncate">{{ auth()->user()->getRoleNames()->first() ?? 'Pengguna' }}</p>
                    </div>
                    <div class="relative">
                        <button @click="open = !open" class="text-blue-300 hover:text-white transition-colors p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <!-- Dropdown -->
                        <div x-show="open" x-transition
                             class="absolute bottom-8 right-0 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
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
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- ===================== MAIN CONTENT ===================== -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Top Navbar -->
            <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-4 lg:px-6 shadow-sm flex-shrink-0">
                <!-- Hamburger (mobile) -->
                <button @click="sidebarOpen = !sidebarOpen"
                        class="lg:hidden p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Page title -->
                <div class="flex-1 lg:flex-none ml-3 lg:ml-0">
                    <h1 class="text-base font-semibold text-gray-800">@yield('page-title', 'Beranda')</h1>
                    @hasSection('breadcrumb')
                        <p class="text-xs text-gray-500">@yield('breadcrumb')</p>
                    @endif
                </div>

                <!-- Right side -->
                <div class="flex items-center gap-2">
                    <!-- Notifikasi -->
                    <button class="p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition-colors relative">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </button>

                    <!-- Tanggal -->
                    <div class="hidden sm:flex items-center gap-1.5 text-xs text-gray-500 bg-gray-50 rounded-lg px-3 py-2 border border-gray-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }}
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm"
                     x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                    <button @click="show = false" class="ml-auto text-green-400 hover:text-green-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm"
                     x-data="{ show: true }" x-show="show" x-transition>
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                    <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif

            <!-- Main Slot -->
            <main class="flex-1 overflow-y-auto p-4 lg:p-6">
                @yield('content')
            </main>

        </div><!-- end main -->
    </div><!-- end flex -->

    <script>
        function sidebarApp() {
            return {
                sidebarOpen: window.innerWidth >= 1024,
                init() {
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) {
                            this.sidebarOpen = true;
                        }
                    });
                }
            }
        }
    </script>

    @stack('scripts')
</body>
</html>
