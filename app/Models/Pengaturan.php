<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengaturan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'batas_mingguan',
        'batas_bulanan',
        'batas_tahunan',
        'telegram_chat_id',
        'telegram_bot_token',
        'notif_telegram_aktif',
        'notif_tugas_baru',
        'notif_tugas_selesai',
        'jam_pengingat_pagi',
        'jam_laporan_malam',
    ];

    protected $casts = [
        'notif_telegram_aktif' => 'boolean',
        'notif_tugas_baru'     => 'boolean',
        'notif_tugas_selesai'  => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ambil bot token yang efektif: jika user punya bot token custom pakai itu,
     * jika kosong fallback ke bot token default sistem.
     */
    public function getEffectiveBotToken(): ?string
    {
        return !empty($this->telegram_bot_token)
            ? $this->telegram_bot_token
            : (config('services.telegram.bot_token') ?: env('TELEGRAM_BOT_TOKEN'));
    }
}
