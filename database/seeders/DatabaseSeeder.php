<?php

namespace Database\Seeders;

use App\Models\TbUser;
use App\Models\Role; // Atau App\Models\TbRole sesuaikan nama model role kamu
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        

        // 2. Buat User Admin
        TbUser::create([
            'nama_lengkap' => 'Super Admin',
            'username' => 'superadmin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'id_role' => 1, // Asumsi ID 1 adalah Admin
            'is_active' => true,
        ]);
    }
}