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
        Schema::create('kpi_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('metric');
            $table->decimal('min_value', 5, 2);
            $table->decimal('max_value', 5, 2)->default(100);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_thresholds');
    }
};
