<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('nis', 20)->comment('Nomor Induk Siswa');
            $table->string('nisn', 10)->nullable()->comment('Nomor Induk Siswa Nasional');
            $table->string('nama_lengkap', 150);
            $table->enum('jenis_kelamin', ['L', 'P'])->comment('L=Laki-laki, P=Perempuan');
            $table->date('tanggal_lahir')->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp_ortu', 20)->nullable()->comment('Nomor HP Orang Tua/Wali');
            $table->string('nama_ortu', 100)->nullable()->comment('Nama Orang Tua/Wali');
            $table->string('foto')->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // NIS unik per lembaga
            $table->unique(['institution_id', 'nis', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
