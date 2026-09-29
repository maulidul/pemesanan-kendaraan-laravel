<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data dummy.
     */
    public function run(): void
    {
        // Urutan penting karena ada foreign key constraint
        $this->call([
            UserSeeder::class,
            VehicleSeeder::class,
            DriverSeeder::class,
            BookingSeeder::class,
        ]);
    }
}