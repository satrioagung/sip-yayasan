<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk — {{ config('app.name', 'SIP-SPP') }}</title>
    <meta name="description" content="Masuk ke Sistem Informasi Pembayaran SPP">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-950 via-blue-900 to-indigo-900 flex items-center justify-center p-4">

    <!-- Background decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay:2s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-5"></div>
    </div>

    <!-- Login Card -->
    <div class="relative w-full max-w-md">
        <!-- Logo & Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl mb-4 shadow-2xl">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white tracking-tight">SIP-SPP</h1>
            <p class="text-blue-200 text-sm mt-1">Sistem Informasi Pembayaran SPP</p>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-white mb-1">Selamat Datang</h2>
            <p class="text-blue-200 text-sm mb-6">Masuk ke akun Anda untuk melanjutkan</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-3 bg-green-500/20 border border-green-400/30 rounded-lg text-green-300 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-blue-100 mb-1.5">
                        Alamat Email
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </span>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autocomplete="username"
                            placeholder="email@contoh.com"
                            class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300/60
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                                   transition-all duration-200 text-sm
                                   @error('email') border-red-400/60 @enderror"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-red-300 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-blue-100 mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative" x-data="{ tampilkan: false }">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-blue-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </span>
                        <input
                            id="password"
                            name="password"
                            :type="tampilkan ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="w-full pl-10 pr-12 py-3 bg-white/10 border border-white/20 rounded-xl text-white placeholder-blue-300/60
                                   focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent
                                   transition-all duration-200 text-sm
                                   @error('password') border-red-400/60 @enderror"
                        >
                        <button type="button" @click="tampilkan = !tampilkan"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-blue-300 hover:text-white transition-colors">
                            <svg x-show="!tampilkan" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg x-show="tampilkan" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-red-300 text-xs">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember_me"
                               class="w-4 h-4 rounded border-white/30 bg-white/10 text-blue-500 focus:ring-blue-400 focus:ring-offset-0">
                        <span class="text-sm text-blue-200">Ingat Saya</span>
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}"
                           class="text-sm text-blue-300 hover:text-white transition-colors underline-offset-2 hover:underline">
                            Lupa Kata Sandi?
                        </a>
                    @endif
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-3 px-4 bg-blue-500 hover:bg-blue-400 text-white font-semibold rounded-xl
                               transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]
                               shadow-lg shadow-blue-500/25 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-0
                               text-sm tracking-wide">
                    Masuk ke Sistem
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-blue-300/60 text-xs mt-6">
            &copy; {{ date('Y') }} {{ config('app.name', 'SIP-SPP') }} &mdash; Hak Cipta Dilindungi
        </p>
    </div>

</body>
</html>
