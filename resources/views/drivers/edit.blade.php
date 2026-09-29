@extends('layouts.app')

@section('title', 'Edit Driver')

@section('header', 'Edit Driver')
@section('subheader', 'Perbarui data driver: ' . $driver->nama_driver)

@section('content')
<div class="max-w-2xl">
    <div class="card card-padded animate-slide-up">
        <form method="POST" action="{{ route('drivers.update', $driver) }}" class="space-y-5">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="nama_driver" class="form-label">Nama Driver <span class="text-red-500">*</span></label>
                <input type="text" name="nama_driver" id="nama_driver"
                       value="{{ old('nama_driver', $driver->nama_driver) }}"
                       class="form-input @error('nama_driver') form-input-error @enderror" required>
                @error('nama_driver')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="form-group">
                    <label for="nomor_telepon" class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="nomor_telepon" id="nomor_telepon"
                           value="{{ old('nomor_telepon', $driver->nomor_telepon) }}"
                           class="form-input font-mono @error('nomor_telepon') form-input-error @enderror" required>
                    @error('nomor_telepon')<p class="form-error">{{ $message }}</p>@enderror
                </div>

                <div class="form-group">
                    <label for="nomor_sim" class="form-label">Nomor SIM</label>
                    <input type="text" name="nomor_sim" id="nomor_sim"
                           value="{{ old('nomor_sim', $driver->nomor_sim) }}" class="form-input font-mono">
                </div>
            </div>

            <div class="form-group">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3" class="form-textarea">{{ old('alamat', $driver->alamat) }}</textarea>
            </div>

            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                <select name="status" id="status" class="form-select" required>
                    @foreach(['Aktif', 'Tidak Aktif', 'Cuti'] as $status)
                        <option value="{{ $status }}" {{ old('status', $driver->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
            </div>

            <div class="divider"></div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('drivers.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Update
                </button>
            </div>
        </form>
    </div>
</div>
@endsection