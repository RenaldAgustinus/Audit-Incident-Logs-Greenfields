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
            $table->enum('severity_level', ['low', 'medium', 'critical']);
            $table->enum('status', ['open', 'investigating', 'resolved'])->default('open');
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
