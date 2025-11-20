<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'nama' => 'Admin Utama',
            'email' => 'admin@mail.com',
            'password' => Hash::make('password123'), // wajib hash
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. Admin No. 1',
            'foto_profil' => null,
            'role' => 'admin',
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nama' => 'User Tes',
            'email' => 'user@mail.com',
            'password' => Hash::make('password123'), // wajib hash
            'nomor_telepon' => '081234567890',
            'alamat' => 'Jl. User No. 1',
            'foto_profil' => null,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
