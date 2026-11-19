@extends('layouts.mazer')

@section('content')
<div class="page-title mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-6">
            <h3 class="fw-bold mb-1">Profil & Biodata Pengembang</h3>
            <p class="text-subtitle text-muted mb-0">Informasi mengenai pengembang aplikasi Catatan Keuangan & Pengingat Tugas.</p>
        </div>
        <div class="col-12 col-md-6 text-md-end mt-3 mt-md-0">
            <a href="/dashboard" class="btn btn-outline-primary btn-sm rounded-pill px-3 shadow-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- KOLOM KIRI: Kartu Profil Utama --}}
    <div class="col-12 col-lg-5 col-xl-4">
        <div class="card shadow-sm border-0 text-center position-relative overflow-hidden">
            {{-- Background Banner Gradient --}}
            <div style="height: 110px; background: linear-gradient(135deg, #435ebe 0%, #2575fc 100%);"></div>

            {{-- Avatar Wrapper --}}
            <div class="position-relative mx-auto" style="margin-top: -55px; width: 110px; height: 110px;">
                <div class="rounded-circle border border-4 border-white shadow bg-white d-flex align-items-center justify-content-center text-primary fs-1 fw-bold" style="width: 100%; height: 100%; background: #f0f4ff;">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>

            <div class="card-body pt-3 pb-4">
                <h4 class="fw-bold mb-1">Afifudin Albar</h4>
                <p class="text-muted small mb-2">@Albaryaping</p>
                <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill fw-semibold small mb-3">
                    <i class="bi bi-code-slash me-1"></i> Full-Stack Web Developer & Mahasiswa
                </span>

                <p class="text-muted small px-2 mb-4">
                    Pengembang aplikasi web yang fokus pada pembuatan sistem informasi praktis, manajemen data keuangan, dan integrasi bot otomatisasi.
                </p>

                <hr class="my-3 opacity-25">

                {{-- TOMBOL KONTAK & SOSIAL MEDIA --}}
                <h6 class="fw-bold text-start mb-3 small text-uppercase text-secondary">
                    <i class="bi bi-link-45deg me-1"></i> Hubungi / Ikuti Saya:
                </h6>

                <div class="d-grid gap-2">
                    {{-- Tombol WhatsApp --}}
                    <a href="https://wa.me/?text=Halo%20Albar,%20saya%20tertarik%20dengan%20proyek%20Catatan%20Keuangan%20dan%20Tugas" 
                       target="_blank" 
                       class="btn btn-success d-flex align-items-center justify-content-center shadow-sm py-2 rounded-3">
                        <i class="bi bi-whatsapp fs-5 me-2"></i> Chat via WhatsApp
                    </a>

                    {{-- Tombol Instagram --}}
                    <a href="https://instagram.com/" 
                       target="_blank" 
                       class="btn text-white d-flex align-items-center justify-content-center shadow-sm py-2 rounded-3" 
                       style="background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); border: none;">
                        <i class="bi bi-instagram fs-5 me-2"></i> Ikuti di Instagram
                    </a>

                    {{-- Tombol GitHub --}}
                    <a href="https://github.com/FesnukAL" 
                       target="_blank" 
                       class="btn btn-dark d-flex align-items-center justify-content-center shadow-sm py-2 rounded-3">
                        <i class="bi bi-github fs-5 me-2"></i> Profil GitHub (FesnukAL)
                    </a>

                    {{-- Tombol Portofolio Repository --}}
                    <a href="https://github.com/FesnukAL/pengeluaran" 
                       target="_blank" 
                       class="btn btn-outline-primary d-flex align-items-center justify-content-center shadow-sm py-2 rounded-3">
                        <i class="bi bi-folder2-open fs-5 me-2"></i> Repository Proyek Ini
                    </a>
                </div>
            </div>
        </div>

        {{-- Info Kontak Ringkas --}}
        <div class="card shadow-sm border-0">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light-primary text-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-envelope-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Alamat Email</small>
                        <a href="mailto:uwiwiwiwiegeg@gmail.com" class="fw-semibold text-break">uwiwiwiwiegeg@gmail.com</a>
                    </div>
                </div>

                <div class="d-flex align-items-center mb-3">
                    <div class="bg-light-success text-success rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-telegram"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Bot Telegram</small>
                        <a href="https://t.me/Pengingatalbar_bot" target="_blank" class="fw-semibold">@Pengingatalbar_bot</a>
                    </div>
                </div>

                <div class="d-flex align-items-center">
                    <div class="bg-light-info text-info rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Lokasi</small>
                        <span class="fw-semibold">Indonesia 🇮🇩</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: Detail Biodata & Portofolio Proyek --}}
    <div class="col-12 col-lg-7 col-xl-8">
        {{-- Card Tentang Saya --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header border-bottom bg-transparent py-3">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="bi bi-person-lines-fill text-primary fs-4 me-2"></i> Tentang Pengembang
                </h5>
            </div>
            <div class="card-body pt-3">
                <p>
                    Halo! Nama saya <strong>Afifudin Albar</strong>. Saya adalah mahasiswa dan pengembang web yang bersemangat mempelajari teknologi rekayasa perangkat lunak, arsitektur berbasis cloud, serta integrasi sistem otomatisasi.
                </p>
                <p class="mb-3">
                    Website <strong>Catatan Keuangan & Pengingat Tugas</strong> ini saya rancang sebagai solusi terpadu untuk membantu pencatatan finansial pribadi (pemasukan, pengeluaran, transfer mutasi saldo antar dompet) sekaligus mengorganisasi jadwal akademik perkuliahan dan tugas harian agar tidak terlewat deadline.
                </p>

                <h6 class="fw-bold mb-2 small text-uppercase text-secondary">
                    <i class="bi bi-cpu me-1"></i> Keahlian & Teknologi yang Digunakan:
                </h6>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light-primary text-primary px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> PHP 8.3 & Laravel 11</span>
                    <span class="badge bg-light-info text-info px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> Livewire 3 (Reaktif)</span>
                    <span class="badge bg-light-success text-success px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> MySQL Database</span>
                    <span class="badge bg-light-warning text-warning px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> Bootstrap 5 & Mazer UI</span>
                    <span class="badge bg-light-danger text-danger px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> Telegram Bot API</span>
                    <span class="badge bg-light-secondary text-secondary px-3 py-2 rounded-pill"><i class="bi bi-check2-circle me-1"></i> Docker & Railway.app (PaaS)</span>
                </div>
            </div>
        </div>

        {{-- Card Proyek Portofolio --}}
        <div class="card shadow-sm border-0">
            <div class="card-header border-bottom bg-transparent py-3">
                <h5 class="card-title mb-0 d-flex align-items-center">
                    <i class="bi bi-briefcase-fill text-primary fs-4 me-2"></i> Portofolio Proyek Terkait
                </h5>
            </div>
            <div class="card-body pt-3">
                <div class="row g-3">
                    {{-- Proyek 1 --}}
                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-light position-relative">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">Catatan Keuangan & Pengingat Tugas Mahasiswa</h6>
                                    <small class="text-muted">Aplikasi Web Multi-User + Cloud Deployment</small>
                                </div>
                                <span class="badge bg-success rounded-pill px-3 py-1 small">Live Production</span>
                            </div>
                            <p class="small text-muted mb-3">
                                Platform pencatatan keuangan modern multi-dompet dengan pemantauan batas anggaran boros, integrasi jadwal kuliah, serta notifikasi otomatis tugas via Bot Telegram.
                            </p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="https://github.com/FesnukAL/pengeluaran" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    <i class="bi bi-github me-1"></i> Lihat Kode di GitHub
                                </a>
                                <a href="https://pengeluaran-production.up.railway.app" target="_blank" class="btn btn-sm btn-primary rounded-pill px-3">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka Live Demo Railway
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Proyek 2 --}}
                    <div class="col-12">
                        <div class="p-3 rounded-3 border bg-light position-relative">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1 text-primary">Sistem Bot Notifikasi & Pengingat Telegram Otomatis</h6>
                                    <small class="text-muted">Integrasi Webhook & Cron Job Scheduler</small>
                                </div>
                                <span class="badge bg-info rounded-pill px-3 py-1 small">Integrated</span>
                            </div>
                            <p class="small text-muted mb-3">
                                Layanan background cron yang secara otomatis mengirimkan rekap tugas deadline dan jadwal kuliah setiap pukul 07:00 pagi dan rekap produktivitas harian pukul 21:00 malam ke akun Telegram masing-masing pengguna.
                            </p>
                            <a href="{{ route('pengaturan.telegram') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-telegram me-1"></i> Lihat Konfigurasi Bot
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
