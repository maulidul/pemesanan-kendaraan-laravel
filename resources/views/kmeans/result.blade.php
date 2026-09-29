@extends('layouts.app')

@section('title', 'Hasil Analisis K-Means')

@section('header', 'Hasil Analisis K-Means Clustering')
@section('subheader', 'Detail pengelompokan kendaraan dan history iterasi hingga konvergen')

@section('content')

{{-- ============================================
     STATUS BOX
     ============================================ --}}
<div class="mb-6 p-5 rounded-2xl border {{ $result['converged'] ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }} animate-slide-up">
    <div class="flex items-start gap-4">
        @if($result['converged'])
        <div class="h-12 w-12 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg flex-shrink-0">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="flex-1">
            <p class="font-bold text-emerald-900 text-lg">✓ Algoritma Konvergen</p>
            <p class="text-sm text-emerald-700 mt-1">
                Iterasi berhenti pada <strong>iterasi ke-{{ $result['iterations'] }}</strong> dengan K=<strong>{{ $k }}</strong>, maks. iterasi <strong>{{ $maxIterations }}</strong>, threshold <strong>{{ $tolerance }}</strong>.
                Hasil analisis telah disimpan ke database.
            </p>
        </div>
        @else
        <div class="h-12 w-12 rounded-xl bg-red-500 flex items-center justify-center text-white shadow-lg flex-shrink-0">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1">
            <p class="font-bold text-red-900 text-lg">✗ Belum Konvergen</p>
            <p class="text-sm text-red-700 mt-1">Coba naikkan jumlah iterasi maksimum atau naikkan threshold konvergensi.</p>
        </div>
        @endif
    </div>
</div>

{{-- ============================================
     RINGKASAN CLUSTER (dengan card gradient)
     ============================================ --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8 stagger">
    @foreach($clusterSummary as $idx => $summary)
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
                <span class="text-xs font-medium bg-white/25 px-2.5 py-1 rounded-full backdrop-blur">C{{ $idx }}</span>
            </div>
            <p class="text-sm text-white/80 font-medium uppercase tracking-wide">{{ $summary['name'] }}</p>
            <p class="text-5xl font-bold mt-1">{{ $summary['count'] }}</p>
            <p class="text-xs text-white/70 mt-1">kendaraan dikelompokkan</p>

            <div class="divider mt-4 bg-white/20"></div>
            <div class="mt-3 space-y-1.5 text-xs">
                <div class="flex justify-between text-white/80">
                    <span>Avg Frequency</span>
                    <strong class="text-white">{{ $summary['avg_frequency'] }}</strong>
                </div>
                <div class="flex justify-between text-white/80">
                    <span>Avg Duration</span>
                    <strong class="text-white">{{ $summary['avg_duration'] }} hari</strong>
                </div>
                <div class="flex justify-between text-white/80">
                    <span>Avg Intensity</span>
                    <strong class="text-white">{{ $summary['avg_intensity'] }}</strong>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ============================================
     TABEL HASIL CLUSTERING
     ============================================ --}}
<div class="card mb-6 animate-slide-up">
    <div class="px-6 py-4 border-b border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">📊 Hasil Pengelompokan Kendaraan</h3>
                <p class="text-xs text-slate-500 mt-0.5">Setiap kendaraan diklasifikasikan ke cluster dengan jarak Euclidean terdekat</p>
            </div>
            <span class="badge-blue">{{ count($rows) }} Kendaraan</span>
        </div>
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
                    <th>Cluster</th>
                    <th class="text-right">Jarak Euclidean</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $index => $row)
                @php
                    $badgeClass = match($row['cluster']) {
                        0 => 'badge-red',
                        1 => 'badge-yellow',
                        2 => 'badge-green',
                        default => 'badge-gray',
                    };
                @endphp
                <tr>
                    <td class="text-slate-500">{{ $index + 1 }}</td>
                    <td class="font-medium text-slate-900">{{ $row['nama'] }}</td>
                    <td class="font-mono text-xs text-slate-600">{{ $row['plat'] }}</td>
                    <td class="text-right font-semibold">{{ number_format($row['frequency'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['duration'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['intensity'], 2) }}</td>
                    <td>
                        <span class="{{ $badgeClass }}">C{{ $row['cluster'] }} · {{ $row['cluster_name'] }}</span>
                    </td>
                    <td class="text-right font-mono text-xs text-slate-700">{{ number_format($row['distance'], 4) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================
     HISTORY ITERASI CENTROID
     ============================================ --}}
<div class="card mb-6 animate-slide-up">
    <div class="px-6 py-4 border-b border-slate-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">🔄 History Iterasi Centroid</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perubahan posisi centroid hingga konvergen (max_shift &lt; tolerance)</p>
            </div>
            <span class="badge-green">✓ {{ count($result['history']) }} Iterasi</span>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center w-24">Iterasi</th>
                    @for($i = 0; $i < $k; $i++)
                    <th class="text-center">Centroid {{ $i }} — {{ $result['cluster_names'][$i] }}</th>
                    @endfor
                    <th class="text-center">Max Shift</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($result['history'] as $hist)
                <tr>
                    <td class="text-center">
                        <span class="badge-blue">{{ $hist['iteration'] }}</span>
                    </td>
                    @foreach($hist['centroids'] as $cIdx => $centroid)
                    <td class="text-center font-mono text-xs">
                        <div class="inline-block text-left bg-slate-50 px-3 py-2 rounded-lg border border-slate-200">
                            <div class="text-blue-700">[{{ number_format($centroid[0], 4) }},</div>
                            <div class="text-emerald-700 ml-2">{{ number_format($centroid[1], 4) }},</div>
                            <div class="text-purple-700 ml-2">{{ number_format($centroid[2], 4) }}]</div>
                        </div>
                    </td>
                    @endforeach
                    <td class="text-center font-mono text-xs">
                        <span class="{{ $hist['max_shift'] < $tolerance ? 'text-emerald-600 font-bold' : 'text-slate-700' }}">
                            {{ number_format($hist['max_shift'], 6) }}
                        </span>
                        @if($hist['max_shift'] < $tolerance)
                            <div class="progress-track mt-2">
                                <div class="progress-bar progress-bar-success" style="width: 100%"></div>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($hist['max_shift'] < $tolerance)
                            <span class="badge-green">✓ Konvergen</span>
                        @else
                            <span class="badge-gray">Iterasi</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================
     ACTION BUTTONS
     ============================================ --}}
<div class="flex justify-between items-center">
    <a href="{{ route('kmeans.index') }}" class="btn-secondary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
    <button onclick="window.print()" class="btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        Cetak / Print
    </button>
</div>
@endsection