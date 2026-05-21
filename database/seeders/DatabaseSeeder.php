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

        // 1. Insert Users (Hardcoded agar gampang buat login)
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
                'severity_level' => $faker->randomElement(['low', 'medium']),
                'status' => $faker->randomElement(['open', 'investigating', 'resolved']),
                'is_deleted' => false,
                'created_at' => $now->subHours(rand(1, 100)),
                'updated_at' => $now,
            ];
        }
        
        // 3. Insert 3 CRITICAL Logs (Bintang Utamanya untuk Dashboard)
        $incidents[] = [
            'reported_by' => 2,
            'incident_title' => 'Kebocoran Pipa Utama Tangki A',
            'description' => 'Terjadi penurunan tekanan drastis pada pipa distribusi susu di Sektor A.',
            'severity_level' => 'critical',
            'status' => 'open',
            'is_deleted' => false,
            'created_at' => Carbon::now()->subMinutes(15),
            'updated_at' => Carbon::now(),
        ];
        $incidents[] = [
            'reported_by' => 3,
            'incident_title' => 'Sistem Pendingin Chiller 04 Mati',
            'description' => 'Suhu pada chiller penyimpanan naik melebihi batas standar operasional.',
            'severity_level' => 'critical',
            'status' => 'open',
            'is_deleted' => false,
            'created_at' => Carbon::now()->subMinutes(45),
            'updated_at' => Carbon::now(),
        ];
        $incidents[] = [
            'reported_by' => 2,
            'incident_title' => 'Panel Listrik Sektor C Terbakar',
            'description' => 'Korsleting pada panel utama menyebabkan *shutdown* sebagian lini produksi.',
            'severity_level' => 'critical',
            'status' => 'investigating',
            'is_deleted' => false,
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now(),
        ];

        DB::table('incident_logs')->insert($incidents);

        // 4. Insert Audit Trails Dummy
        DB::table('audit_trails')->insert([
            [
                'incident_id' => 52, // Asumsi ID Critical ke-2
                'user_id' => 1, // Diubah oleh Supervisor
                'action' => 'STATUS_UPDATED',
                'old_value' => 'open',
                'new_value' => 'investigating',
                'created_at' => Carbon::now()->subMinutes(10),
            ],
            [
                'incident_id' => 1,
                'user_id' => 2,
                'action' => 'CREATED',
                'old_value' => null,
                'new_value' => 'Log insiden baru dibuat',
                'created_at' => Carbon::now()->subHours(90),
            ]
        ]);
    }
}