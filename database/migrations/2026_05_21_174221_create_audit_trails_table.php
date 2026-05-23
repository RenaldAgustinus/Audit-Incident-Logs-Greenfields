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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_id')->constrained('incident_logs')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('action', 50);
            
            // TAMBAHAN WAJIB: Kolom description untuk menyimpan keterangan log
            $table->string('description'); 
            
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            
            // Ganti jadi timestamps() biar otomatis bikin created_at dan updated_at
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};