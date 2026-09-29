<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed user dengan berbagai role.
     */
    public function run(): void
    {
        // Akun default sesuai requirement (maulidul@gmail.com / 12345678)
        User::updateOrCreate(
            ['email' => 'maulidul@gmail.com'],
            [
                'name'     => 'Maulidul',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );

        // Tambah akun dengan role berbeda untuk testing
        $users = [
            ['name' => 'Budi Santoso',   'email' => 'budi@dishub.go.id',     'role' => 'pemohon'],
            ['name' => 'Siti Aminah',    'email' => 'siti@dishub.go.id',     'role' => 'pemohon'],
            ['name' => 'Andi Wijaya',    'email' => 'andi@dishub.go.id',     'role' => 'pemohon'],
            ['name' => 'Dewi Lestari',   'email' => 'dewi@dishub.go.id',     'role' => 'operasional'],
            ['name' => 'Rudi Hermawan',  'email' => 'rudi@dishub.go.id',     'role' => 'operasional'],
            ['name' => 'Ir. Hendra',     'email' => 'hendra@dishub.go.id',   'role' => 'manajemen'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name'     => $user['name'],
                    'password' => Hash::make('12345678'),
                    'role'     => $user['role'],
                ]
            );
        }
    }
}