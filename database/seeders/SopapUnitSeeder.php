<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SopapUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sopap_unitterkait = [
        // [
        //     'id_sop' => '15',
        //     'id_unit_kerja' => '7'
        // ],
        // [
        //     'id_sop' => '15',
        //     'id_unit_kerja' => '9'
        // ],
        // [
        //     'id_sop' => '15',
        //     'id_unit_kerja' => '10'
        // ],
        // [
        //     'id_sop' => '15',
        //     'id_unit_kerja' => '11'
        // ],
        // [
        //     'id_sop' => '15',
        //     'id_unit_kerja' => '12'
        // ],
        // [
        //     'id_sop' => '16',
        //     'id_unit_kerja' => '21'
        // ],
        // [
        //     'id_sop' => '20',
        //     'id_unit_kerja' => '11'
        // ],
        // [
        //     'id_sop' => '22',
        //     'id_unit_kerja' => '9'
        // ],
        // [
        //     'id_sop' => '22',
        //     'id_unit_kerja' => '10'
        // ],
        // [
        //     'id_sop' => '22',
        //     'id_unit_kerja' => '11'
        // ],
        // [
        //     'id_sop' => '22',
        //     'id_unit_kerja' => '12'
        // ],
        [
            'id_sop' => '30',
            'id_unit_kerja' => '7'
        ],
        [
            'id_sop' => '30',
            'id_unit_kerja' => '10'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '7'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '9'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '10'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '11'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '12'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '14'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '15'
        ],
        [
            'id_sop' => '32',
            'id_unit_kerja' => '18'
        ],
        [
            'id_sop' => '35',
            'id_unit_kerja' => '3'
        ],
        [
            'id_sop' => '35',
            'id_unit_kerja' => '19'
        ],
        [
            'id_sop' => '40',
            'id_unit_kerja' => '21'
        ],
        [
            'id_sop' => '42',
            'id_unit_kerja' => '21'
        ],
        [
            'id_sop' => '43',
            'id_unit_kerja' => '9'
        ],
        [
            'id_sop' => '43',
            'id_unit_kerja' => '10'
        ],
        [
            'id_sop' => '43',
            'id_unit_kerja' => '11'
        ],
        ];

        DB::table('tb_sop_unit_terkait')->insert($sopap_unitterkait);
    }
}
