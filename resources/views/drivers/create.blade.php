@extends('layouts.app')

@section('title', 'Tambah Driver')

@section('header', 'Tambah Driver')
@section('subheader', 'Tambahkan data driver / sopir baru')

@section('content')
<div class="max-w-2xl">
    <div class="card card-padded animate-slide-up">
        <form method="POST" action="{{ route('drivers.store') }}" class="space-y-5">
            @csrf

            <div class="form-group">
                <label for="nama_driver" class="form-label">Nama Driver <span class="text-red-500">*</span></label>
                <input type="text" name="nama_driver" id="nama_driver" value="{{ old('nama_driver') }}"
                       class="form-input @error('nama_driver') form-input-error @enderror"
                       placeholder="Contoh: Budi Santoso" required>
                @error('nama_driver') <p class="form-error">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon" value="{{ old('nomor_telepon') }}"
                           class="form-input font-mono @error('nomor_telepon') form-input-error @enderror"
                           placeholder="081234567890" required>
                    @error('nomor_telepon') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label for="nomor_sim" class="form-label">Nomor SIM</label>
                    <input type="text" name="nomor_sim" id="nomor_sim" value="{{ old('nomor_sim') }}"
                           class="form-input font-mono" placeholder="1234567890123456">
                </div>
            </div>

            <div class="form-group">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="form-textarea"
                          placeholder="Alamat lengkap driver...">{{ old('alamat') }}</textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    @foreach(['Aktif', 'Tidak Aktif', 'Cuti'] as $status)
                        <option value="{{ $status }}" {{ old('status', 'Aktif') == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="divider"></div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('drivers.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection