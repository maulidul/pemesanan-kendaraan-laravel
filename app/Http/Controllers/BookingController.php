<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BookingController extends Controller
{
    /**
     * Tampilkan daftar pemesanan.
     */
    public function index(Request $request): View
    {
        $query = Booking::with(['user', 'vehicle', 'driver']);

        // Filter status
        if ($request->filled('status')) {
            $query->where('status_persetujuan', $request->status);
        }

        // Filter kendaraan
        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        // Filter hanya punya user yang login (untuk role pemohon)
        if (auth()->user()->hasRole('pemohon')) {
            $query->where('user_id', auth()->id());
        }

        $bookings = $query->latest('tanggal_pemesanan')
            ->paginate(10)
            ->withQueryString();

        $vehicles = Vehicle::orderBy('nama_kendaraan')->get();

        return view('bookings.index', compact('bookings', 'vehicles'));
    }

    /**
     * Form pengajuan pemesanan baru.
     */
    public function create(): View
    {
        $vehicles = Vehicle::tersedia()->orderBy('nama_kendaraan')->get();
        $drivers  = Driver::aktif()->orderBy('nama_driver')->get();

        return view('bookings.create', compact('vehicles', 'drivers'));
    }

    /**
     * Simpan pemesanan baru (status default: pending).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'driver_id'        => 'nullable|exists:drivers,id',
            'tanggal_pemesanan'=> 'required|date',
            'tanggal_mulai'    => 'required|date|after_or_equal:tanggal_pemesanan',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan'           => 'required|string|max:200',
            'keperluan'        => 'required|string|max:1000',
            'jumlah_penumpup'  => 'required|integer|min:1|max:50',
        ], [
            'tanggal_mulai.after_or_equal'   => 'Tanggal mulai tidak boleh sebelum tanggal pemesanan.',
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
        ]);

        // Cek konflik jadwal kendaraan
        $conflict = Booking::where('vehicle_id', $validated['vehicle_id'])
            ->whereIn('status_persetujuan', ['pending', 'disetujui'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('tanggal_mulai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                  ->orWhereBetween('tanggal_selesai', [$validated['tanggal_mulai'], $validated['tanggal_selesai']])
                  ->orWhere(function ($q2) use ($validated) {
                      $q2->where('tanggal_mulai', '<=', $validated['tanggal_mulai'])
                         ->where('tanggal_selesai', '>=', $validated['tanggal_selesai']);
                  });
            })
            ->exists();

        if ($conflict) {
            return back()->withInput()
                ->with('error', 'Tanggal yang dipilih bentrok dengan pemesanan lain untuk kendaraan tersebut.');
        }

        $validated['user_id']             = auth()->id();
        $validated['status_persetujuan']  = 'pending';

        Booking::create($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Pemesanan berhasil diajukan. Menunggu persetujuan operasional.');
    }

    /**
     * Form edit (hanya untuk pemohon yang sama & status pending).
     */
    public function edit(Booking $booking): View
    {
        // Pastikan user hanya bisa edit miliknya sendiri (kecuali admin/operasional)
        if (auth()->user()->hasRole('pemohon') && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $vehicles = Vehicle::orderBy('nama_kendaraan')->get();
        $drivers  = Driver::orderBy('nama_driver')->get();

        return view('bookings.edit', compact('booking', 'vehicles', 'drivers'));
    }

    /**
     * Update pemesanan.
     */
    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id'       => 'required|exists:vehicles,id',
            'driver_id'        => 'nullable|exists:drivers,id',
            'tanggal_pemesanan'=> 'required|date',
            'tanggal_mulai'    => 'required|date|after_or_equal:tanggal_pemesanan',
            'tanggal_selesai'  => 'required|date|after_or_equal:tanggal_mulai',
            'tujuan'           => 'required|string|max:200',
            'keperluan'        => 'required|string|max:1000',
            'jumlah_penumpup'  => 'required|integer|min:1|max:50',
        ]);

        // Hanya boleh edit jika status pending
        if ($booking->status_persetujuan !== 'pending' && auth()->user()->hasRole('pemohon')) {
            return back()->with('error', 'Pemesanan yang sudah diproses tidak dapat diubah.');
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')
            ->with('success', 'Data pemesanan berhasil diperbarui.');
    }

    /**
     * Hapus pemesanan.
     */
    public function destroy(Booking $booking): RedirectResponse
    {
        if (auth()->user()->hasRole('pemohon') && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $booking->delete();

        return redirect()->route('bookings.index')
            ->with('success', 'Pemesanan berhasil dihapus.');
    }

    /**
     * Halaman tabel approval (khusus operasional & admin).
     */
    public function approval(Request $request): View
    {
        abort_unless(auth()->user()->hasRole(['admin', 'operasional']), 403);

        $query = Booking::with(['user', 'vehicle', 'driver']);

        if ($request->filled('status')) {
            $query->where('status_persetujuan', $request->status);
        }

        $bookings = $query->latest('tanggal_pemesanan')
            ->paginate(15)
            ->withQueryString();

        return view('bookings.approval', compact('bookings'));
    }

    /**
     * Proses approval (setujui / tolak).
     */
    public function processApproval(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless(auth()->user()->hasRole(['admin', 'operasional']), 403);

        $validated = $request->validate([
            'status'    => 'required|in:disetujui,ditolak,selesai',
            'catatan'   => 'nullable|string|max:500',
        ]);

        $booking->update([
            'status_persetujuan'    => $validated['status'],
            'catatan_persetujuan'   => $validated['catatan'] ?? null,
            'tanggal_diseputujui'   => Carbon::now(),
        ]);

        // Jika disetujui, set kendaraan menjadi Digunakan
        if ($validated['status'] === 'disetujui' && $booking->vehicle) {
            $booking->vehicle->update(['status_ketersediaan' => 'Digunakan']);
        }

        // Jika selesai, kembalikan kendaraan menjadi Tersedia
        if ($validated['status'] === 'selesai' && $booking->vehicle) {
            $booking->vehicle->update(['status_ketersediaan' => 'Tersedia']);
        }

        return redirect()->route('bookings.approval')
            ->with('success', 'Status pemesanan berhasil diperbar.');
    }
}