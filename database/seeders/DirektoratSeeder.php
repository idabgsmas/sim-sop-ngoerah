<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DirektoratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $direktorat = [[
            'kode_direktorat' => '01',
            'nama_direktorat' => 'Layanan Operasional',
            'email_direktorat' => 'layop@ngoerah.id',
            'no_telp' => '(021) 10-12121',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'kode_direktorat' => '02',
            'nama_direktorat' => 'Medik dan Keperawatan',
            'email_direktorat' => 'medkep@gnoerah.id',
            'no_telp' => '(021) 10-34343',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'kode_direktorat' => '03',
            'nama_direktorat' => 'Perencanaan dan Keuangan',
            'email_direktorat' => 'perkeu@ngoerah.id',
            'no_telp' => '(021) 10-98765',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'kode_direktorat' => '04',
            'nama_direktorat' => 'Sumber Daya Manusia, Pendidikan, dan Pelatihan',
            'email_direktorat' => 'sdmpp@ngoerah.id',
            'no_telp' => '(021) 10-56789',
            'created_at' => now(),
            'updated_at' => now()
        ]];

        // DB::table('tb_direktorat')->insert($direktorat);
    }
}
