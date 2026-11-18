<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('approval', function (Blueprint $table) {
            $table->unique(
                'monitoring_id',
                'approval_monitoring_id_unique'
            );
        });

        Schema::table('penilaian', function (Blueprint $table) {
            $table->unique(
                'monitoring_id',
                'penilaian_monitoring_id_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('approval', function (Blueprint $table) {
            $table->dropUnique('approval_monitoring_id_unique');
        });

        Schema::table('penilaian', function (Blueprint $table) {
            $table->dropUnique('penilaian_monitoring_id_unique');
        });
    }
};
