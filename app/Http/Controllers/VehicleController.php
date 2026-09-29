<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    /**
     * Tampilkan daftar kendaraan.
     */
    public function index(Request $request): View
    {
        $query = Vehicle::query();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status_ketersediaan', $request->status);
        }

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kendaraan', 'like', "%{$search}%")
                  ->orWhere('plat_nomor', 'like', "%{$search}%");
            });
        }

        $vehicles = $query->withCount('bookings')
            ->orderBy('nama_kendaraan')
            ->paginate(10)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    /**
     * Tampilkan form tambah kendaraan.
     */
    public function create(): View
    {
        return view('vehicles.create');
    }

    /**
     * Simpan kendaraan baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kendaraan'      => 'required|string|max:100',
            'plat_nomor'          => 'required|string|max:20|unique:vehicles,plat_nomor',
            'jenis_kendaraan'     => 'required|in:Sedan,SUV,Minibus,Pick Up,Truk,Bus,Motor Dinas',
            'kapasitas_penumpup'  => 'required|integer|min:1|max:100',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia,Dalam Perbaikan,Digunakan',
            'keterangan'          => 'nullable|string|max:500',
        ]);

        Vehicle::create($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Data kendaraan berhasil ditambahkan.');
    }

    /**
     * Tampilkan form edit.
     */
    public function edit(Vehicle $vehicle): View
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update data kendaraan.
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'nama_kendaraan'      => 'required|string|max:100',
            'plat_nomor'          => 'required|string|max:20|unique:vehicles,plat_nomor,' . $vehicle->id,
            'jenis_kendaraan'     => 'required|in:Sedan,SUV,Minibus,Pick Up,Truk,Bus,Motor Dinas',
            'kapasitas_penumpup'  => 'required|integer|min:1|max:100',
            'status_ketersediaan' => 'required|in:Tersedia,Tidak Tersedia,Dalam Perbaikan,Digunakan',
            'keterangan'          => 'nullable|string|max:500',
        ]);

        $vehicle->update($validated);

        return redirect()->route('vehicles.index')
            ->with('success', 'Data kendaraan berhasil diperbarui.');
    }

    /**
     * Hapus kendaraan.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        try {
            $vehicle->delete();
            return redirect()->route('vehicles.index')
                ->with('success', 'Data kendaraan berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->route('vehicles.index')
                ->with('error', 'Kendaraan tidak dapat dihapus karena masih memiliki data pemesanan.');
        }
    }
}