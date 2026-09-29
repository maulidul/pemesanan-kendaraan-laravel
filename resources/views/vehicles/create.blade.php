@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('header', 'Tambah Kendaraan')
@section('subheader', 'Tambahkan data kendaraan dinas baru')

@section('content')
<div class="max-w-2xl">
    <div class="card card-padded animate-slide-up">
        <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-5">
            @csrf

            <div class="form-group">
                <label for="nama_kendaraan" class="form-label">Nama Kendaraan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kendaraan" id="nama_kendaraan"
                       value="{{ old('nama_kendaraan') }}"
                       class="form-input @error('nama_kendaraan') form-input-error @enderror"
                       placeholder="Contoh: Toyota Innova Reborn" required>
                @error('nama_kendaraan') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="plat_nomor" class="form-label">Plat Nomor <span class="text-red-500">*</span></label>
                    <input type="text" name="plat_nomor" id="plat_nomor" value="{{ old('plat_nomor') }}"
                           class="form-input font-mono @error('plat_nomor') form-input-error @enderror"
                           placeholder="B 1234 XYZ" required>
                    @error('plat_nomor') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="jenis_kendaraan" class="form-label">Jenis Kendaraan <span class="text-red-500">*</span></label>
                    <select name="jenis_kendaraan" id="jenis_kendaraan" class="form-select" required>
                        <option value="">Pilih jenis</option>
                        @foreach(['Sedan', 'SUV', 'Minibus', 'Pick Up', 'Truk', 'Bus', 'Motor Dinas'] as $jenis)
                            <option value="{{ $jenis }}" {{ old('jenis_kendaraan') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="kapasitas_penumpup" class="form-label">Kapasitas Penumpang <span class="text-red-500">*</span></label>
                    <input type="number" name="kapasitas_penumpup" id="kapasitas_penumpup"
                           value="{{ old('kapasitas_penumpup', 4) }}" min="1" max="100" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="status_ketersediaan" class="form-label">Status Ketersediaan <span class="text-red-500">*</span></label>
                    <select name="status_ketersediaan" id="status_ketersediaan" class="form-select" required>
                        @foreach(['Tersedia', 'Tidak Tersedia', 'Dalam Perbaikan', 'Digunakan'] as $status)
                            <option value="{{ $status }}" {{ old('status_ketersediaan', 'Tersedia') == $status ? 'selected' : '' }}>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea name="keterangan" id="keterangan" rows="3" class="form-textarea"
                          placeholder="Catatan tambahan tentang kendaraan...">{{ old('keterangan') }}</textarea>
            </div>

            <div class="divider"></div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('vehicles.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection