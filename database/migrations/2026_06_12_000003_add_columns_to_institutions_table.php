<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            // Jenjang pendidikan
            $table->string('jenjang', 20)->nullable()->after('name')
                  ->comment('TK, SD, SMP, SMA, SMK, Madrasah, dsb');

            // Identitas visual
            $table->string('warna_tema', 7)->default('#2563eb')->after('logo')
                  ->comment('Hex color kode tema lembaga');

            // Konfigurasi struk
            $table->text('footer_struk')->nullable()->after('warna_tema')
                  ->comment('Teks footer yang dicetak di struk pembayaran');
            $table->string('prefix_nomor_struk', 10)->default('SPP')->after('footer_struk')
                  ->comment('Prefix nomor struk, misal: SPP, KWT, BYR');

            // Kepala Sekolah sudah ada (principal_name), tambah NIP
            $table->string('nip_kepala', 30)->nullable()->after('principal_name')
                  ->comment('NIP Kepala Sekolah');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['jenjang', 'warna_tema', 'footer_struk', 'prefix_nomor_struk', 'nip_kepala']);
        });
    }
};
