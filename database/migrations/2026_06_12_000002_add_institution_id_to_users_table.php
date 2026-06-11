<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('institution_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('institutions')
                  ->nullOnDelete();
            $table->string('role')->default('siswa')->after('email');
            $table->string('nis', 20)->nullable()->after('role')->comment('Nomor Induk Siswa');
            $table->string('phone', 20)->nullable()->after('nis');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->dropColumn(['role', 'nis', 'phone', 'is_active']);
            $table->dropSoftDeletes();
        });
    }
};
