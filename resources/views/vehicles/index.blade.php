@extends('layouts.app')

@section('title', 'Data Kendaraan')

@section('header', 'Data Kendaraan')
@section('subheader', 'Manajemen data kendaraan dinas')

@section('content')
<div class="card animate-slide-up">
    {{-- Toolbar --}}
    <div class="px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <form method="GET" action="{{ route('vehicles.index') }}" class="flex flex-col md:flex-row gap-2 flex-1">
            <div class="relative flex-1 max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / plat nomor..."
                       class="form-input pl-10">
            </div>
            <select name="status" class="form-select md:w-48">
                <option value="">Semua Status</option>
                @foreach(['Tersedia', 'Tidak Tersedia', 'Dalam Perbaikan', 'Digunakan'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('vehicles.index') }}" class="btn-ghost btn-sm">Reset</a>
            @endif
        </form>

        @if(auth()->user()->hasRole(['admin', 'operasional']))
        <a href="{{ route('vehicles.create') }}" class="btn-success">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Kendaraan
        </a>
        @endif
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">No</th>
                    <th>Kendaraan</th>
                    <th>Plat Nomor</th>
                    <th>Jenis</th>
                    <th>Kapasitas</th>
                    <th>Status</th>
                    <th class="text-center">Pemesanan</th>
                    @if(auth()->user()->hasRole(['admin', 'operasional']))
                    <th class="w-32">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($vehicles as $index => $vehicle)
                <tr>
                    <td class="text-slate-500">{{ $vehicles->firstItem() + $index }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 text-white flex items-center justify-center shadow-sm">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900">{{ $vehicle->nama_kendaraan }}</div>
                                @if($vehicle->keterangan)
                                <div class="text-xs text-slate-500 line-clamp-1">{{ $vehicle->keterangan }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="px-2 py-1 bg-slate-100 rounded text-slate-700 text-sm font-mono">{{ $vehicle->plat_nomor }}</code>
                    </td>
                    <td>
                        <span class="badge-purple">{{ $vehicle->jenis_kendaraan }}</span>
                    </td>
                    <td class="text-slate-700">
                        <span class="font-semibold">{{ $vehicle->kapasitas_penumpup }}</span>
                        <span class="text-xs text-slate-500">orang</span>
                    </td>
                    <td>
                        <span class="badge-{{ $vehicle->status_color }}">
                            <span class="dot bg-{{ $vehicle->status_color }}-500 mr-1.5"></span>
                            {{ $vehicle->status_ketersediaan }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 h-7 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">{{ $vehicle->bookings_count }}x</span>
                    </td>
                    @if(auth()->user()->hasRole(['admin', 'operasional']))
                    <td>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('vehicles.edit', $vehicle) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ auth()->user()->hasRole(['admin', 'operasional']) ? 8 : 7 }}" class="text-center py-16 text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16"/></svg>
                            <p class="text-sm">Belum ada data kendaraan</p>
                            @if(auth()->user()->hasRole(['admin', 'operasional']))
                            <a href="{{ route('vehicles.create') }}" class="btn-success btn-sm mt-2">Tambah Kendaraan</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200">
        {{ $vehicles->links() }}
    </div>
</div>
@endsection