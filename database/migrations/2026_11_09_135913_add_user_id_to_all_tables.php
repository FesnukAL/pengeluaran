<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tabel-tabel yang akan ditambahkan user_id
     */
    protected array $tables = [
        'dompets',
        'kategoris',
        'pengeluarans',
        'pemasukans',
        'mutasis',
        'matkuls',
        'tugas',
        'todos',
        'pengaturans',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'user_id')) {
                        $table->foreignId('user_id')
                            ->nullable()
                            ->after('id')
                            ->constrained('users')
                            ->onDelete('cascade');
                    }
                });
            }
        }

        // Jika sudah ada user di database, kaitkan data lama ke user pertama
        $firstUserId = DB::table('users')->orderBy('id')->value('id');
        if ($firstUserId) {
            foreach ($this->tables as $tableName) {
                if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'user_id')) {
                    DB::table($tableName)->whereNull('user_id')->update(['user_id' => $firstUserId]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'user_id')) {
                        $table->dropForeign([$tableName . '_user_id_foreign']);
                        $table->dropColumn('user_id');
                    }
                });
            }
        }
    }
};
