<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = [
        [
            'id_role' => '1',
            'nama_role' => 'Administrator',
            'deskripsi_role' => 'Mengatur data direktorat, unit, akun, dan role'
        ],
        [
            'id_role' => '2',
            'nama_role' => 'Verifikator',
            'deskripsi_role' => 'Melakukan verifikasi dan/atau revisi data SOP yang diajukan'
        ],
        [
            'id_role' => '3',
            'nama_role' => 'Viewer',
            'deskripsi_role' => 'Melihat data SOP yang ada'
        ],
        [
            'id_role' => '4',
            'nama_role' => 'Pengusul',
            'deskripsi_role' => 'Mengatur SOP yang ada pada unitnya masing-masing'
        ]
        ];
    }
}
