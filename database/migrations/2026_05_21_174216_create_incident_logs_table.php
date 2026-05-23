<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('incident_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reported_by')->constrained('users')->onDelete('cascade');
            $table->string('incident_title', 150);
            $table->text('description');
            
            // PERUBAHAN 1: Ditambah .nullable() karena Operator tidak ngisi ini di awal
            $table->enum('severity_level', ['low', 'medium', 'critical'])->nullable();
            
            // PERUBAHAN 2: Status diganti total sesuai revisi SOP terbaru
            $table->enum('status', [
                'insiden_baru', 
                'butuh_tindak_lanjut', 
                'menunggu_verifikasi', 
                'selesai', 
                'ditolak'
            ])->default('insiden_baru');

            // PERUBAHAN 3: Dua kolom baru untuk menampung "Laporan & Foto Tindak Lanjut" dari Operator
            $table->text('resolution_notes')->nullable();
            $table->string('resolution_photo')->nullable();
            
            $table->boolean('is_deleted')->default(false);
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_logs');
    }
};