<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();

            $table->tinyInteger('bulan')->nullable();  // 1-12, null untuk tipe non-bulanan
            $table->smallInteger('tahun');             // contoh: 2024
            $table->unsignedBigInteger('nominal');
            $table->unsignedBigInteger('nominal_terbayar')->default(0);

            $table->enum('status', ['belum_bayar', 'sebagian', 'lunas'])->default('belum_bayar');
            $table->date('jatuh_tempo')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Cegah duplikat tagihan: 1 siswa, 1 jenis pembayaran, 1 bulan/tahun
            $table->unique(
                ['student_id', 'payment_type_id', 'bulan', 'tahun', 'deleted_at'],
                'bills_unique_tagihan'
            );

            $table->index(['institution_id', 'status']);
            $table->index(['student_id', 'status']);
            $table->index(['payment_type_id', 'tahun', 'bulan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
