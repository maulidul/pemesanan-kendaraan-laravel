<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} — Login</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased min-h-screen flex items-stretch">

    {{-- ============================================
         LEFT PANEL — Branding & visual
         ============================================ --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-gradient-primary text-white flex-col justify-between p-12">
        {{-- Decorative mesh --}}
        <div class="absolute inset-0 bg-mesh opacity-60"></div>

        {{-- Floating shapes --}}
        <div class="absolute top-10 right-10 h-72 w-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 h-64 w-64 bg-purple-300/20 rounded-full blur-3xl"></div>

        {{-- Logo --}}
        <div class="relative z-10 flex items-center gap-3 animate-fade-in">
            <div class="h-12 w-12 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center shadow-inner ring-1 ring-white/30">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 4zM19 17a2 2 0 11-4 0 2 2 0 014 4z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">SIPETAK</h1>
                <p class="text-xs text-blue-100 uppercase tracking-widest">Kendaraan Dinas</p>
            </div>
        </div>

        {{-- Hero text --}}
        <div class="relative z-10 animate-slide-up">
            <h2 class="text-4xl xl:text-5xl font-bold leading-tight tracking-tight text-balance">
                Kelola pemesanan kendaraan <span class="text-blue-200">dengan cerdas.</span>
            </h2>
            <p class="mt-6 text-blue-100 text-lg leading-relaxed max-w-md">
                Platform terintegrasi untuk pemesanan, approval, dan analisis pola pemakaian kendaraan dinas menggunakan algoritma <strong class="text-white">K-Means Clustering</strong>.
            </p>

            {{-- Feature pills --}}
            <div class="mt-8 flex flex-wrap gap-2">
                <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-xs font-medium border border-white/20">📊 Dashboard Analitik</span>
                <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-xs font-medium border border-white/20">🚗 Manajemen Kendaraan</span>
                <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-xs font-medium border border-white/20">🧠 Data Mining K-Means</span>
            </div>
        </div>

        {{-- Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs text-blue-200">
            <span>© {{ date('Y') }} SIPETAK. All rights reserved.</span>
            <span>v1.0.0</span>
        </div>
    </div>

    {{-- ============================================
         RIGHT PANEL — Form login
         ============================================ --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 bg-gradient-soft">
        <div class="w-full max-w-md">
            {{-- Mobile logo --}}
            <div class="lg:hidden mb-8 flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-gradient-cool flex items-center justify-center text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 4zM19 17a2 2 0 11-4 0 2 2 0 014 4z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-slate-900">SIPETAK</h1>
                    <p class="text-xs text-slate-500">Kendaraan Dinas</p>
                </div>
            </div>

            {{-- Header --}}
            <div class="mb-8 animate-slide-up">
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Selamat Datang 👋</h2>
                <p class="text-slate-500 mt-2">Masuk untuk melanjutkan ke dashboard</p>
            </div>

            {{-- Flash messages --}}
            @if (session('status'))
            <div class="alert-success mb-6">
                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            {{-- Form --}}
            <form method="POST" action="{{ route('login') }}" class="space-y-5 animate-fade-in">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9"/></svg>
                        </span>
                        <input id="email" type="email" name="email" value="maulidul@gmail.com"
                               class="form-input pl-10 @error('email') form-input-error @enderror"
                                required autofocus autocomplete="username">
                    </div>
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </span>
                        <input id="password" type="password" name="password" value="12345678"
                               class="form-input pl-10 @error('password') form-input-error @enderror"
                                required autocomplete="current-password">
                    </div>
                    @error('password')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label for="remember_me" class="flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" name="remember" class="form-checkbox">
                        <span class="ml-2 text-sm text-slate-600">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" class="btn-primary btn-lg btn-block shadow-lg shadow-blue-500/30">
                    Masuk
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </form>

            {{-- Demo info --}}
            <div class="mt-8 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                <p class="text-xs font-semibold text-blue-700 mb-2 flex items-center gap-1.5">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    Akun Demo
                </p>
                <div class="space-y-1 text-xs text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Email:</span>
                        <code class="px-2 py-0.5 bg-white rounded text-slate-800 font-mono">maulidul@gmail.com</code>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Password:</span>
                        <code class="px-2 py-0.5 bg-white rounded text-slate-800 font-mono">12345678</code>
                    </div>
                </div>
            </div>

            <p class="text-center text-xs text-slate-400 mt-8">© {{ date('Y') }} {{ config('app.name') }}. Hak cipta dilindungi.</p>
        </div>
    </div>
</body>
</html>