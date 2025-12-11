<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $akun = [
            [
                'id_user' => '1',
                //'id_direktorat' => '',
                'id_role' => '1', //admin
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'nama_lengkap' => 'Admin',
                'password' => 'admin',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '16',
                //'id_direktorat' => '',
                'id_role' => '1', //admin
                'username' => 'citra',
                'email' => 'citra@ngoerah.id',
                'nama_lengkap' => 'Citra Dewi',
                'password' => 'citra',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '2',
                //'id_direktorat' => '',
                'id_role' => '2', //verifikator
                'username' => 'nia',
                'email' => 'nia@ngoerah.id',
                'nama_lengkap' => 'Nia Rachman',
                'password' => 'nia',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '15',
                //'id_direktorat' => '',
                'id_role' => '2', //verifikator
                'username' => 'herman',
                'email' => 'herman@ngoerah.id',
                'nama_lengkap' => 'Herman Darmawan',
                'password' => 'herman',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '3',
                //'id_direktorat' => '',
                'id_role' => '3', //viewer
                'username' => 'vina',
                'email' => 'citra@ngoerah.id',
                'nama_lengkap' => 'Citra Dewi',
                'password' => 'citra',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '17',
                //'id_direktorat' => '',
                'id_role' => '3', //viewer
                'username' => 'belinda',
                'email' => 'belinda@ngoerah.id',
                'nama_lengkap' => 'Belinda Putri',
                'password' => 'belinda',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '4',
                'id_direktorat' => '1',
                'id_role' => '4', //pengusul
                'username' => 'bagus',
                'email' => 'bagus@gmail.com',
                'nama_lengkap' => 'Bagus',
                'password' => 'bagus',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '14',
                'id_direktorat' => '1',
                'id_role' => '4', //pengusul
                'username' => 'laras_umum',
                'email' => 'laras@ngoerah.id',
                'nama_lengkap' => 'Larasati',
                'password' => 'laras',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '18',
                'id_direktorat' => '1',
                'id_role' => '4', //pengusul
                'username' => 'anna',
                'email' => 'anna@ngoerah.id',
                'nama_lengkap' => 'Joanna',
                'password' => 'joanna',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '6',
                'id_direktorat' => '2',
                'id_role' => '4', //pengusul
                'username' => 'gede_igd',
                'email' => 'gede@ngoerah.id',
                'nama_lengkap' => 'Gede Wibowo',
                'password' => 'gede',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '7',
                'id_direktorat' => '2',
                'id_role' => '4', //pengusul
                'username' => 'ratna_rj',
                'email' => 'ratna@ngoerah.id',
                'nama_lengkap' => 'Ratna Dewi',
                'password' => 'ratna',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '8',
                'id_direktorat' => '2',
                'id_role' => '4', //pengusul
                'username' => 'ketut_ri',
                'email' => 'ketut@ngoerah.id',
                'nama_lengkap' => 'Ketut Wirawan',
                'password' => 'ketut',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '9',
                'id_direktorat' => '2',
                'id_role' => '4', //pengusul
                'username' => 'komang_icu',
                'email' => 'komang@ngoerah.id',
                'nama_lengkap' => 'Komang Ratih',
                'password' => 'komang',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '10',
                'id_direktorat' => '2',
                'id_role' => '4', //pengusul
                'username' => 'sinta_lab',
                'email' => 'sinta@ngoerah.id',
                'nama_lengkap' => 'Sinta Dewi',
                'password' => 'sinta',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '11',
                'id_direktorat' => '2',
                'id_role' => '4', //pengusul
                'username' => 'ayu_farm',
                'email' => 'ayu@ngoerah.id',
                'nama_lengkap' => 'Ayu Lestari',
                'password' => 'ayu',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '13',
                'id_direktorat' => '3',
                'id_role' => '4', //pengusul
                'username' => 'erlangga_keu',
                'email' => 'erlangga@ngoerah.id',
                'nama_lengkap' => 'Erlangga',
                'password' => 'erlangga',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_user' => '12',
                'id_direktorat' => '4',
                'id_role' => '4', //pengusul
                'username' => 'ratih_diklit',
                'email' => 'ratih@ngoerah.id',
                'nama_lengkap' => 'Ratih Kumala',
                'password' => 'ratih',
                'is_active' => '1',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('tb_user')->insert($akun);
    }
}
