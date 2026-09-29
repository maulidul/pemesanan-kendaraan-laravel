<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    /**
     * Tampilkan daftar driver.
     */
    public function index(Request $request): View
    {
        $query = Driver::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_driver', 'like', "%{$search}%")
                  ->orWhere('nomor_telepon', 'like', "%{$search}%");
            });
        }

        $drivers = $query->withCount('bookings')
            ->orderBy('nama_driver')
            ->paginate(10)
            ->withQueryString();

        return view('drivers.index', compact('drivers'));
    }

    public function create(): View
    {
        return view('drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_driver'    => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:20',
            'nomor_sim'      => 'nullable|string|max:30',
            'alamat'         => 'nullable|string|max:500',
            'status'         => 'required|in:Aktif,Tidak Aktif,Cuti',
        ]);

        Driver::create($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Data driver berhasil ditambahkan.');
    }

    public function edit(Driver $driver): View
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate([
            'nama_driver'    => 'required|string|max:100',
            'nomor_telepon'  => 'required|string|max:20',
            'nomor_sim'      => 'nullable|string|max:30',
            'alamat'         => 'nullable|string|max:500',
            'status'         => 'required|in:Aktif,Tidak Aktif,Cuti',
        ]);

        $driver->update($validated);

        return redirect()->route('drivers.index')
            ->with('success', 'Data driver berhasil diperbarui.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        try {
            $driver->delete();
            return redirect()->route('drivers.index')
                ->with('success', 'Data driver berhasil dihapus.');
        } catch (\Throwable $e) {
            return redirect()->route('drivers.index')
                ->with('error', 'Driver tidak dapat dihapus karena masih memiliki data pemesanan.');
        }
    }
}