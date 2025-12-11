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


        $data_sop = [
        // [
        //     'id_user' => '6',
        //     'id_status' => '5',
        //     'nomor_sop' => 'SK/RS/DIR/III/2022/002',
        //     'judul_sop' => 'SOP Hand Hygiene (Cuci Tangan)',
        //     'deskripsi' => 'Prosedur wajib untuk mencuci tangan sesuai 5 momen hand hygiene (sebelum/sesudah kontak pasien, sebelum tindakan aseptik, dsb.).',
        //     'kategori_sop' => 'SOP AP',
        //     'is_all_units' => '1',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2022-03-10 00:00:00',
        //     'tgl_berlaku' => '2022-03-13 00:00:00',
        //     'tgl_kadaluwarsa' => '2025-03-13 00:00:00',
        //     'tgl_review_tahunan' => '2023-03-13 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '7'
        // ],
        // [
        //     'id_user' => '8',
        //     'id_status' => '1',
        //     'nomor_sop' => 'SK/RS/DIR/I/2024/005',
        //     'judul_sop' => 'SOP Penerimaan Pasien Baru Rawat Inap',
        //     'deskripsi' => 'Prosedur admission pasien, mulai dari verifikasi identitas, orientasi ruangan, hingga asesmen awal keperawatan.).',
        //     'kategori_sop' => 'SOP Internal',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2024-01-21 00:00:00',
        //     'tgl_berlaku' => '2024-01-25 00:00:00',
        //     'tgl_kadaluwarsa' => '2027-01-25 00:00:00',
        //     'tgl_review_tahunan' => '2025-01-25 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '9'
        // ],
        // [
        //     'id_user' => '7',
        //     'id_status' => '2',
        //     'nomor_sop' => 'SK/RS/DIR/VI/2025/004',
        //     'judul_sop' => 'SOP Pemberian Transfusi Darah',
        //     'deskripsi' => 'Prosedur pemeriksaan kecocokan, persiapan pasien, pemantauan, dan dokumentasi selama pemberian transfusi darah.).',
        //     'kategori_sop' => 'SOP AP',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2025-06-01 00:00:00',
        //     'tgl_berlaku' => '2025-06-10 00:00:00',
        //     'tgl_kadaluwarsa' => '2028-06-10 00:00:00',
        //     'tgl_review_tahunan' => '2026-06-10 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '8'
        // ],
        // [
        //     'id_user' => '14',
        //     'id_status' => '3',
        //     'nomor_sop' => 'SK/RS/SDM/V/2025/018',
        //     'judul_sop' => 'SOP Pengajuan Cuti Karyawan',
        //     'deskripsi' => 'Prosedur pengajuan, persetujuan, dan pencatatan semua jenis cuti karyawan (tahunan, sakit, besar, dll.)).',
        //     'kategori_sop' => 'SOP AP',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2025-05-17 00:00:00',
        //     'tgl_berlaku' => '2025-05-19 00:00:00',
        //     'tgl_kadaluwarsa' => '2028-05-19 00:00:00',
        //     'tgl_review_tahunan' => '2026-05-19 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '2'
        // ],
        // [
        //     'id_user' => '12',
        //     'id_status' => '5',
        //     'nomor_sop' => 'SK/RS/SDM/IV/2022/022',
        //     'judul_sop' => 'SOP Prosedur Pelatihan Karyawan Baru (Onboarding)',
        //     'deskripsi' => 'Prosedur orientasi dan pelatihan wajib (K3, mutu, service excellence) bagi seluruh karyawan yang baru bergabung.',
        //     'kategori_sop' => 'SOP AP',
        //     'is_all_units' => '1',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2022-04-18 00:00:00',
        //     'tgl_berlaku' => '2022-04-22 00:00:00',
        //     'tgl_kadaluwarsa' => '2025-04-22 00:00:00',
        //     'tgl_review_tahunan' => '2023-04-22 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '22'
        // ],
        // [
        //     'id_user' => '13',
        //     'id_status' => '3',
        //     'nomor_sop' => 'SK/RS/ADM/IX/2025/016',
        //     'judul_sop' => 'SOP Klaim Asuransi Pasien',
        //     'deskripsi' => 'Prosedur lengkap untuk verifikasi keanggotaan, pengajuan klaim, dan follow-up pembayaran dari pihak asuransi/BPJS.',
        //     'kategori_sop' => 'SOP Internal',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2025-09-17 00:00:00',
        //     'tgl_berlaku' => '2025-09-19 00:00:00',
        //     'tgl_kadaluwarsa' => '2028-09-19 00:00:00',
        //     'tgl_review_tahunan' => '2026-09-19 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '20'
        // ],
        // [
        //     'id_user' => '10',
        //     'id_status' => '2',
        //     'nomor_sop' => 'SK/RS/DIR/X/2024/009',
        //     'judul_sop' => 'SOP Pengambilan Spesimen Darah Vena',
        //     'deskripsi' => 'Prosedur pengambilan sampel darah vena, termasuk persiapan alat, identifikasi pasien, dan labelisasi spesimen yang benar.',
        //     'kategori_sop' => 'SOP Internal',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2024-10-17 00:00:00',
        //     'tgl_berlaku' => '2024-10-27 00:00:00',
        //     'tgl_kadaluwarsa' => '2027-10-27 00:00:00',
        //     'tgl_review_tahunan' => '2025-10-27 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '14'
        // ],
        // [
        //     'id_user' => '9',
        //     'id_status' => '2',
        //     'nomor_sop' => 'SK/RS/DIR/XI/2024/012',
        //     'judul_sop' => 'SOP Penggunaan Alat Pelindung Diri (APD)',
        //     'deskripsi' => 'Prosedur pemilihan, cara penggunaan (donning), dan pelepasan (doffing) APD (masker, sarung tangan, gaun) yang tepat.',
        //     'kategori_sop' => 'SOP AP',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2024-12-20 00:00:00',
        //     'tgl_berlaku' => '2024-12-23 00:00:00',
        //     'tgl_kadaluwarsa' => '2027-12-23 00:00:00',
        //     'tgl_review_tahunan' => '2025-12-23 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '10'
        // ],
        // [
        //     'id_user' => '18',
        //     'id_status' => '4',
        //     'nomor_sop' => 'SK/RS/KMN/VI/2023/025',
        //     'judul_sop' => 'SOP Kebersihan dan Sanitasi Ruang Rawat Inap',
        //     'deskripsi' => 'Prosedur pembersihan lantai, perabotan, dan kamar mandi pasien secara rutin dan sesuai jadwal (pagi, sore).',
        //     'kategori_sop' => 'SOP Internal',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2023-06-20 00:00:00',
        //     'tgl_berlaku' => '2023-06-23 00:00:00',
        //     'tgl_kadaluwarsa' => '2026-06-23 00:00:00',
        //     'tgl_review_tahunan' => '2024-06-23 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '6'
        // ],
        // [
        //     'id_user' => '6',
        //     'id_status' => '4',
        //     'nomor_sop' => 'SK/RS/DIR/XI/2025/006',
        //     'judul_sop' => 'SOP Resusitasi Jantung Paru (RJP/Bantuan Hidup Dasar)',
        //     'deskripsi' => 'Prosedur segera yang harus dilakukan saat pasien mengalami henti jantung/henti napas (Dewasa, Anak, dan Bayi).',
        //     'kategori_sop' => 'SOP AP',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2025-11-11 00:00:00',
        //     'tgl_berlaku' => '2025-11-12 00:00:00',
        //     'tgl_kadaluwarsa' => '2028-11-12 00:00:00',
        //     'tgl_review_tahunan' => '2026-11-12 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '7'
        // ],
        // [
        //     'id_user' => '18',
        //     'id_status' => '1',
        //     'nomor_sop' => 'SK/RS/DIR/V/2024/013',
        //     'judul_sop' => 'SOP Sterilisasi Alat Medis',
        //     'deskripsi' => 'Prosedur pencucian, pengemasan, sterilisasi, dan penyimpanan alat medis/bedah di unit CSSD.',
        //     'kategori_sop' => 'SOP Internal',
        //     'is_all_units' => '0',
        //     'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
        //     'tgl_pengesahan' => '2024-05-13 00:00:00',
        //     'tgl_berlaku' => '2024-05-23 00:00:00',
        //     'tgl_kadaluwarsa' => '2027-05-23 00:00:00',
        //     'tgl_review_tahunan' => '2025-05-23 00:00:00',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        //     'id_unit_kerja' => '6'
        // ],
        [
            'id_user' => '18',
            'id_status' => '1',
            'nomor_sop' => 'SK/RS/DIR/V/2024/013',
            'judul_sop' => 'SOP Sterilisasi Alat Medis',
            'deskripsi' => 'Prosedur pencucian, pengemasan, sterilisasi, dan penyimpanan alat medis/bedah di unit CSSD.',
            'kategori_sop' => 'SOP Internal',
            'is_all_units' => '0',
            'dokumen_path' => 'sop-documents/01KC0XD5XZCPYN62RRP7S3XCXX.pdf',
            'tgl_pengesahan' => '2024-05-13 00:00:00',
            'tgl_berlaku' => '2024-05-23 00:00:00',
            'tgl_kadaluwarsa' => '2027-05-23 00:00:00',
            'tgl_review_tahunan' => '2025-05-23 00:00:00',
            'created_at' => now(),
            'updated_at' => now(),
            'id_unit_kerja' => '6'
        ]
        ];

        //DB::table('tb_sop')->insert($data_sop);

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
        [
            'id_sop' => '22',
            'id_unit_kerja' => '9'
        ],
        [
            'id_sop' => '22',
            'id_unit_kerja' => '10'
        ],
        [
            'id_sop' => '22',
            'id_unit_kerja' => '11'
        ],
        [
            'id_sop' => '22',
            'id_unit_kerja' => '12'
        ]
        ];

        DB::table('tb_sop_unit_terkait')->insert($sopap_unitterkait);
   
    }
}