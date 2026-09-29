@extends('layouts.app')

@section('title', 'Profil Saya')

@section('header', 'Profil Saya')
@section('subheader', 'Kelola informasi akun Anda')

@section('content')
<div class="max-w-2xl space-y-6">
    {{-- Profile Card --}}
    <div class="card card-padded animate-slide-up">
        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-200">
            <div class="avatar-lg bg-gradient-cool ring-4 ring-white shadow-lg">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h3 class="font-bold text-slate-900">{{ $user->name }}</h3>
                <p class="text-sm text-slate-500">{{ $user->email }}</p>
                <span class="badge-blue mt-1">{{ $user->role_label }}</span>
            </div>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div class="form-group">
                <label for="name" class="form-label">Nama</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="form-input" required>
            </div>

            <div class="form-group">
                <label class="form-label">Role</label>
                <input type="text" value="{{ $user->role_label }}" disabled
                       class="form-input bg-slate-50 text-slate-500 cursor-not-allowed">
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
                @if (session('status') === 'profile-updated')
                    <span class="text-sm text-emerald-600 font-medium">✓ Tersimpan</span>
                @endif
            </div>
        </form>
    </div>

    {{-- Password Card --}}
    <div class="card card-padded animate-slide-up">
        <h3 class="font-bold text-slate-900 mb-1">Ubah Password</h3>
        <p class="text-sm text-slate-500 mb-5">Pastikan password Anda kuat dan unik.</p>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf @method('PUT')

            <div class="form-group">
                <label for="current_password" class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" id="current_password" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" name="password" id="password" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required>
            </div>

            <div class="flex items-center justify-between pt-2">
                <button type="submit" class="btn-primary">Update Password</button>
                @if (session('status') === 'password-updated')
                    <span class="text-sm text-emerald-600 font-medium">✓ Tersimpan</span>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection