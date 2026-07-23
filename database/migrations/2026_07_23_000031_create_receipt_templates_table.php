<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();

            $table->string('nama_template');
            $table->text('header')->nullable();   // HTML/teks header struk
            $table->text('footer')->nullable();   // HTML/teks footer struk
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_qr')->default(false);
            $table->enum('ukuran', ['a4', 'thermal58', 'thermal80'])->default('a4');
            $table->boolean('is_default')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['institution_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_templates');
    }
};
