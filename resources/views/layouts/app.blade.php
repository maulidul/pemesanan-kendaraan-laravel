<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🚗</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50">
    <div class="min-h-screen flex">
        <!-- ============================================
             SIDEBAR
             ============================================ -->
        @auth
        <aside class="w-64 bg-gradient-cool text-white flex flex-col fixed h-full shadow-2xl z-20">
            <!-- Brand -->
            <div class="px-5 py-5 border-b border-white/10">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="h-10 w-10 rounded-xl bg-white/15 backdrop-blur flex items-center justify-center shadow-inner ring-1 ring-white/20 group-hover:scale-105 transition">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 4zM19 17a2 2 0 11-4 0 2 2 0 014 4z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold tracking-tight">SIPETAK</h1>
                        <p class="text-[10px] text-blue-200 uppercase tracking-wider">Kendaraan Dinas</p>
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                <p class="px-3 mb-2 text-[10px] font-bold text-blue-200/70 uppercase tracking-widest">Menu Utama</p>

                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-9 9 9-9 9-2-9zm9-9l9 9-9 9-2-9"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('bookings.index') }}"
                   class="nav-link {{ request()->routeIs('bookings.index*', 'bookings.create*', 'bookings.edit*') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <span>Pemesanan</span>
                </a>

                @if(auth()->user()->hasRole(['admin', 'operasional']))
                <a href="{{ route('bookings.approval') }}"
                   class="nav-link {{ request()->routeIs('bookings.approval') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="flex-1">Approval</span>
                    @php $pendingCount = \App\Models\Booking::where('status_persetujuan', 'pending')->count(); @endphp
                    @if($pendingCount > 0)
                        <span class="bg-yellow-400 text-yellow-900 text-[10px] font-bold px-1.5 py-0.5 rounded-full dot-pulse">{{ $pendingCount }}</span>
                    @endif
                </a>
                @endif

                <a href="{{ route('vehicles.index') }}"
                   class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>
                    <span>Kendaraan</span>
                </a>

                <a href="{{ route('drivers.index') }}"
                   class="nav-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v1v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg>
                    <span>Driver</span>
                </a>

                <div class="pt-3 mt-3">
                    <p class="px-3 mb-2 text-[10px] font-bold text-blue-200/70 uppercase tracking-widest">Analisis Data</p>

                    <a href="{{ route('kmeans.index') }}"
                       class="nav-link {{ request()->routeIs('kmeans.*') ? 'active' : '' }}">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>K-Means</span>
                    </a>
                </div>
            </nav>

            <!-- User profile footer -->
            <div class="p-4 border-t border-white/10">
                <div class="flex items-center gap-3 group">
                    <div class="avatar-md bg-gradient-to-br from-blue-400 to-purple-500 shadow-lg ring-2 ring-white/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-blue-200 truncate">{{ auth()->user()->role_label }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-blue-200 hover:text-white hover:bg-white/10 p-2 rounded-lg transition" title="Keluar">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>
        @endauth

        <!-- ============================================
             MAIN CONTENT
             ============================================ -->
        <main class="@auth flex-1 ml-64 @endauth min-h-screen">
            @auth
            <!-- Top bar -->
            <header class="glass sticky top-0 z-10 border-b border-slate-200/60 px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="animate-fade-in">
                        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                            <span>{{ request()->routeIs('dashboard') ? 'Beranda' : ucwords(str_replace('.', ' / ', request()->route()->getName())) }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">{{ $header ?? 'Dashboard' }}</h2>
                        @isset($subheader)
                        <p class="text-sm text-slate-500 mt-0.5">{{ $subheader }}</p>
                        @endisset
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="hidden md:flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 rounded-lg">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span class="text-xs font-medium text-slate-700">{{ now()->translatedFormat('l, d F Y') }}</span>
                        </div>
                    </div>
                </div>
            </header>
            @endauth

            <!-- Flash messages -->
            @if(session('success'))
            <div class="mx-6 mt-4 alert-success animate-slide-up">
                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-4 alert-danger animate-slide-up">
                <svg class="h-5 w-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            <!-- Page content -->
            <div class="p-6 lg:p-8 animate-fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>