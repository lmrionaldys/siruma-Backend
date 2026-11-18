<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blok', function (Blueprint $table) {
            $table->unique(
                ['perumahan_id', 'nama_blok'],
                'blok_perumahan_nama_unique'
            );
        });

        Schema::table('unit', function (Blueprint $table) {
            $table->unique(
                ['blok_id', 'nomor_unit'],
                'unit_blok_nomor_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('blok', function (Blueprint $table) {
            $table->dropUnique('blok_perumahan_nama_unique');
        });

        Schema::table('unit', function (Blueprint $table) {
            $table->dropUnique('unit_blok_nomor_unique');
        });
    }
};
