<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bill_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('nomor_transaksi', 50)->unique();
            $table->date('tanggal_bayar');
            $table->enum('metode_bayar', ['tunai','transfer','qris','kartu_debit','kartu_kredit','lainnya'])->default('tunai');
            $table->unsignedBigInteger('nominal_bayar');
            $table->string('bukti_file')->nullable();
            $table->text('keterangan')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'tanggal_bayar']);
            $table->index(['bill_id']);
            $table->index(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
