<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unit = [
        [
            'id_direktorat' => '1',
            'kode_unit_kerja' => '101',
            'nama_unit' => 'Bagian Umum dan Rumah Tangga',
            'email_unit' => 'umum@ngoerah.id',
            'no_telp' => '(021) 10-101',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '1',
            'kode_unit_kerja' => '102',
            'nama_unit' => 'Instalasi Pemeliharaan Sarana dan Prasarana (IPSRS)',
            'email_unit' => 'ipsrs@ngoerah.id',
            'no_telp' => '(021) 10-102',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '1',
            'kode_unit_kerja' => '103',
            'nama_unit' => 'Instalasi Sistem Informasi Manajemen Rumah Sakit (SIMRS)',
            'email_unit' => 'simrs@ngoerah.id',
            'no_telp' => '(021) 10-103',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '1',
            'kode_unit_kerja' => '104',
            'nama_unit' => 'Unit Layanan Pengadaan (ULP)',
            'email_unit' => 'ulp@ngoerah.id',
            'no_telp' => '(021) 10-104',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '1',
            'kode_unit_kerja' => '105',
            'nama_unit' => 'Instalasi Sanitasi dan Lingkungan',
            'email_unit' => 'sanitasi@ngoerah.id',
            'no_telp' => '(021) 10-105',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '201',
            'nama_unit' => 'Instalasi Gawat Darurat',
            'email_unit' => 'igd@ngoerah.id',
            'no_telp' => '(021) 10-201',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '202',
            'nama_unit' => 'Instalasi Rawat Jalan',
            'email_unit' => 'irja@ngoerah.id',
            'no_telp' => '(021) 10-202',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '203',
            'nama_unit' => 'Instalasi Rawat Inap',
            'email_unit' => 'irna@ngoerah.id',
            'no_telp' => '(021) 10-203',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '204',
            'nama_unit' => 'Instalasi Intensive Care Unit',
            'email_unit' => 'icu@ngoerah.id',
            'no_telp' => '(021) 10-204',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '205',
            'nama_unit' => 'Instalasi Bedah Sentral',
            'email_unit' => 'ibs@ngoerah.id',
            'no_telp' => '(021) 10-205',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '206',
            'nama_unit' => 'Instalasi Pelayanan Jantung Terpadu',
            'email_unit' => 'ipjt@ngoerah.id',
            'no_telp' => '(021) 10-206',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '207',
            'nama_unit' => 'Instalasi Farmasi',
            'email_unit' => 'farmasi@ngoerah.id',
            'no_telp' => '(021) 10-207',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '208',
            'nama_unit' => 'Instalasi Laboratorium',
            'email_unit' => 'lab@ngoerah.id',
            'no_telp' => '(021) 10-208',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '209',
            'nama_unit' => 'Instalasi Radiologi, Imaging, dan Interventional',
            'email_unit' => 'radiologi@ngoerah.id',
            'no_telp' => '(021) 10-209',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '210',
            'nama_unit' => 'Instalasi Kedokteran Forensik dan Pemulasaran Jenazah',
            'email_unit' => 'forensik@ngoerah.id',
            'no_telp' => '(021) 10-210',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '211',
            'nama_unit' => 'Instalasi Gizi',
            'email_unit' => 'gizi@ngoerah.id',
            'no_telp' => '(021) 10-211',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '2',
            'kode_unit_kerja' => '212',
            'nama_unit' => 'Instalasi Rehabilitasi Medik',
            'email_unit' => 'rehab@ngoerah.id',
            'no_telp' => '(021) 10-212',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '3',
            'kode_unit_kerja' => '301',
            'nama_unit' => 'Bagian Perencanaan dan Anggaran',
            'email_unit' => 'peren@ngoerah.id',
            'no_telp' => '(021) 10-301',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '3',
            'kode_unit_kerja' => '302',
            'nama_unit' => 'Bagian Keuangan dan Akuntansi',
            'email_unit' => 'keuangan@ngoerah.id',
            'no_telp' => '(021) 10-302',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '4',
            'kode_unit_kerja' => '401',
            'nama_unit' => 'Bagian Sumber Daya Manusia',
            'email_unit' => 'sdm@ngoerah.id',
            'no_telp' => '(021) 10-401',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'id_direktorat' => '4',
            'kode_unit_kerja' => '402',
            'nama_unit' => 'Bagian Pendidikan dan Penelitian',
            'email_unit' => 'diklit@ngoerah.id',
            'no_telp' => '(021) 10-402',
            'created_at' => now(),
            'updated_at' => now()
        ]
        ];

        // DB::table('tb_unit_kerja')->insert($unit);
    }
}
