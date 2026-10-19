<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->string('tipe')->default('kuliah')->after('matkul_id'); // 'kuliah' atau 'daily'
        });

        // Migrasikan data todo lama ke tabel tugas sebagai 'daily'
        if (Schema::hasTable('todos')) {
            $todos = DB::table('todos')->get();
            foreach ($todos as $todo) {
                $prioritas = 'Sedang';
                if (isset($todo->prioritas)) {
                    if ($todo->prioritas === 'Urgent') $prioritas = 'Tinggi';
                    elseif ($todo->prioritas === 'Santai') $prioritas = 'Rendah';
                    elseif ($todo->prioritas === 'Tinggi' || $todo->prioritas === 'Rendah') $prioritas = $todo->prioritas;
                }

                DB::table('tugas')->insert([
                    'matkul_id' => null,
                    'tipe' => 'daily',
                    'judul_tugas' => $todo->kegiatan,
                    'deskripsi' => null,
                    'deadline' => $todo->created_at ?: now(),
                    'status' => $todo->is_selesai ? 'selesai' : 'belum',
                    'prioritas' => $prioritas,
                    'created_at' => $todo->created_at ?: now(),
                    'updated_at' => $todo->updated_at ?: now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};

