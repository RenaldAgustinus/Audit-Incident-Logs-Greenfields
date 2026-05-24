<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // 0. BERSIHKAN DATA LAMA (Mencegah duplikasi)
        DB::table('audit_trails')->delete();
        DB::table('incident_logs')->delete();
        DB::table('users')->delete();

        // 1. INSERT MASTER USERS
        DB::table('users')->insert([
            [
                'id' => 1,
                'username' => 'supervisor_admin',
                'password' => Hash::make('greenfields123'),
                'full_name' => 'Renald Agustinus', 
                'role' => 'supervisor',
                'created_at' => $now,
            ],
            [
                'id' => 2,
                'username' => 'operator_satu',
                'password' => Hash::make('greenfields123'),
                'full_name' => 'Lia Kusuma',
                'role' => 'operator',
                'created_at' => $now,
            ],
            [
                'id' => 3,
                'username' => 'operator_dua',
                'password' => Hash::make('greenfields123'),
                'full_name' => 'Bagas Prasetyo',
                'role' => 'operator',
                'created_at' => $now,
            ]
        ]);

        // 2. DATA DUMMY REALISTIS (Tanpa Faker)
        $incidents = [
            [
                'reported_by' => 2, // Lia
                'incident_title' => 'Mesin Pasteurisasi Unit 2 Overheat',
                'description' => 'Suhu mesin tiba-tiba naik melebihi ambang batas normal (mencapai 90°C). Indikator alarm merah menyala, produksi dihentikan sementara.',
                'severity_level' => 'critical',
                'status' => 'insiden_baru',
                'resolution_notes' => null,
                'resolution_photo' => null,
                'is_deleted' => false,
                'created_at' => Carbon::now()->subHours(2),
                'updated_at' => Carbon::now()->subHours(2),
            ],
            [
                'reported_by' => 3, // Bagas
                'incident_title' => 'Kebocoran Pipa Susu Tangki B',
                'description' => 'Terdapat tetesan susu cair pada sambungan pipa valve bawah dekat area filter. Area lantai menjadi licin.',
                'severity_level' => 'critical',
                'status' => 'butuh_tindak_lanjut',
                'resolution_notes' => null,
                'resolution_photo' => null,
                'is_deleted' => false,
                'created_at' => Carbon::now()->subDays(1)->setTime(10, 15),
                'updated_at' => Carbon::now()->subDays(1)->setTime(11, 00),
            ],
            [
                'reported_by' => 2, // Lia
                'incident_title' => 'Sensor Suhu Chiller 04 Error',
                'description' => 'Pembacaan suhu di layar monitor LCD tidak akurat, angka melompat-lompat.',
                'severity_level' => 'medium',
                'status' => 'menunggu_verifikasi',
                'resolution_notes' => 'Kabel sensor kendor akibat getaran kompresor, sudah di-solder ulang dan dibungkus isolator.',
                'resolution_photo' => null,
                'is_deleted' => false,
                'created_at' => Carbon::now()->subDays(2)->setTime(14, 20),
                'updated_at' => Carbon::now()->subDays(2)->setTime(16, 00),
            ],
            [
                'reported_by' => 3, // Bagas
                'incident_title' => 'Lampu Penerangan Sektor Produksi Mati',
                'description' => 'Bohlam putus di area packing line 1. Mengganggu visibilitas pekerja saat pengecekan kualitas (QC).',
                'severity_level' => 'low',
                'status' => 'selesai',
                'resolution_notes' => 'Lampu TL 40 watt sudah diganti dengan unit LED baru yang lebih terang.',
                'resolution_photo' => null,
                'is_deleted' => false,
                'created_at' => Carbon::now()->subDays(3)->setTime(8, 45),
                'updated_at' => Carbon::now()->subDays(3)->setTime(9, 30),
            ],
            [
                'reported_by' => 2, // Lia
                'incident_title' => 'Conveyor Belt Sektor C Tersendat',
                'description' => 'Motor penggerak berbunyi kasar dan putaran belt tidak stabil.',
                'severity_level' => 'medium',
                'status' => 'ditolak',
                'resolution_notes' => 'Telah diberi pelumas pada gear utama.',
                'resolution_photo' => null,
                'is_deleted' => false,
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(3),
            ],
            [
                'reported_by' => 3, // Bagas
                'incident_title' => 'Stok Cairan Sanitasi Menipis',
                'description' => 'Cairan pembersih untuk sterilisasi alat sebelum shift pagi sisa 1 jerigen. Perlu *restock* dari gudang pusat.',
                'severity_level' => null, // Sengaja dikosongkan agar UI "Belum Ditentukan" muncul
                'status' => 'insiden_baru',
                'resolution_notes' => null,
                'resolution_photo' => null,
                'is_deleted' => false,
                'created_at' => Carbon::now()->subMinutes(45),
                'updated_at' => Carbon::now()->subMinutes(45),
            ]
        ];

        DB::table('incident_logs')->insert($incidents);
    }
}