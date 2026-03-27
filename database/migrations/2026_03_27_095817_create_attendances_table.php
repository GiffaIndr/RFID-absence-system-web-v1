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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('rfid_card_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->timestamp('tap_in')->nullable();
            $table->timestamp('tap_out')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'blocked'])->default('present');
            $table->integer('work_duration')->nullable();  // dalam menit
            $table->text('notes')->nullable();
            $table->timestamps();

            // Satu karyawan hanya boleh punya 1 record absensi per hari
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
