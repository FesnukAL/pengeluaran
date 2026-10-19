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
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->foreignId('dompet_id')->nullable()->after('id')->constrained('dompets')->nullOnDelete();
        });

        // Tambah dompet_id ke tabel pemasukans
        Schema::table('pemasukans', function (Blueprint $table) {
            $table->foreignId('dompet_id')->nullable()->after('id')->constrained('dompets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengeluarans', function (Blueprint $table) {
            $table->dropForeign(['dompet_id']);
            $table->dropColumn('dompet_id');
        });

        Schema::table('pemasukans', function (Blueprint $table) {
            $table->dropForeign(['dompet_id']);
            $table->dropColumn('dompet_id');
        });
    }
};
