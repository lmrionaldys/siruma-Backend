<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monitoring', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->constrained('unit')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->date('tanggal_monitoring');

            $table->decimal('progress', 5, 2)->default(0);

            $table->string('kondisi');

            $table->text('keterangan')->nullable();

            $table->string('status')->default('DRAFT');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monitoring');
    }
};