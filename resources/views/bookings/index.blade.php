@extends('layouts.app')

@section('title', 'Pemesanan Kendaraan')

@section('header', 'Pemesanan Kendaraan')
@section('subheader', 'Riwayat pemesanan kendaraan dinas')

@section('content')
<div class="card animate-slide-up">
    <div class="px-6 py-4 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <form method="GET" action="{{ route('bookings.index') }}" class="flex flex-col md:flex-row gap-2 flex-1">
            <select name="status" class="form-select md:w-44">
                <option value="">Semua Status</option>
                @foreach(['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="vehicle_id" class="form-select md:w-56">
                <option value="">Semua Kendaraan</option>
                @foreach($vehicles as $v)
                    <option value="{{ $v->id }}" {{ request('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->nama_kendaraan }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-sm">Filter</button>
            @if(request('status') || request('vehicle_id'))
                <a href="{{ route('bookings.index') }}" class="btn-ghost btn-sm">Reset</a>
            @endif
        </form>

        <a href="{{ route('bookings.create') }}" class="btn-success">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Ajukan Pemesanan
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">No</th>
                    <th>Pemohon</th>
                    <th>Kendaraan</th>
                    <th>Driver</th>
                    <th>Periode</th>
                    <th>Tujuan</th>
                    <th>Status</th>
                    <th class="w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $index => $booking)
                <tr>
                    <td class="text-slate-500">{{ $bookings->firstItem() + $index }}</td>
                    <td>
                        <div class="flex items-center gap-2">
                            <div class="avatar-sm bg-gradient-to-br from-purple-400 to-pink-500">
                                {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <span class="font-medium text-slate-900">{{ $booking->user->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="text-slate-700">{{ $booking->vehicle->nama_kendaraan ?? '-' }}</td>
                    <td class="text-slate-700">{{ $booking->driver->nama_driver ?? '—' }}</td>
                    <td>
                        <div class="text-sm font-medium text-slate-900">{{ $booking->tanggal_mulai->format('d M Y') }}</div>
                        <div class="text-xs text-slate-500">s/d {{ $booking->tanggal_selesai->format('d M Y') }}</div>
                    </td>
                    <td>
                        <div class="line-clamp-1 max-w-xs">{{ $booking->tujuan }}</div>
                    </td>
                    <td>
                        <span class="badge-{{ $booking->status_color }}">
                            <span class="dot bg-{{ $booking->status_color }}-500 mr-1.5"></span>
                            {{ $booking->status_label }}
                        </span>
                    </td>
                    <td>
                        @if(auth()->user()->hasRole(['admin', 'operasional']) || $booking->user_id === auth()->id())
                        <div class="flex items-center gap-1">
                            <a href="{{ route('bookings.edit', $booking) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('bookings.destroy', $booking) }}" onsubmit="return confirm('Yakin ingin membatalkan pemesanan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-16 text-slate-500">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="h-12 w-12 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/></svg>
                            <p class="text-sm">Belum ada data pemesanan</p>
                            <a href="{{ route('bookings.create') }}" class="btn-success btn-sm mt-2">Ajukan Pemesanan</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-200">
        {{ $bookings->links() }}
    </div>
</div>
@endsection