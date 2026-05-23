<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $now = Carbon::now();

        // 1. Insert Master Users
        DB::table('users')->insert([
            [
                'username' => 'supervisor_admin',
                'password' => Hash::make('greenfields123'),
                'full_name' => 'Renald Agustinus', 
                'role' => 'supervisor',
                'created_at' => $now,
            ],
            [
                'username' => 'operator_satu',
                'password' => Hash::make('greenfields123'),
                'full_name' => 'Lia Kusuma',
                'role' => 'operator',
                'created_at' => $now,
            ],
            [
                'username' => 'operator_dua',
                'password' => Hash::make('greenfields123'),
                'full_name' => 'Bagas Prasetyo',
                'role' => 'operator',
                'created_at' => $now,
            ]
        ]);

        // 2. Insert 50 Normal/Medium Logs via Faker
        $incidents = [];
        for ($i = 0; $i < 50; $i++) {
            $incidents[] = [
                'reported_by' => $faker->randomElement([2, 3]), // Operator 1 atau 2
                'incident_title' => $faker->sentence(4),
                'description' => $faker->paragraph(),
                // Sesuai revisi, ada kemungkinan severity belum diset
                'severity_level' => $faker->randomElement(['low', 'medium', null]), 
                
                // PERBAIKAN 1: Gunakan status versi baru!
                'status' => $faker->randomElement([
                    'insiden_baru', 
                    'butuh_tindak_lanjut', 
                    'menunggu_verifikasi', 
                    'selesai', 
                    'ditolak'
                ]),
                
                'is_deleted' => false,
                'created_at' => $now->copy()->subHours(rand(1, 100)),
                'updated_at' => $now,
            ];
        }
        
        // 3. Insert 3 CRITICAL Logs (Bintang Utamanya untuk Dashboard)
        $incidents[] = [
            'reported_by' => 2,
            'incident_title' => 'Kebocoran Pipa Utama Tangki A',
            'description' => 'Terjadi penurunan tekanan drastis pada pipa distribusi susu di Sektor A.',
            'severity_level' => 'critical',
            'status' => 'insiden_baru', // Diubah ke status baru
            'is_deleted' => false,
            'created_at' => Carbon::now()->subMinutes(15),
            'updated_at' => Carbon::now(),
        ];
        $incidents[] = [
            'reported_by' => 3,
            'incident_title' => 'Sistem Pendingin Chiller 04 Mati',
            'description' => 'Suhu pada chiller penyimpanan naik melebihi batas standar operasional.',
            'severity_level' => 'critical',
            'status' => 'insiden_baru', // Diubah ke status baru
            'is_deleted' => false,
            'created_at' => Carbon::now()->subMinutes(45),
            'updated_at' => Carbon::now(),
        ];
        $incidents[] = [
            'reported_by' => 2,
            'incident_title' => 'Panel Listrik Sektor C Terbakar',
            'description' => 'Korsleting pada panel utama menyebabkan *shutdown* sebagian lini produksi.',
            'severity_level' => 'critical',
            'status' => 'butuh_tindak_lanjut', // Diubah ke status baru
            'is_deleted' => false,
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now(),
        ];

        DB::table('incident_logs')->insert($incidents);

        // 4. Insert Audit Trails Dummy
        DB::table('audit_trails')->insert([
            [
                'incident_id' => 53, // Asumsi ID Critical
                'user_id' => 1, // Diubah oleh Supervisor
                'action' => 'UPDATE_SEVERITY',
                
                // PERBAIKAN 2: Tambahkan kolom description yang wajib ada!
                'description' => 'Supervisor menetapkan tingkat severity menjadi CRITICAL',
                
                'old_value' => 'insiden_baru',
                'new_value' => 'butuh_tindak_lanjut',
                'created_at' => Carbon::now()->subMinutes(10),
                'updated_at' => Carbon::now(),
            ],
            [
                'incident_id' => 1,
                'user_id' => 2,
                'action' => 'CREATE',
                
                // PERBAIKAN 2: Tambahkan kolom description
                'description' => 'Operator menambah insiden baru',
                
                'old_value' => null,
                'new_value' => 'Data insiden dibuat',
                'created_at' => Carbon::now()->subHours(90),
                'updated_at' => Carbon::now(),
            ]
        ]);
    }
}