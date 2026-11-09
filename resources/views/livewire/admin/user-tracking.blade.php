<div>
    <div class="page-heading">
        <div class="page-title mb-3">
            <div class="row align-items-center">
                <div class="col-12 col-md-6">
                    <h3 class="fw-bold text-dark">
                        <i class="bi bi-shield-shaded text-primary me-2"></i> Panel Super Admin & Tracking User
                    </h3>
                    <p class="text-subtitle text-muted">Pantau pengguna yang aktif dan aktivitas sistem secara real-time.</p>
                </div>
                <div class="col-12 col-md-6 text-md-end">
                    <span class="badge bg-light-primary text-primary fs-6 px-3 py-2 rounded-pill">
                        <i class="bi bi-person-check-fill me-1"></i> Mode Super Admin Aktif
                    </span>
                </div>
            </div>
        </div>

        @if(session('sukses'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 1. KARTU STATISTIK GLOBAL SISTEM -->
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon purple mb-2">
                                    <i class="bi bi-people-fill text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Pengguna</h6>
                                <h4 class="font-extrabold mb-0">{{ $stats['total_users'] }} User</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon blue mb-2">
                                    <i class="bi bi-shield-lock-fill text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Super Admin</h6>
                                <h4 class="font-extrabold mb-0">{{ $stats['total_admin'] }} Orang</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon green mb-2">
                                    <i class="bi bi-arrow-left-right text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Transaksi</h6>
                                <h4 class="font-extrabold mb-0">{{ $stats['total_transaksi'] }} Data</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6">
                <div class="card shadow-sm border-0">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                <div class="stats-icon red mb-2">
                                    <i class="bi bi-clipboard-check-fill text-white"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold">Total Tugas</h6>
                                <h4 class="font-extrabold mb-0">{{ $stats['total_tugas'] }} Tugas</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TABEL DAFTAR PENGGUNA -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="bi bi-table me-2"></i> Daftar Seluruh Pengguna Website
                </h5>
                <div class="d-flex align-items-center" style="max-width: 320px; width: 100%;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control border-start-0" placeholder="Cari nama / email user...">
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Pengguna</th>
                                <th>Role</th>
                                <th class="text-center">Dompet</th>
                                <th class="text-center">Transaksi</th>
                                <th class="text-center">Tugas & Matkul</th>
                                <th>Bergabung</th>
                                <th class="text-end">Aksi Super Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $u)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                                {{ strtoupper(substr($u->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                                <small class="text-muted">{{ $u->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($u->role === 'admin')
                                            <span class="badge bg-danger rounded-pill px-3 py-1">
                                                <i class="bi bi-shield-check me-1"></i> Super Admin
                                            </span>
                                        @else
                                            <span class="badge bg-light-secondary text-secondary rounded-pill px-3 py-1">
                                                <i class="bi bi-person me-1"></i> User
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-primary text-primary fw-bold">
                                            {{ $u->dompets_count }} Dompet
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-success text-success fw-bold">
                                            {{ $u->pengeluarans_count + $u->pemasukans_count }} Transaksi
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-light-info text-info fw-bold">
                                            {{ $u->tugas_count }} Tugas / {{ $u->matkuls_count }} Matkul
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $u->created_at ? $u->created_at->format('d M Y, H:i') : '-' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        @if($u->id !== auth()->id())
                                            <button wire:click="toggleRole({{ $u->id }})" class="btn btn-sm btn-outline-warning me-1" title="Ubah Role">
                                                <i class="bi bi-arrow-repeat"></i> {{ $u->role === 'admin' ? 'Jadikan User' : 'Jadikan Admin' }}
                                            </button>
                                            <button wire:click="hapusUser({{ $u->id }})" wire:confirm="Yakin ingin menghapus pengguna {{ $u->name }}? Semua data keuangan dan tugas miliknya akan terhapus permanen!" class="btn btn-sm btn-outline-danger" title="Hapus User">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @else
                                            <span class="badge bg-light-primary text-primary">Akun Anda</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Tidak ada data pengguna yang sesuai pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
