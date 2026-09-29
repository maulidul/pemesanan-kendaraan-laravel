<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\KmeansResult;
use App\Models\Vehicle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard dengan ringkasan statistik.
     */
    public function index(): View
    {
        // ---- Statistik utama ----
        $totalBookings      = Booking::count();
        $totalKendaraan     = Vehicle::count();
        $kendaraanTersedia  = Vehicle::where('status_ketersediaan', 'Tersedia')->count();
        $totalDriver        = Driver::count();
        $driverAktif        = Driver::where('status', 'Aktif')->count();

        // Statistik status pemesanan
        $bookingsPending    = Booking::where('status_persetujuan', 'pending')->count();
        $bookingsDisetujui  = Booking::where('status_persetujuan', 'disetujui')->count();
        $bookingsSelesai    = Booking::where('status_persetujuan', 'selesai')->count();

        // ---- Grafik pemesanan 6 bulan terakhir ----
        $monthlyData = Booking::select(
                DB::raw('DATE_FORMAT(tanggal_pemesanan, "%Y-%m") as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->where('tanggal_pemesanan', '>=', Carbon::now()->subMonths(6))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // Pastikan semua bulan 6 bulan terakhir muncul (default 0)
        $chartLabels = [];
        $chartData   = [];
        for ($i = 5; $i >= 0; $i--) {
            $key = Carbon::now()->subMonths($i)->format('Y-m');
            $chartLabels[] = Carbon::createFromFormat('Y-m', $key)
                ->translatedFormat('M Y');
            $chartData[]   = $monthlyData[$key] ?? 0;
        }

        // ---- Pemesanan terbaru (5 transaksi terakhir) ----
        $recentBookings = Booking::with(['user', 'vehicle', 'driver'])
            ->latest()
            ->take(5)
            ->get();

        // ---- Hasil analisis K-Means terakhir (jika ada) ----
        $latestKmeans = KmeansResult::with('vehicle')
            ->latest('created_at')
            ->get()
            ->unique('vehicle_id');

        return view('dashboard.index', compact(
            'totalBookings',
            'totalKendaraan',
            'kendaraanTersedia',
            'totalDriver',
            'driverAktif',
            'bookingsPending',
            'bookingsDisetujui',
            'bookingsSelesai',
            'chartLabels',
            'chartData',
            'recentBookings',
            'latestKmeans'
        ));
    }
}