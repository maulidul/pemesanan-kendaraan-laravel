<?php

namespace Database\Seeders;

use App\Models\Driver;
use Illuminate\Database\Seeder;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            ['nama_driver' => 'Joko Susilo',     'nomor_telepon' => '081234567001', 'nomor_sim' => 'SIM A 1234567890', 'status' => 'Aktif'],
            ['nama_driver' => 'Agus Pratama',    'nomor_telepon' => '081234567002', 'nomor_sim' => 'SIM A 1234567891', 'status' => 'Aktif'],
            ['nama_driver' => 'Surya Darma',     'nomor_telepon' => '081234567003', 'nomor_sim' => 'SIM A 1234567892', 'status' => 'Aktif'],
            ['nama_driver' => 'Wahyu Pratama',   'nomor_telepon' => '081234567004', 'nomor_sim' => 'SIM A 1234567893', 'status' => 'Aktif'],
            ['nama_driver' => 'Eko Saputra',     'nomor_telepon' => '081234567005', 'nomor_sim' => 'SIM B1 1234567894','status' => 'Aktif'],
            ['nama_driver' => 'Dedi Kurniawan',  'nomor_telepon' => '081234567006', 'nomor_sim' => 'SIM A 1234567895', 'status' => 'Cuti'],
            ['nama_driver' => 'Yanto Cahyono',   'nomor_telepon' => '081234567007', 'nomor_sim' => 'SIM A 1234567896', 'status' => 'Aktif'],
            ['nama_driver' => 'Roni Iskandar',   'nomor_telepon' => '081234567008', 'nomor_sim' => 'SIM A 1234567897', 'status' => 'Tidak Aktif'],
        ];

        foreach ($drivers as $d) {
            Driver::updateOrCreate(['nomor_telepon' => $d['nomor_telepon']], $d);
        }
    }
}