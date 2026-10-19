<div>
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="fw-bold text-muted mb-1"><i class="bi bi-gear-fill"></i> Atur Batas Pengeluaran Wajar (Rp)</label>
            <input type="number" class="form-control border-primary" wire:model.live="batas_boros">
        </div>
    </div>

    <div class="row mb-4 g-2 g-sm-3">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-success stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold">TOTAL PEMASUKAN</small>
                <h5 class="mb-0 text-success fw-extrabold fs-6 fs-sm-5">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-danger stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold">TOTAL PENGELUARAN</small>
                <h5 class="mb-0 text-danger fw-extrabold fs-6 fs-sm-5">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-primary stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold">SISA SALDO</small>
                <h5 class="mb-0 text-primary fw-extrabold fs-6 fs-sm-5">Rp {{ number_format($sisa_saldo, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card {{ $status_boros == 'AMAN 👍' ? 'bg-light-info' : 'bg-light-warning' }} stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold">STATUS KEUANGAN</small>
                <h5 class="mb-0 fw-extrabold fs-6 fs-sm-5">{{ $status_boros }}</h5>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header pb-0">
            <h4 class="card-title">Daftar Detail Pengeluaran</h4>
        </div>
        <div class="card-body">

            <div class="row mb-4 mt-2">
                <div class="col-md-3 mb-2">
                    <label class="fw-bold">Dari Tanggal</label>
                    <input type="date" class="form-control" wire:model.live="tgl_mulai">
                </div>
                <div class="col-md-3 mb-2">
                    <label class="fw-bold">Sampai Tanggal</label>
                    <input type="date" class="form-control" wire:model.live="tgl_selesai">
                </div>

                <div class="col-md-4 mb-2">
                    <label class="fw-bold text-primary">Filter Sumber Dana</label>
                    <select class="form-select border-primary" wire:model.live="filter_dompet">
                        <option value="">-- Semua Dompet / Rekening --</option>
                        @foreach($daftarDompet as $dompet)
                        <option value="{{ $dompet->id }}">{{ $dompet->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th width="25%">Kategori / Dompet</th>
                            <th>Keterangan Tambahan</th>
                            <th width="20%" class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $item->kategori->nama_kategori ?? 'Tanpa Kategori' }}</span>
                                <div class="mt-1">
                                    <small class="text-muted fw-bold">
                                        <i class="bi bi-wallet-fill"></i> {{ $item->dompet->nama ?? 'Tanpa Dompet' }}
                                    </small>
                                </div>
                            </td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td class="text-end fw-bold text-danger">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada catatan pengeluaran pada filter ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>