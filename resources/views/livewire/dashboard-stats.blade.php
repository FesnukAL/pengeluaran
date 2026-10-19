<div>
    <div class="row mb-3 g-2 g-sm-3">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 stat-card-compact">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="stats-icon purple mb-0" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-wallet2"></i></div>
                        <h6 class="text-muted font-semibold small mb-0">Keluar Hari Ini</h6>
                    </div>
                    <h5 class="font-extrabold mb-0 fs-6 fs-sm-5">Rp {{ number_format($pengeluaran_hari_ini, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 stat-card-compact">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="stats-icon green mb-0" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-arrow-down-left-circle"></i></div>
                        <h6 class="text-muted font-semibold small mb-0">Masuk Bln Ini</h6>
                    </div>
                    <h5 class="font-extrabold mb-0 text-success fs-6 fs-sm-5">Rp {{ number_format($total_pemasukan, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 stat-card-compact">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="stats-icon red mb-0" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-arrow-up-right-circle"></i></div>
                        <h6 class="text-muted font-semibold small mb-0">Keluar Bln Ini</h6>
                    </div>
                    <h5 class="font-extrabold mb-0 text-danger fs-6 fs-sm-5">Rp {{ number_format($total_pengeluaran, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card h-100 shadow-sm border-0 stat-card-compact">
                <div class="card-body p-2 p-sm-3">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="stats-icon blue mb-0" style="width: 2rem; height: 2rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;"><i class="bi bi-cash-stack"></i></div>
                        <h6 class="text-muted font-semibold small mb-0">Sisa Saldo</h6>
                    </div>
                    <h5 class="font-extrabold mb-0 text-primary fs-6 fs-sm-5">Rp {{ number_format($sisa_saldo, 0, ',', '.') }}</h5>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3 mt-2">Rincian Saldo & Arus Kas per Dompet</h5>
    <div class="row mb-4">
        @foreach($breakdown_dompet as $dompet)
        <div class="col-md-4 mb-3">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-muted mb-0 fw-bold">{{ $dompet->nama }}</h6>
                        <span class="badge bg-primary">Rp {{ number_format($dompet->saldo, 0, ',', '.') }}</span>
                    </div>

                    <div class="row text-center small border-top pt-3 mt-2">
                        <div class="col-6 border-end">
                            <span class="text-muted d-block mb-1">Masuk (Bln Ini)</span>
                            <span class="text-success fw-bold">+ Rp {{ number_format($dompet->pemasukan_bulan_ini, 0, ',', '.') }}</span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block mb-1">Keluar (Bln Ini)</span>
                            <span class="text-danger fw-bold">- Rp {{ number_format($dompet->pengeluaran_bulan_ini, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card shadow-sm">
        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <h4 class="card-title">Riwayat Transaksi</h4>
        </div>
        <div class="card-body mt-3">

            <div class="row mb-4">
                <div class="col-md-3 mb-2">
                    <select class="form-select border-primary" wire:model.live="bulan">
                        <option value="01">Januari</option>
                        <option value="02">Februari</option>
                        <option value="03">Maret</option>
                        <option value="04">April</option>
                        <option value="05">Mei</option>
                        <option value="06">Juni</option>
                        <option value="07">Juli</option>
                        <option value="08">Agustus</option>
                        <option value="09">September</option>
                        <option value="10">Oktober</option>
                        <option value="11">November</option>
                        <option value="12">Desember</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <select class="form-select border-primary" wire:model.live="tahun">
                        @for ($i = 2025; $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-lg mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Tipe</th>
                            <th>Kategori / Detail</th>
                            <th>Keterangan</th>
                            <th class="text-end">Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}</td>
                            <td>
                                @if($item->tipe === 'pemasukan')
                                <span class="badge bg-light-success text-success"><i class="bi bi-arrow-down-left"></i> IN</span>
                                @else
                                <span class="badge bg-light-danger text-danger"><i class="bi bi-arrow-up-right"></i> OUT</span>
                                @endif
                            </td>
                            <td class="fw-bold text-muted">{{ $item->nama_tipe }}</td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                            <td class="text-end fw-bold {{ $item->tipe === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                {{ $item->tipe === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi pada bulan ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>