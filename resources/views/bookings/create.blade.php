@extends('layouts.app')

@section('title', 'Ajukan Pemesanan')

@section('header', 'Ajukan Pemesanan Kendaraan')
@section('subheader', 'Form pengajuan pemesanan kendaraan dinas')

@section('content')
<div class="max-w-3xl">
    <div class="card card-padded animate-slide-up">
        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="vehicle_id" class="form-label">Kendaraan <span class="text-red-500">*</span></label>
                    <select name="vehicle_id" id="vehicle_id" class="form-select @error('vehicle_id') form-input-error @enderror" required>
                        <option value="">Pilih kendaraan</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                {{ $v->nama_kendaraan }} ({{ $v->plat_nomor }})
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="driver_id" class="form-label">Driver (Opsional)</label>
                    <select name="driver_id" id="driver_id" class="form-select">
                        <option value="">Tanpa Driver / Self Drive</option>
                        @foreach($drivers as $d)
                            <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>{{ $d->nama_driver }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="tanggal_pemesanan" class="form-label">Tanggal Pemesanan <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_pemesanan" id="tanggal_pemesanan"
                           value="{{ old('tanggal_pemesanan', date('Y-m-d')) }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="jumlah_penumpup" class="form-label">Jumlah Penumpang <span class="text-red-500">*</span></label>
                    <input type="number" name="jumlah_penumpup" id="jumlah_penumpup"
                           value="{{ old('jumlah_penumpup', 1) }}" min="1" max="50" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                           value="{{ old('tanggal_mulai') }}" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                           value="{{ old('tanggal_selesai') }}" class="form-input" required>
                </div>
            </div>

            <div class="form-group">
                <label for="tujuan" class="form-label">Tujuan <span class="text-red-500">*</span></label>
                <input type="text" name="tujuan" id="tujuan" value="{{ old('tujuan') }}"
                       class="form-input" placeholder="Contoh: Bandung - Jakarta" required>
            </div>

            <div class="form-group">
                <label for="keperluan" class="form-label">Keperluan <span class="text-red-500">*</span></label>
                <textarea name="keperluan" id="keperluan" rows="3" class="form-textarea" required
                          placeholder="Jelaskan keperluan / agenda pemesanan kendaraan...">{{ old('keperluan') }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('bookings.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Ajukan Pemesanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection