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
                <h5 class="mb-0 text-success fw-extrabold fs-6 fs-sm-5">Rp {{ number_format($total_pemasukan_setahun, 0, ',', '.') }}</h5>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-danger stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold">TOTAL PENGELUARAN</small>
                <h5 class="mb-0 text-danger fw-extrabold fs-6 fs-sm-5">Rp {{ number_format($total_setahun, 0, ',', '.') }}</h5>
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
            <h4 class="card-title">Rekap Pengeluaran Tahunan</h4>
        </div>
        <div class="card-body">
            
            <div class="row mb-4 mt-2">
                <div class="col-md-3 mb-2">
                    <label class="fw-bold mb-1">Pilih Tahun</label>
                    <select class="form-select" wire:model.live="tahun">
                        @for ($i = 2025; $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <div class="col-md-4 mb-2">
                    <label class="fw-bold text-primary mb-1">Filter Sumber Dana</label>
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
                            <th width="10%">No</th>
                            <th>Bulan</th>
                            <th width="30%" class="text-end">Total Pengeluaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($laporan_per_bulan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $item['bulan'] }}</td>
                            <td class="text-end fw-bold">
                                @if($item['total'] > 0)
                                    <span class="text-danger">Rp {{ number_format($item['total'], 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>