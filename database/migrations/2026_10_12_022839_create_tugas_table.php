<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('matkul_id')->nullable()->constrained('matkuls')->nullOnDelete();
            $table->string('judul_tugas');
            $table->text('deskripsi')->nullable();
            $table->dateTime('deadline'); // Tanggal dan jam pengumpulan tugas
            $table->string('status')->default('belum'); // belum, proses, selesai
            $table->string('prioritas')->default('Sedang'); // Tinggi, Sedang, Rendah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
