<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('school_year_id')->constrained('school_years')->cascadeOnDelete();
            $table->string('nama_kelas', 50)->comment('contoh: X RPL 1');
            $table->string('tingkat', 10)->comment('contoh: X, XI, XII, 7, 8, 9');
            $table->string('jurusan', 50)->nullable()->comment('contoh: RPL, TKJ, Akuntansi');
            $table->string('wali_kelas', 100)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['institution_id', 'school_year_id', 'nama_kelas', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
