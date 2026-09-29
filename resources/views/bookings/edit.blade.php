@extends('layouts.app')

@section('title', 'Edit Pemesanan')

@section('header', 'Edit Pemesanan')
@section('subheader', 'Perbarui data pemesanan #' . $booking->id)

@section('content')
<div class="max-w-3xl">
    <div class="card card-padded animate-slide-up">
        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="vehicle_id" class="form-label">Kendaraan <span class="text-red-500">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') form-input-error @enderror" required>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id', $booking->vehicle_id) == $v->id ? 'selected' : '' }}>
                                {{ $v->nama_kendaraan }} ({{ $v->plat_nomor }})
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="driver_id" class="form-label">Driver</label>
                    <select name="driver_id" id="driver_id" class="form-select">
                        <option value="">Tanpa Driver</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}" {{ old('driver_id', $booking->driver_id) == $d->id ? 'selected' : '' }}>{{ $d->nama_driver }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pemesanan" id="tanggal_pemesanan"
                           value="{{ old('tanggal_pemesanan', $booking->tanggal_pemesanan->format('Y-m-d')) }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="jumlah_penumpup" class="form-label">Jumlah Penumpang <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_penumpup" id="jumlah_penumpup"
                           value="{{ old('jumlah_penumpup', $booking->jumlah_penumpup) }}" min="1" max="50" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                           value="{{ old('tanggal_mulai', $booking->tanggal_mulai->format('Y-m-d')) }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                           value="{{ old('tanggal_selesai', $booking->tanggal_selesai->format('Y-m-d')) }}" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="tujuan" class="form-label">Tujuan <span class="text-red-500">*</span></label>
                <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan', $booking->tujuan) }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="keperluan" class="form-label">Keperluan <span class="text-red-500">*</span></label>
                <textarea name="keperluan" id="keperluan" rows="3" class="form-textarea" required>{{ old('keperluan', $booking->keperluan) }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('bookings.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection