<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->string('nama', 20)->comment('contoh: 2024/2025');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('aktif')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['institution_id', 'nama', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_years');
    }
};
