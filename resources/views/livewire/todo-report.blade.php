<div>
    <div class="page-heading">
        <div class="page-title mb-4">
            <h3><i class="bi bi-graph-up-arrow"></i> Analisis Produktivitas</h3>
            <p class="text-subtitle text-muted">Rekapitulasi pengerjaan tugas harian Anda.</p>
        </div>

        @if (session()->has('sukses'))
        <div class="alert alert-success alert-dismissible show fade mb-3">
            <i class="bi bi-check-circle"></i> {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-end">
                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                        <label class="form-label fw-bold text-muted">Dari Tanggal</label>
                        <input type="date" class="form-control" wire:model.live="tgl_mulai">
                    </div>
                    <div class="col-md-4 col-12 mb-2 mb-md-0">
                        <label class="form-label fw-bold text-muted">Sampai Tanggal</label>
                        <input type="date" class="form-control" wire:model.live="tgl_selesai">
                    </div>
                    <div class="col-md-4 col-12 d-grid">
                        <button class="btn btn-secondary" wire:click="resetFilter" @if(!$tgl_mulai && !$tgl_selesai) disabled @endif>
                            <i class="bi bi-arrow-clockwise"></i> Bersihkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-2 g-sm-3">
            <div class="col-6 col-lg-4">
                <div class="card shadow-sm border-start border-success border-4 h-100 mb-0">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="text-muted font-semibold small mb-1">Berhasil Selesai</h6>
                        <h3 class="font-extrabold mb-0 text-success fs-4 fs-sm-3">{{ $persenSelesai }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-4">
                <div class="card shadow-sm border-start border-danger border-4 h-100 mb-0">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="text-muted font-semibold small mb-1">Tugas Terlewat</h6>
                        <h3 class="font-extrabold mb-0 text-danger fs-4 fs-sm-3">{{ $persenTerlewat }}%</h3>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <div class="card shadow-sm border-start border-primary border-4 h-100 mb-0">
                    <div class="card-body p-2 p-sm-3">
                        <h6 class="text-muted font-semibold small mb-1">Total Seluruh Tugas</h6>
                        <h3 class="font-extrabold mb-0 text-primary fs-4 fs-sm-3">{{ $totalTask }} <small class="fs-6">Item</small></h3>
                    </div>
                </div>
            </div>
        </div>

        <section class="section mt-4">
            @forelse ($laporan as $tanggal => $tasks)
            <div class="card mb-4 shadow-sm animate__animated animate__fadeIn">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="bi bi-calendar3 me-2"></i>
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </h5>

                    <div class="d-flex align-items-center gap-2">
                        @php
                        $dayTotal = $tasks->count();
                        $dayDone = $tasks->where('is_selesai', true)->count();
                        $dayRate = $dayTotal > 0 ? round(($dayDone / $dayTotal) * 100) : 0;
                        @endphp
                        <span class="badge {{ $dayRate == 100 ? 'bg-success' : 'bg-warning' }} me-2">
                            Produktivitas: {{ $dayRate }}%
                        </span>

                        <button class="btn btn-sm btn-outline-danger"
                            wire:click="hapusPerTanggal('{{ $tanggal }}')"
                            wire:confirm="Yakin ingin menghapus seluruh rekaman laporan pada tanggal {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}?">
                            <i class="bi bi-trash3-fill"></i> Hapus Hari Ini
                        </button>
                    </div>
                </div>
                <div class="card-body mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-lg mb-0">
                            <thead>
                                <tr>
                                    <th>Kegiatan</th>
                                    <th class="text-center" width="20%">Status</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tasks as $task)
                                <tr>
                                    <td class="col-auto">
                                        <p class="mb-0 {{ $task->is_selesai ? 'text-muted text-decoration-line-through' : 'fw-bold' }}">
                                            {{ $task->kegiatan }}
                                        </p>
                                    </td>
                                    <td class="col-auto text-center">
                                        @if($task->is_selesai)
                                        <span class="badge bg-light-success text-success">Selesai</span>
                                        @else
                                        <span class="badge bg-light-danger text-danger">Terlewat</span>
                                        @endif
                                    </td>
                                    <td class="col-auto text-center">
                                        <button class="btn btn-sm btn-light-danger text-danger p-1 px-2"
                                            wire:click="hapusTask({{ $task->id }})"
                                            wire:confirm="Hapus tugas '{{ $task->kegiatan }}' dari laporan ini?">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="100" class="mb-3 opacity-50" alt="No Data">
                    <h5>Tidak Ada Data Ditemukan</h5>
                    <p class="text-muted">Ganti parameter filter atau arsipkan tugas baru di halaman Input Daily.</p>
                </div>
            </div>
            @endforelse
        </section>
    </div>
</div>