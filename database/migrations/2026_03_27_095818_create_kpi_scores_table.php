<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kpi_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month');                    // 1-12
            $table->decimal('attendance_score', 5, 2)->default(0);   // skor kehadiran
            $table->decimal('punctuality_score', 5, 2)->default(0);  // skor ketepatan waktu
            $table->decimal('total_score', 5, 2)->default(0);        // skor akhir KPI
            $table->enum('status', ['valid', 'invalid'])->default('valid');
            $table->boolean('tap_out_allowed')->default(true);        // hasil 4.2 validasi threshold
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_scores');
    }
};
