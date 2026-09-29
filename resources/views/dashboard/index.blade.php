@extends('layouts.app')

@section('title', 'Dashboard')

@section('header', 'Dashboard')
@section('subheader', 'Ringkasan statistik pemesanan kendaraan dinas')

@section('content')
    {{-- ============================================
         WELCOME BANNER
         ============================================ --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-cool p-6 lg:p-8 text-white shadow-xl mb-8 animate-slide-up">
        <div class="absolute inset-0 bg-mesh opacity-50"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-1">👋 Selamat datang kembali,</p>
                <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">{{ auth()->user()->name }}!</h1>
                <p class="text-blue-100 mt-1 text-sm">Semoga harimu menyenangkan. Berikut ringkasan aktivitas sistem hari ini.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('bookings.create') }}" class="btn bg-white text-blue-700 hover:bg-blue-50 shadow-lg">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Pemesanan Baru
                </a>
            </div>
        </div>
    </div>

    {{-- ============================================
         STATISTIK UTAMA
         ============================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8 stagger">
        {{-- Total Pemesanan --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Pemesanan</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">{{ number_format($totalBookings) }}</p>
                    <p class="text-xs text-emerald-600 mt-1 flex items-center gap-1">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        Semua status
                    </p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
        </div>

        {{-- Kendaraan Tersedia --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kendaraan Tersedia</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $kendaraanTersedia }}<span class="text-base text-slate-400 font-medium">/{{ $totalKendaraan }}</span>
                    </p>
                    <div class="progress-track mt-3">
                        <div class="progress-bar" style="width: {{ $totalKendaraan > 0 ? ($kendaraanTersedia / $totalKendaraan) * 100 : 0 }}%"></div>
                    </div>
                </div>
                <div class="stat-icon bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 4zM19 17a2 2 0 11-4 0 2 2 0 014 4z"/></svg>
                </div>
            </div>
        </div>

        {{-- Driver Aktif --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Driver Aktif</p>
                    <p class="text-3xl font-bold text-slate-900 mt-2">
                        {{ $driverAktif }}<span class="text-base text-slate-400 font-medium">/{{ $totalDriver }}</span>
                    </p>
                    <p class="text-xs text-slate-500 mt-2">Bertugas aktif</p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-indigo-500 to-indigo-600 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v1v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857"/></svg>
                </div>
            </div>
        </div>

        {{-- Pending Approval --}}
        <div class="stat-card">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Menunggu Persetujuan</p>
                    <p class="text-3xl font-bold text-amber-600 mt-2">{{ $bookingsPending }}</p>
                    <p class="text-xs text-amber-600 mt-1 flex items-center gap-1">
                        <span class="dot bg-amber-500 dot-pulse"></span>
                        Butuh aksi operasional
                    </p>
                </div>
                <div class="stat-icon bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-lg">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================
         STATUS BADGES
         ============================================ --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 stagger">
        <div class="stat-card border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">⏳ Pending</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $bookingsPending }}</p>
                </div>
                <span class="badge-yellow text-base px-3 py-1">{{ number_format(($bookingsPending/max($totalBookings,1))*100, 0) }}%</span>
            </div>
        </div>
        <div class="stat-card border-l-4 border-l-emerald-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">✓ Disetujui</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $bookingsDisetujui }}</p>
                </div>
                <span class="badge-green text-base px-3 py-1">{{ number_format(($bookingsDisetujui/max($totalBookings,1))*100, 0) }}%</span>
            </div>
        </div>
        <div class="stat-card border-l-4 border-l-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-slate-500 font-medium">🏁 Selesai</p>
                    <p class="text-2xl font-bold text-slate-900 mt-1">{{ $bookingsSelesai }}</p>
                </div>
                <span class="badge-blue text-base px-3 py-1">{{ number_format(($bookingsSelesai/max($totalBookings,1))*100, 0) }}%</span>
            </div>
        </div>
    </div>

    {{-- ============================================
         CHART & CLUSTER RINGKASAN
         ============================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Chart --}}
        <div class="card card-padded lg:col-span-2 animate-slide-up">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">📈 Statistik Pemesanan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">6 bulan terakhir</p>
                </div>
                <span class="badge-blue">Bulanan</span>
            </div>
            <div class="h-72">
                <canvas id="bookingChart"></canvas>
            </div>
        </div>

        {{-- Cluster --}}
        <div class="card card-padded animate-slide-up">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">🎯 Cluster Kendaraan</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Hasil K-Means terakhir</p>
                </div>
                <a href="{{ route('kmeans.index') }}" class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">
                    Detail →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($latestKmeans as $result)
                <div class="flex items-center justify-between p-3 bg-slate-50 rounded-xl hover:bg-slate-100 transition">
                    <div class="flex items-center gap-3">
                        <span class="dot bg-{{ $result->cluster_color }}-500 dot-pulse"></span>
                        <span class="text-sm font-medium text-slate-800">{{ $result->vehicle->nama_kendaraan ?? '-' }}</span>
                    </div>
                    <span class="badge-{{ $result->cluster_color }} text-[10px]">{{ $result->cluster_name }}</span>
                </div>
                @empty
                <div class="text-center py-8">
                    <p class="text-sm text-slate-500">Belum ada analisis.</p>
                    <a href="{{ route('kmeans.index') }}" class="btn-primary btn-sm mt-3 inline-flex">Jalankan Sekarang</a>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ============================================
         PEMESANAN TERBARU
         ============================================ --}}
    <div class="card animate-slide-up">
        <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-900">🕐 Pemesanan Terbaru</h3>
                <p class="text-xs text-slate-500 mt-0.5">5 transaksi terakhir</p>
            </div>
            <a href="{{ route('bookings.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 hover:underline">Lihat semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pemohon</th>
                        <th>Kendaraan</th>
                        <th>Tujuan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                    <tr>
                        <td>
                            <div class="font-medium text-slate-900">{{ $booking->tanggal_pemesanan->format('d M Y') }}</div>
                            <div class="text-xs text-slate-500">{{ $booking->tanggal_pemesanan->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="avatar-sm bg-gradient-to-br from-blue-400 to-purple-500">
                                    {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="font-medium text-slate-900">{{ $booking->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="text-slate-700">{{ $booking->vehicle->nama_kendaraan ?? '-' }}</td>
                        <td class="max-w-xs">
                            <div class="line-clamp-1">{{ $booking->tujuan }}</div>
                        </td>
                        <td>
                            <span class="badge-{{ $booking->status_color }}">{{ $booking->status_label }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-slate-500">
                            <p class="text-sm">Belum ada data pemesanan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('bookingChart').getContext('2d');

        // Gradient untuk area chart
        const gradient = ctx.createLinearGradient(0, 0, 0, 280);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Jumlah Pemesanan',
                    data: @json($chartData),
                    borderColor: '#3b82f6',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#3b82f6',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgb(15 23 42)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0, color: '#94a3b8' },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        ticks: { color: '#94a3b8' },
                        grid: { display: false }
                    }
                }
            }
        });
    </script>
    @endpush
@endsection