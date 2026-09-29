@extends('layouts.app')

@section('title', 'Data Driver')

@section('header', 'Data Driver')
@section('subheader', 'Manajemen data sopir / driver')

@section('content')
<div class="card animate-slide-up">
    <div class="px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <form method="GET" action="{{ route('drivers.index') }}" class="flex flex-col md:flex-row gap-2 flex-1">
            <div class="relative flex-1 max-w-xs">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / telepon..."
                       class="form-input pl-10">
            </div>
            <select name="status" class="form-select md:w-48">
                <option value="">Semua Status</option>
                @foreach(['Aktif', 'Tidak Aktif', 'Cuti'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-sm">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('drivers.index') }}" class="btn-ghost btn-sm">Reset</a>
            @endif
        </form>

        @if(auth()->user()->hasRole(['admin', 'operasional']))
        <a href="{{ route('drivers.create') }}" class="btn-success">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Driver
        </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">No</th>
                    <th>Driver</th>
                    <th>Telepon</th>
                    <th>No. SIM</th>
                    <th>Status</th>
                    <th class="text-center">Pemesanan</th>
                    @if(auth()->user()->hasRole(['admin', 'operasional']))
                    <th class="w-32">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($drivers as $index => $driver)
                <tr>
                    <td class="text-slate-500">{{ $drivers->firstItem() + $index }}</td>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="avatar-sm bg-gradient-to-br from-emerald-400 to-teal-500">
                                {{ strtoupper(substr($driver->nama_driver, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-slate-900">{{ $driver->nama_driver }}</div>
                                @if($driver->alamat)
                                <div class="text-xs text-slate-500 line-clamp-1">{{ Str::limit($driver->alamat, 40) }}</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <code class="text-slate-700 text-sm font-mono">{{ $driver->nomor_telepon }}</code>
                    </td>
                    <td class="text-slate-700">{{ $driver->nomor_sim ?? '—' }}</td>
                    <td>
                        <span class="badge-{{ $driver->status_color }}">
                            <span class="dot bg-{{ $driver->status_color }}-500 mr-1.5"></span>
                            {{ $driver->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span class="inline-flex items-center justify-center min-w-[2rem] px-2 h-7 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">{{ $driver->bookings_count }}x</span>
                    </td>
                    @if(auth()->user()->hasRole(['admin', 'operasional']))
                    <td>
                        <div class="flex items-center gap-1">
                            <a href="{{ route('drivers.edit', $driver) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('drivers.destroy', $driver) }}" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
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
                    <td colspan="{{ auth()->user()->hasRole(['admin', 'operasional']) ? 7 : 6 }}" class="text-center py-16 text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v1v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2"/></svg>
                            <p class="text-sm">Belum ada data driver</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200">
        {{ $drivers->links() }}
    </div>
</div>
@endsection