<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id();

            $table->foreignId('blok_id')
                ->constrained('blok')
                ->cascadeOnDelete();

            $table->string('nomor_unit');

            $table->decimal('luas_tanah', 10, 2);
            $table->decimal('luas_bangunan', 10, 2);

            $table->decimal('nilai_tanah_per_m2', 15, 2);
            $table->decimal('nilai_bangunan_per_m2', 15, 2);

            $table->string('status')->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit');
    }
};