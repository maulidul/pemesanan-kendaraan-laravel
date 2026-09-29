@extends('layouts.app')

@section('title', 'Approval Pemesanan')

@section('header', 'Approval Pemesanan')
@section('subheader', 'Proses persetujuan pemesanan kendaraan dari operasional')

@section('content')
<div class="card animate-slide-up">
    <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
        <form method="GET" action="{{ route('bookings.approval') }}" class="flex gap-2">
            <select name="status" class="form-select md:w-44">
                <option value="">Semua Status</option>
                @foreach(['pending', 'disetujui', 'ditolak', 'selesai'] as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn-primary btn-sm">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-12">No</th>
                    <th>Pemohon</th>
                    <th>Kendaraan</th>
                    <th>Tujuan & Keperluan</th>
                    <th>Periode</th>
                    <th>Status</th>
                    <th class="w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $index => $booking)
                <tr>
                    <td class="text-slate-500">{{ $bookings->firstItem() + $index }}</td>
                    <td>
                        <div class="font-semibold text-slate-900">{{ $booking->user->name ?? '-' }}</div>
                        <div class="text-xs text-slate-500">{{ $booking->user->email ?? '' }}</div>
                    </td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $booking->vehicle->nama_kendaraan ?? '-' }}</div>
                        <code class="text-xs font-mono text-slate-500">{{ $booking->vehicle->plat_nomor ?? '' }}</code>
                    </td>
                    <td>
                        <div class="font-medium text-slate-900">{{ $booking->tujuan }}</div>
                        <div class="text-xs text-slate-500 line-clamp-1">{{ $booking->keperluan }}</div>
                    </td>
                    <td>
                        <div class="text-sm font-medium text-slate-900">{{ $booking->tanggal_mulai->format('d M Y') }}</div>
                        <div class="text-xs text-slate-500">s/d {{ $booking->tanggal_selesai->format('d M Y') }}</div>
                    </td>
                    <td>
                        <span class="badge-{{ $booking->status_color }}">
                            <span class="dot bg-{{ $booking->status_color }}-500 mr-1.5"></span>
                            {{ $booking->status_label }}
                        </span>
                    </td>
                    <td>
                        <button onclick="document.getElementById('approval-form-{{ $booking->id }}').classList.toggle('hidden')"
                                class="btn-primary btn-sm">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Proses
                        </button>
                    </td>
                </tr>
                <tr id="approval-form-{{ $booking->id }}" class="hidden">
                    <td colspan="7" class="bg-slate-50 px-6 py-4">
                        <form method="POST" action="{{ route('bookings.processApproval', $booking) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <div>
                                    <label class="form-label text-xs">Status</label>
                                    <select name="status" required class="form-select text-sm">
                                        <option value="disetujui" {{ $booking->status_persetujuan === 'disetujui' ? 'selected' : '' }}>✓ Setujui</option>
                                        <option value="ditolak" {{ $booking->status_persetujuan === 'ditolak' ? 'selected' : '' }}>✗ Tolak</option>
                                        <option value="selesai" {{ $booking->status_persetujuan === 'selesai' ? 'selected' : '' }}>🏁 Tandai Selesai</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="form-label text-xs">Catatan</label>
                                    <input type="text" name="catatan" value="{{ old('catatan', $booking->catatan_persetujuan) }}"
                                           class="form-input text-sm" placeholder="Catatan (opsional)">
                                </div>
                            </div>
                            <div class="flex justify-end gap-2">
                                <button type="button" onclick="document.getElementById('approval-form-{{ $booking->id }}').classList.add('hidden')" class="btn-ghost btn-sm">Batal</button>
                                <button type="submit" class="btn-success btn-sm">Simpan</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-16 text-slate-500">Belum ada data pemesanan.</td>
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