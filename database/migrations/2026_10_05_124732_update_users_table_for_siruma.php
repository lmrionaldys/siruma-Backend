<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')
                ->nullable()
                ->after('id')
                ->constrained('roles')
                ->nullOnDelete();

            $table->string('nama')->after('role_id');

            $table->string('username')
                ->unique()
                ->after('email');

            $table->string('status')
                ->default('aktif')
                ->after('password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropUnique(['users_username_unique']);
            $table->dropColumn([
                'role_id',
                'nama',
                'username',
                'status',
            ]);
        });
    }
};