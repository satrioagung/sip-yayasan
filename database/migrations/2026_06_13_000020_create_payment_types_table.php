<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();

            $table->string('nama');           // contoh: SPP Bulanan, Ujian Semester
            $table->string('kode', 20);       // contoh: SPP, UJIAN
            $table->unsignedBigInteger('nominal_default')->default(0); // Rp dalam satuan integer
            $table->enum('tipe', ['bulanan', 'tahunan', 'sekali', 'bebas'])->default('bulanan');
            $table->boolean('bisa_cicil')->default(false);
            $table->boolean('wajib')->default(true);
            $table->boolean('aktif')->default(true);
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['institution_id', 'kode', 'deleted_at'], 'payment_types_kode_unique');
            $table->index(['institution_id', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_types');
    }
};
