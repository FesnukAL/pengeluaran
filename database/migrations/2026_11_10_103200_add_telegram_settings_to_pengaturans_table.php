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
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->string('telegram_chat_id')->nullable()->after('batas_tahunan');
            $table->string('telegram_bot_token')->nullable()->after('telegram_chat_id');
            $table->boolean('notif_telegram_aktif')->default(false)->after('telegram_bot_token');
            $table->boolean('notif_tugas_baru')->default(true)->after('notif_telegram_aktif');
            $table->boolean('notif_tugas_selesai')->default(true)->after('notif_tugas_baru');
            $table->string('jam_pengingat_pagi', 5)->default('07:00')->after('notif_tugas_selesai');
            $table->string('jam_laporan_malam', 5)->default('21:00')->after('jam_pengingat_pagi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturans', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_chat_id',
                'telegram_bot_token',
                'notif_telegram_aktif',
                'notif_tugas_baru',
                'notif_tugas_selesai',
                'jam_pengingat_pagi',
                'jam_laporan_malam',
            ]);
        });
    }
};

