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
        Schema::table('matkuls', function (Blueprint $table) {
            $table->string('tipe')->default('kuliah')->after('id'); // 'kuliah' atau 'jaga_lab'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matkuls', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
