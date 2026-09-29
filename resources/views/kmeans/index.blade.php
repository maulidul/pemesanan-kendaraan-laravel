@extends('layouts.app')

@section('title', 'Analisis K-Means')

@section('header', 'Analisis K-Means Clustering')
@section('subheader', 'Pengelompokan kendaraan berdasarkan pola pemakaian dengan Euclidean Distance')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ============================================
         FORM PARAMETER
         ============================================ --}}
    <div class="card card-padded lg:sticky lg:top-24 self-start animate-slide-up">
        <div class="flex items-center gap-3 mb-5">
            <div class="h-10 w-10 rounded-xl bg-gradient-cool text-white flex items-center justify-center shadow-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-900">Parameter</h3>
                <p class="text-xs text-slate-500">Konfigurasi algoritma</p>
            </div>
        </div>

        <form method="POST" action="{{ route('kmeans.analyze') }}" class="space-y-5">
            @csrf

            <div class="form-group">
                <label for="k" class="form-label">Jumlah Cluster (K)</label>
                <div class="flex items-center gap-3">
                    <input type="range" name="k_range" id="k_range" min="2" max="5" value="3"
                           class="flex-1 accent-blue-600" oninput="document.getElementById('k').value=this.value; document.getElementById('k_value').textContent=this.value">
                    <input type="number" name="k" id="k" value="3" min="2" max="5" required class="form-input w-20 text-center">
                    <span id="k_value" class="text-sm font-bold text-blue-600 w-4">3</span>
                </div>
                <p class="form-hint">💡 Direkomendasikan 3: Tinggi, Sedang, Rendah</p>
            </div>

            <div class="form-group">
                <label for="max_iterations" class="form-label">Maks. Iterasi</label>
                <input type="number" name="max_iterations" id="max_iterations" value="100" min="1" max="500" required class="form-input">
            </div>

            <div class="form-group">
                <label for="tolerance" class="form-label">Threshold Konvergensi</label>
                <input type="number" step="0.0001" name="tolerance" id="tolerance" value="0.0001" min="0.0001" max="1" required class="form-input font-mono">
                <p class="form-hint">⏱️ Semakin kecil = semakin presisi</p>
            </div>

            <div class="divider"></div>

            <button type="submit" class="btn-primary btn-block btn-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Jalankan Analisis
            </button>

            @if($latestResults->isNotEmpty())
            <button type="button" onclick="document.getElementById('reset-form').submit()" class="btn-secondary btn-block btn-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                Reset Hasil
            </button>
            @endif
        </form>

        @if($latestResults->isNotEmpty())
        <form id="reset-form" method="POST" action="{{ route('kmeans.reset') }}" class="hidden">@csrf</form>
        @endif
    </div>

    {{-- ============================================
         INFO & RINGKASAN
         ============================================ --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Info Card --}}
        <div class="card card-padded animate-slide-up">
            <div class="flex items-start gap-4">
                <div class="h-12 w-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-slate-900 mb-2">Tentang Algoritma K-Means</h3>
                    <p class="text-sm text-slate-600 mb-3">Algoritma unsupervised learning yang mengelompokkan kendaraan berdasarkan 3 atribut utama:</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <p class="text-xs font-semibold text-blue-700 uppercase">Frequency</p>
                            <p class="text-xs text-slate-600 mt-1">Jumlah pemesanan</p>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                            <p class="text-xs font-semibold text-emerald-700 uppercase">Duration</p>
                            <p class="text-xs text-slate-600 mt-1">Rata-rata durasi (hari)</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <p class="text-xs font-semibold text-purple-700 uppercase">Intensity</p>
                            <p class="text-xs text-slate-600 mt-1">Pemesanan / bulan</p>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600">📐 Perhitungan jarak menggunakan <strong class="text-slate-900">Euclidean Distance</strong> dengan normalisasi <strong class="text-slate-900">min-max</strong>. Iterasi berhenti otomatis saat centroid stabil (konvergen).</p>
                </div>
            </div>
        </div>

        {{-- Ringkasan Cluster --}}
        @if($latestResults->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 stagger">
            @foreach($clusterStats as $idx => $stat)
            @php
                $cardClass = match($idx) {
                    0 => 'cluster-card-high',
                    1 => 'cluster-card-mid',
                    2 => 'cluster-card-low',
                    default => 'cluster-card-mid',
                };
                $icon = match($idx) {
                    0 => '🔥',
                    1 => '⚡',
                    2 => '🌱',
                    default => '📊',
                };
            @endphp
            <div class="{{ $cardClass }} cluster-shine">
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-3xl">{{ $icon }}</span>
                        <span class="text-xs font-medium bg-white/20 px-2 py-1 rounded-full backdrop-blur">C{{ $idx }}</span>
                    </div>
                    <p class="text-sm text-white/80 font-medium">{{ $stat['name'] }}</p>
                    <p class="text-4xl font-bold mt-1">{{ $stat['count'] }}</p>
                    <p class="text-xs text-white/70 mt-1">kendaraan</p>
                    <div class="divider mt-3 bg-white/20"></div>
                    <p class="text-xs text-white/80 mt-2">Rata-rata Frequency: <strong>{{ $stat['avg_frequency'] }}</strong></p>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Daftar Kendaraan --}}
        <div class="card animate-slide-up">
            <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">📋 Data Kendaraan ({{ $totalVehicles }})</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Fitur yang akan dianalisis</p>
                </div>
                <span class="badge-blue">Dataset</span>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Kendaraan</th>
                            <th>Plat</th>
                            <th class="text-right">Frequency</th>
                            <th class="text-right">Duration</th>
                            <th class="text-right">Intensity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($features as $index => $feature)
                        <tr>
                            <td class="text-slate-500">{{ $index + 1 }}</td>
                            <td class="font-medium text-slate-900">{{ $feature['nama'] }}</td>
                            <td class="font-mono text-xs text-slate-600">{{ $feature['plat'] }}</td>
                            <td class="text-right font-semibold text-slate-900">{{ number_format($feature['frequency']) }}</td>
                            <td class="text-right text-slate-700">{{ number_format($feature['duration'], 1) }}</td>
                            <td class="text-right text-slate-700">{{ number_format($feature['intensity'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection