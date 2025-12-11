<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $history = [
        // [
        //     'id_sop' => '4',
        //     'id_user' => '4',
        //     'id_status' => '3',
        //     'keterangan_perubahan' => 'Terdapat kesalahan penulisan judul sop',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ],
        [
            'id_sop' => '16',
            'id_user' => '14',
            'id_status' => '3',
            'keterangan_perubahan' => 'Perubahan unit terkait',
            'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id_sop' => '18',
            'id_user' => '13',
            'id_status' => '3',
            'keterangan_perubahan' => 'Terdapat kesalahan nomor SK',
            'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id_sop' => '37',
            'id_user' => '4',
            'id_status' => '3',
            'keterangan_perubahan' => 'Terdapat kesalahan tanggal pengesahan',
            'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'id_sop' => '38',
            'id_user' => '4',
            'id_status' => '3',
            'keterangan_perubahan' => 'Perubahan unit terkait',
            'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ]
        ];

        DB::table('tb_history_sop')->insert($history);
    }
}
