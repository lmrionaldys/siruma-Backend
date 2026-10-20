<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitoring_id')
                ->constrained('monitoring')
                ->cascadeOnDelete();

            $table->decimal('luas_tanah', 10, 2);
            $table->decimal('luas_bangunan', 10, 2);

            $table->decimal('nilai_tanah_per_m2', 15, 2);
            $table->decimal('nilai_bangunan_per_m2', 15, 2);

            $table->decimal('nilai_total_tanah', 15, 2);
            $table->decimal('nilai_total_bangunan', 15, 2);

            $table->decimal('total_nilai', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penilaian');
    }
};