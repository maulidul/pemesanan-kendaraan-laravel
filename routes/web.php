<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\KMeansController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Pemesanan Kendaraan Dinas
|--------------------------------------------------------------------------
|
| Semua route yang membutuhkan autentikasi dibungkus middleware 'auth'.
| Role-based access menggunakan middleware 'role' (lihat EnsureUserHasRole).
|
*/

// =============== ROUTE PUBLIK ===============
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// =============== AUTH (Login/Logout) ===============
// Catatan: register dinonaktifkan (sesuai requirement).
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// =============== ROUTE TERPROTEKSI ===============
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---- Kendaraan (Master Data) ----
    // Index & Show dapat diakses semua role, create/edit/delete khusus admin & operasional
    Route::get('/vehicles',          [VehicleController::class, 'index'])->name('vehicles.index');
    Route::middleware('role:admin,operasional')->group(function () {
        Route::get('/vehicles/create',  [VehicleController::class, 'create'])->name('vehicles.create');
        Route::post('/vehicles',        [VehicleController::class, 'store'])->name('vehicles.store');
        Route::get('/vehicles/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
        Route::put('/vehicles/{vehicle}',      [VehicleController::class, 'update'])->name('vehicles.update');
        Route::delete('/vehicles/{vehicle}',   [VehicleController::class, 'destroy'])->name('vehicles.destroy');
    });

    // ---- Driver (Master Data) ----
    Route::get('/drivers',          [DriverController::class, 'index'])->name('drivers.index');
    Route::middleware('role:admin,operasional')->group(function () {
        Route::get('/drivers/create',  [DriverController::class, 'create'])->name('drivers.create');
        Route::post('/drivers',        [DriverController::class, 'store'])->name('drivers.store');
        Route::get('/drivers/{driver}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
        Route::put('/drivers/{driver}',      [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/drivers/{driver}',   [DriverController::class, 'destroy'])->name('drivers.destroy');
    });

    // ---- Pemesanan (Transaksi) ----
    // Semua user dapat melihat & membuat pemesanan.
    // Edit/hapus dibatasi sesuai role (lihat controller).
    Route::get('/bookings',            [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/create',     [BookingController::class, 'create'])->name('bookings.create');
    Route::post('/bookings',           [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
    Route::put('/bookings/{booking}',      [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{booking}',   [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Halaman approval khusus admin/operasional
    Route::middleware('role:admin,operasional')->group(function () {
        Route::get('/bookings-approval',                          [BookingController::class, 'approval'])->name('bookings.approval');
        Route::patch('/bookings/{booking}/process-approval',      [BookingController::class, 'processApproval'])->name('bookings.processApproval');
    });

    // ---- K-Means Clustering (Analisis Data Mining) ----
    // Akses untuk semua role (termasuk manajemen/atasan sesuai requirement).
    Route::get('/kmeans',           [KMeansController::class, 'index'])->name('kmeans.index');
    Route::post('/kmeans/analyze',  [KMeansController::class, 'analyze'])->name('kmeans.analyze');
    Route::middleware('role:admin,operasional,manajemen')->group(function () {
        Route::post('/kmeans/reset', [KMeansController::class, 'reset'])->name('kmeans.reset');
    });
});