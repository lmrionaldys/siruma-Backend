<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foto_monitoring', function (Blueprint $table) {
            $table->id();

            $table->foreignId('monitoring_id')
                ->constrained('monitoring')
                ->cascadeOnDelete();

            $table->string('jenis_foto');
            $table->string('file_path');
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foto_monitoring');
    }
};