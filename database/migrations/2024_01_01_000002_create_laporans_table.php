<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique(); // BR-2026-001

            // ---- Diisi Operator (Buat Laporan) ----
            $table->foreignId('operator_id')->constrained('users');
            $table->string('station');   // gilingan|boiler|pemurnian|penguapan
            $table->string('machine');   // mis. "Gilingan 02"
            $table->string('category');  // mekanik|elektrik|instrumentasi|proses
            $table->string('condition_text'); // Kondisi/Abnormalitas
            $table->string('urgency')->default('sedang'); // tinggi|sedang|rendah
            $table->date('incident_date');
            $table->time('incident_time');
            $table->text('description');
            $table->string('photo_before')->nullable();

            // ---- Status alur ----
            $table->string('status')->default('menunggu_validasi');

            // ---- Diisi Manager saat Validasi Laporan ----
            $table->text('rejection_reason')->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->timestamp('validated_at')->nullable();

            // ---- Diisi Manager saat Penugasan Teknisi ----
            $table->foreignId('technician_id')->nullable()->constrained('users');
            $table->string('assignment_priority')->nullable(); // tinggi|sedang|rendah
            $table->text('assignment_note')->nullable();
            $table->string('work_start_time', 5)->nullable(); // "10:05"
            $table->timestamp('assigned_at')->nullable();

            // ---- Diisi Teknisi ----
            $table->timestamp('started_at')->nullable();
            $table->text('inspection_result')->nullable();  // Hasil Pemeriksaan
            $table->string('root_cause')->nullable();        // Penyebab Kerusakan
            $table->string('action_taken')->nullable();      // Tindakan yang Dilakukan
            $table->text('components_text')->nullable();     // Komponen Diganti/Diperiksa
            $table->string('work_end_time', 5)->nullable();  // "11:40"
            $table->text('additional_note')->nullable();
            $table->string('photo_after')->nullable();
            $table->timestamp('submitted_for_validation_at')->nullable();

            // ---- Diisi Manager saat Validasi Akhir ----
            $table->text('final_manager_note')->nullable();
            $table->timestamp('final_validated_at')->nullable();

            $table->timestamps();

            $table->index(['status']);
            $table->index(['station']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
