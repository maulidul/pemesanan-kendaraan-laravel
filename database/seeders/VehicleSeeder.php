<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            ['nama_kendaraan' => 'Toyota Innova Reborn',   'plat_nomor' => 'B 1001 ABC', 'jenis_kendaraan' => 'Minibus', 'kapasitas_penumpup' => 7,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Toyota Avanza Veloz',    'plat_nomor' => 'B 1002 DEF', 'jenis_kendaraan' => 'Minibus', 'kapasitas_penumpup' => 7,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Honda CR-V',             'plat_nomor' => 'B 1003 GHI', 'jenis_kendaraan' => 'SUV',     'kapasitas_penumpup' => 5,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Toyota Camry',           'plat_nomor' => 'B 1004 JKL', 'jenis_kendaraan' => 'Sedan',   'kapasitas_penumpup' => 4,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Mitsubishi Pajero',      'plat_nomor' => 'B 1005 MNO', 'jenis_kendaraan' => 'SUV',     'kapasitas_penumpup' => 7,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Isuzu Elf',              'plat_nomor' => 'B 1006 PQR', 'jenis_kendaraan' => 'Bus',     'kapasitas_penumpup' => 16, 'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Toyota Hilux Double Cabin','plat_nomor' => 'B 1007 STU','jenis_kendaraan' => 'Pick Up', 'kapasitas_penumpup' => 5,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Daihatsu Xenia',         'plat_nomor' => 'B 1008 VWX', 'jenis_kendaraan' => 'Minibus', 'kapasitas_penumpup' => 7,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Suzuki Ertiga',          'plat_nomor' => 'B 1009 YZA', 'jenis_kendaraan' => 'Minibus', 'kapasitas_penumpup' => 7,  'status_ketersediaan' => 'Tersedia'],
            ['nama_kendaraan' => 'Honda Vario 150',        'plat_nomor' => 'B 1010 BCD', 'jenis_kendaraan' => 'Motor Dinas','kapasitas_penumpup' => 2,'status_ketersediaan' => 'Tersedia'],
        ];

        foreach ($vehicles as $v) {
            Vehicle::updateOrCreate(['plat_nomor' => $v['plat_nomor']], $v);
        }
    }
}