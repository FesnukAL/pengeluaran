<div>
    <div class="page-title mb-4">
        <div class="row align-items-center">
            <div class="col-12 col-md-6">
                <h3 class="fw-bold mb-1">Pengaturan Notifikasi & Bot Telegram</h3>
                <p class="text-subtitle text-muted mb-0">Atur bot Telegram dan jadwal waktu pengingat tugas & kuliah sesuai keinginanmu.</p>
            </div>
            <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
                @if($notif_telegram_aktif && !empty($telegram_chat_id))
                    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> Telegram Terhubung & Aktif
                    </span>
                @else
                    <span class="badge bg-secondary px-3 py-2 fs-6 rounded-pill shadow-sm">
                        <i class="bi bi-dash-circle-fill me-1"></i> Telegram Belum Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Alert Pesan Sukses / Error --}}
    @if (session()->has('sukses'))
        <div class="alert alert-success alert-dismissible show fade d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>{{ session('sukses') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible show fade d-flex align-items-center shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- KOLOM KIRI: Form Pengaturan Bot & Waktu --}}
        <div class="col-12 col-lg-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-telegram text-primary fs-4 me-2"></i> Konfigurasi Bot Telegram
                    </h5>
                </div>
                <div class="card-body pt-4">
                    <form wire:submit.prevent="simpan">
                        {{-- Toggle Aktifkan Telegram --}}
                        <div class="form-check form-switch mb-4 p-3 bg-light rounded border">
                            <input class="form-check-input fs-5 ms-0 me-3" type="checkbox" id="notif_telegram_aktif" wire:model="notif_telegram_aktif">
                            <label class="form-check-label fw-bold d-block" for="notif_telegram_aktif">
                                Aktifkan Notifikasi Telegram
                            </label>
                            <small class="text-muted d-block mt-1">
                                Jika diaktifkan, bot akan mengirimkan pengingat jadwal kuliah, tugas, dan rekap harian ke Telegram kamu.
                            </small>
                        </div>

                        {{-- Input Chat ID Telegram --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Telegram Chat ID <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                                <input type="text" class="form-control @error('telegram_chat_id') is-invalid @enderror" 
                                    wire:model="telegram_chat_id" 
                                    placeholder="Contoh: 8062953455">
                            </div>
                            @error('telegram_chat_id')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                ID akun Telegram pribadimu. Cara melihatnya: buka bot <a href="https://t.me/userinfobot" target="_blank" class="fw-semibold">@userinfobot</a> di Telegram lalu ketik /start.
                            </div>
                        </div>

                        {{-- Input Custom Bot Token (Opsional) --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Custom Bot Token <span class="badge bg-light-secondary text-secondary ms-1">Opsional</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="text" class="form-control @error('telegram_bot_token') is-invalid @enderror" 
                                    wire:model="telegram_bot_token" 
                                    placeholder="Kosongkan untuk menggunakan Bot Default Sistem">
                            </div>
                            @error('telegram_bot_token')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Biarkan kosong jika ingin menggunakan bot bawaan sistem. Jika ingin memakai bot buatanmu sendiri dari <a href="https://t.me/BotFather" target="_blank" class="fw-semibold">@BotFather</a>, tempel tokennya di sini.
                            </div>
                        </div>

                        <hr class="my-4">

                        <h5 class="fw-bold mb-3 d-flex align-items-center">
                            <i class="bi bi-clock-history text-primary fs-4 me-2"></i> Pengaturan Waktu Pengingat
                        </h5>

                        {{-- Input Jam Pengingat Pagi & Jam Laporan Malam --}}
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-sunrise text-warning me-1"></i> Jam Pengingat Pagi
                                </label>
                                <input type="time" class="form-control form-control-lg @error('jam_pengingat_pagi') is-invalid @enderror" 
                                    wire:model="jam_pengingat_pagi">
                                @error('jam_pengingat_pagi')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Jadwal kuliah, lab, & tugas harian dikirim pada jam ini (WIB).</small>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold">
                                    <i class="bi bi-moon-stars text-info me-1"></i> Jam Laporan Malam
                                </label>
                                <input type="time" class="form-control form-control-lg @error('jam_laporan_malam') is-invalid @enderror" 
                                    wire:model="jam_laporan_malam">
                                @error('jam_laporan_malam')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Rekap pencapaian tugas harian dikirim pada jam ini (WIB).</small>
                            </div>
                        </div>

                        {{-- Opsi Notifikasi Event Tugas --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block">Notifikasi Event Tugas</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="notif_tugas_baru" wire:model="notif_tugas_baru">
                                <label class="form-check-label" for="notif_tugas_baru">
                                    Kirim notifikasi otomatis saat <strong>Tugas Baru</strong> ditambahkan
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="notif_tugas_selesai" wire:model="notif_tugas_selesai">
                                <label class="form-check-label" for="notif_tugas_selesai">
                                    Kirim notifikasi apresiasi saat <strong>Tugas Selesai</strong>
                                </label>
                            </div>
                        </div>

                        {{-- Tombol Simpan & Test --}}
                        <div class="d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-1"></i> Simpan Pengaturan
                            </button>
                            <button type="button" class="btn btn-outline-success px-3" wire:click="testKoneksi" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="testKoneksi"><i class="bi bi-send-check me-1"></i> Tes Kirim Pesan</span>
                                <span wire:loading wire:target="testKoneksi"><span class="spinner-border spinner-border-sm me-1"></span> Mengirim tes...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: Panduan Setup & Aksi Cepat --}}
        <div class="col-12 col-lg-5">
            {{-- Card Tombol Cepat Trigger --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-lightning-charge text-warning fs-4 me-2"></i> Uji Coba Pengiriman Manual
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Kamu bisa langsung mengirimkan format pesan pengingat ke Telegram saat ini juga tanpa perlu menunggu jam jadwal otomatis:
                    </p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-light-primary text-primary fw-semibold text-start p-3" 
                            wire:click="kirimPengingatManual" wire:loading.attr="disabled">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-sunrise-fill fs-3 me-3"></i>
                                <div>
                                    <div class="fw-bold">Kirim Pengingat Jadwal Pagi Sekarang</div>
                                    <small class="text-muted">Kirim daftar jadwal kuliah & tugas hari ini</small>
                                </div>
                            </div>
                        </button>

                        <button type="button" class="btn btn-light-info text-info fw-semibold text-start p-3" 
                            wire:click="kirimLaporanManual" wire:loading.attr="disabled">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-moon-stars-fill fs-3 me-3"></i>
                                <div>
                                    <div class="fw-bold">Kirim Laporan Malam Sekarang</div>
                                    <small class="text-muted">Kirim rekap produktivitas to-do & tugas</small>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Card Panduan Singkat Hubungkan Telegram --}}
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom bg-transparent py-3">
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="bi bi-info-circle text-info fs-4 me-2"></i> Panduan Menghubungkan Telegram
                    </h5>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered list-group-flush mb-3">
                        <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Dapatkan Chat ID Kamu</div>
                                Buka Telegram, cari bot <code>@userinfobot</code>, tekan <strong>START</strong>. Bot akan membalas dengan nomor ID kamu (misal: <code>8062953455</code>).
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Mulai Percakapan dengan Bot</div>
                                Pastikan kamu sudah menekan <strong>START</strong> pada bot Telegram agar bot memiliki izin untuk mengirimkan pesan kepadamu.
                            </div>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-start border-0 px-0">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Simpan & Uji Coba</div>
                                Masukkan Chat ID kamu di form sebelah kiri, centang <strong>Aktifkan Notifikasi Telegram</strong>, lalu klik tombol <strong>Tes Kirim Pesan</strong>.
                            </div>
                        </li>
                    </ol>

                    <div class="alert alert-light-primary border mb-0 small">
                        <i class="bi bi-shield-check me-1"></i>
                        <strong>Multi-User Aman:</strong> Pesan dan pengingat hanya akan dikirimkan ke Chat ID akunmu sendiri, tidak akan bercampur dengan pengguna lain.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
