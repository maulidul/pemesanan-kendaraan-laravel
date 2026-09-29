<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookingSeeder extends Seeder
{
    /**
     * Seed minimal 25 transaksi pemesanan untuk pengujian analisis K-Means.
     * Pola dibuat bervariasi agar cluster terbentuk dengan baik:
     - Kendaraan 1 & 2 → Frekuensi tinggi (15+ kali)
     - Kendaraan 3 & 4 → Frekuensi sedang (7-10 kali)
     - Kendaraan 5-10  → Frekuensi rendah (1-3 kali)
     */
    public function run(): void
    {
        DB::table('bookings')->truncate();

        $pemohon     = User::where('role', 'pemohon')->get();
        $operasional = User::where('role', 'operasional')->get();
        $allUsers    = User::all();

        $vehicles    = Vehicle::all();
        $drivers     = Driver::where('status', 'Aktif')->get();

        if ($vehicles->isEmpty()) {
            $this->command->warn('Tidak ada kendaraan. Jalankan VehicleSeeder terlebih dahulu.');
            return;
        }

        // Distribusi pemesanan per kendaraan (untuk variasi cluster)
        // Indeks 0-1 = tinggi, 2-3 = sedang, 4-9 = rendah
        $distribution = [
            // Vehicle index : jumlah pemesanan
            0 => 8,   // Tinggi
            1 => 7,   // Tinggi
            2 => 5,   // Sedang
            3 => 4,   // Sedang
            4 => 3,   // Rendah
            5 => 2,   // Rendah
            6 => 2,   // Rendah
            7 => 1,   // Rendah
            8 => 1,   // Rendah
            9 => 1,   // Rendah
        ];

        $tujuans = [
            'Bandung - Jakarta PP', 'Kunjungan ke Kementerian', 'Survey lokasi proyek',
            'Rapat koordinasi', 'Pengiriman dokumen', 'Monitoring lapangan',
            'Kunjungan kerja ke daerah', 'Studi banding', 'Pelatihan SDM',
            'Verifikasi data', 'Inspeksi mendadak', 'Koordinasi lintas SKPD',
        ];

        $keperluans = [
            'Menghadiri rapat koordinasi tingkat Kementerian',
            'Studi banding sistem pelayanan publik',
            'Monitoring pelaksanaan kegiatan di lapangan',
            'Verifikasi data usulan kegiatan tahun anggaran',
            'Kunjungan kerja dalam rangka koordinasi lintas sektoral',
            'Pengiriman laporan bulanan ke kantor pusat',
            'Survey lokasi rencana pembangunan fasilitas umum',
            'Pelatihan peningkatan kapasitas SDM',
            'Koordinasi persiapan acara resmi daerah',
            'Audit internal pengelolaan kendaraan dinas',
        ];

        $statuses = ['pending', 'disetujui', 'disetujui', 'disetujui', 'selesai', 'selesai', 'ditolak'];

        $bookingCounter = 0;
        $startDate = Carbon::now()->subMonths(4);

        foreach ($distribution as $vehicleIndex => $count) {
            if (!isset($vehicles[$vehicleIndex])) continue;

            $vehicle = $vehicles[$vehicleIndex];

            for ($i = 0; $i < $count; $i++) {
                $bookingCounter++;

                // Random tanggal dalam 4 bulan terakhir
                $tanggalPemesanan = $startDate->copy()->addDays(rand(0, 120));
                $durasiHari       = rand(1, 5);
                $tanggalMulai     = $tanggalPemesanan->copy()->addDays(rand(1, 7));
                $tanggalSelesai   = $tanggalMulai->copy()->addDays($durasiHari - 1);

                $status = $statuses[array_rand($statuses)];

                Booking::create([
                    'user_id'             => $pemohon->isNotEmpty() ? $pemohon->random()->id : $allUsers->random()->id,
                    'vehicle_id'          => $vehicle->id,
                    'driver_id'           => $drivers->isNotEmpty() ? $drivers->random()->id : null,
                    'tanggal_pemesanan'   => $tanggalPemesanan->format('Y-m-d'),
                    'tanggal_mulai'       => $tanggalMulai->format('Y-m-d'),
                    'tanggal_selesai'     => $tanggalSelesai->format('Y-m-d'),
                    'tujuan'              => $tujuans[array_rand($tujuans)],
                    'keperluan'           => $keperluans[array_rand($keperluans)],
                    'jumlah_penumpup'     => rand(1, 6),
                    'status_persetujuan'  => $status,
                    'catatan_persetujuan' => $status === 'ditolak' ? 'Benturan jadwal dengan kegiatan lain' : null,
                    'tanggal_diseputujui' => $status !== 'pending' ? now() : null,
                ]);
            }
        }

        $this->command->info("Berhasil membuat {$bookingCounter} transaksi pemesanan dummy.");
    }
}