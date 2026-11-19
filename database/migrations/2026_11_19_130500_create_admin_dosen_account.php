<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pengaturan;
use App\Models\Dompet;
use App\Models\Kategori;
use App\Models\Matkul;
use App\Models\Tugas;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat atau update user admin untuk Dosen
        $user = User::where('email', 'admin@admin.com')->orWhere('name', 'admin')->first();

        if (!$user) {
            $user = User::create([
                'name'     => 'admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]);
        } else {
            $user->update([
                'name'     => 'admin',
                'email'    => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'role'     => 'admin',
            ]);
        }

        // 2. Buat Pengaturan default untuk user admin
        Pengaturan::firstOrCreate(
            ['user_id' => $user->id],
            [
                'batas_mingguan'       => 750000,
                'batas_bulanan'        => 2500000,
                'batas_tahunan'        => 15000000,
                'notif_telegram_aktif' => false,
                'notif_tugas_baru'     => true,
                'notif_tugas_selesai'  => true,
                'jam_pengingat_pagi'   => '07:00',
                'jam_laporan_malam'    => '21:00',
            ]
        );

        // 3. Tambahkan Dompet default jika belum ada
        if (Dompet::where('user_id', $user->id)->count() === 0) {
            Dompet::create([
                'user_id' => $user->id,
                'nama'    => 'Rekening Utama (Bank)',
                'saldo'   => 3500000,
            ]);

            Dompet::create([
                'user_id' => $user->id,
                'nama'    => 'Dompet Tunai',
                'saldo'   => 500000,
            ]);
        }

        // 4. Tambahkan Kategori default jika belum ada
        if (Kategori::where('user_id', $user->id)->count() === 0) {
            $kategoriList = [
                'Makanan & Minuman',
                'Transportasi & Bensin',
                'Pendidikan & Kampus',
                'Gaji / Honor Mengajar',
                'Tunjangan & Bonus',
            ];

            foreach ($kategoriList as $namaKategori) {
                Kategori::create([
                    'user_id'       => $user->id,
                    'nama_kategori' => $namaKategori,
                ]);
            }
        }

        // 5. Tambahkan contoh Matkul & Tugas jika belum ada
        if (Matkul::where('user_id', $user->id)->count() === 0) {
            $matkul = Matkul::create([
                'user_id'     => $user->id,
                'nama_matkul' => 'Pemrograman Web Lanjut',
                'kode_matkul' => 'PWL-01',
                'dosen'       => 'Dosen Pengampu',
                'sks'         => 3,
                'hari'        => 'Senin',
                'jam_mulai'   => '08:00:00',
                'jam_selesai' => '10:30:00',
                'ruangan'     => 'Lab Komputer 2',
                'tipe'        => 'kuliah',
            ]);

            if (Tugas::where('user_id', $user->id)->count() === 0) {
                Tugas::create([
                    'user_id'     => $user->id,
                    'matkul_id'   => $matkul->id,
                    'tipe'        => 'kuliah',
                    'judul_tugas' => 'Review Proyek Akhir Mahasiswa (Pengeluaran & Tugas)',
                    'deskripsi'   => 'Memeriksa implementasi cloud deployment di Railway dan bot Telegram.',
                    'deadline'    => Carbon::now()->addDays(2)->format('Y-m-d H:i:s'),
                    'prioritas'   => 'Tinggi',
                    'status'      => 'proses',
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $user = User::where('email', 'admin@admin.com')->orWhere('name', 'admin')->first();
        if ($user) {
            Tugas::where('user_id', $user->id)->delete();
            Matkul::where('user_id', $user->id)->delete();
            Kategori::where('user_id', $user->id)->delete();
            Dompet::where('user_id', $user->id)->delete();
            Pengaturan::where('user_id', $user->id)->delete();
            $user->delete();
        }
    }
};
