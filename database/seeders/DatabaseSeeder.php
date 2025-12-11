<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use App\Models\TbUser;// Atau App\Models\TbRole sesuaikan nama model role kamu
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // RoleSeeder::class,
            // AkunSeeder::class,
            // DirektoratSeeder::class,
            // UnitSeeder::class,
            //SopSeeder::class,
            SopapUnitSeeder::class
        ]);
        
        // 2. Buat User Admin
        // TbUser::create([
        //     'nama_lengkap' => 'Super Admin',
        //     'username' => 'superadmin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('password'),
        //     'id_role' => 1, // Asumsi ID 1 adalah Admin
        //     'is_active' => true,
        // ]);

        // TbUser::create([
        //     'nama_lengkap' => 'Admin',
        //     'username' => 'admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('admin'),
        //     'id_role' => 1, // Asumsi ID 1 adalah Admin
        //     'is_active' => true,
        // ]);        
    }

}