<div>
    @if($is_edit)
    <div class="card border border-primary shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0 text-white"><i class="bi bi-pencil-square"></i> Edit Transaksi ({{ ucfirst($tipe_edit) }})</h5>
        </div>
        <div class="card-body mt-3">
            <form wire:submit.prevent="update">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">Tanggal</label>
                        <input type="date" class="form-control" wire:model="edit_tanggal">
                    </div>

                    @if($tipe_edit === 'pengeluaran')
                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">Kategori</label>
                        <select class="form-select" wire:model="edit_kategori_id">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-3 mb-3">
                        <label class="fw-bold">Nominal (Rp)</label>
                        <input type="number" class="form-control" wire:model="edit_nominal">
                    </div>
                    <div class="col-md-{{ $tipe_edit === 'pengeluaran' ? '3' : '6' }} mb-3">
                        <label class="fw-bold">Keterangan / Sumber</label>
                        <input type="text" class="form-control" wire:model="edit_keterangan">
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-light me-2" wire:click="batalEdit">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header pb-0">
            <h4 class="card-title">Semua Riwayat Alur Kas Transaksi</h4>
        </div>
        <div class="card-body">
            @if (session()->has('sukses'))
            <div class="alert alert-light-success color-success alert-dismissible show fade">
                <i class="bi bi-check-circle"></i> {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <div class="row mb-4">
                <div class="col-md-3 mb-2">
                    <label class="fw-bold mb-1">Bulan</label>
                    <select class="form-select" wire:model.live="bulan">
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
                    <label class="fw-bold mb-1">Tahun</label>
                    <select class="form-select" wire:model.live="tahun">
                        @for ($i = 2025; $i <= date('Y') + 2; $i++)
                            <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th width="15%">Jenis Arus</th>
                            <th width="20%">Kategori / Detail</th>
                            <th>Keterangan Tambahan</th>
                            <th width="15%" class="text-end">Nominal</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transaksi as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>
                                @if($item->tipe === 'pemasukan')
                                <span class="badge bg-light-success text-success w-100"><i class="bi bi-arrow-down-left-circle"></i> MASUK</span>
                                @else
                                <span class="badge bg-light-danger text-danger w-100"><i class="bi bi-arrow-up-right-circle"></i> KELUAR</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted fw-bold">{{ $item->nama_tipe }}</small>
                            </td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="text-end fw-bold {{ $item->tipe === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                {{ $item->tipe === 'pemasukan' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-warning me-1" wire:click="editAction({{ $item->id }}, '{{ $item->tipe }}')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" wire:click="hapus({{ $item->id }}, '{{ $item->tipe }}')" wire:confirm="Yakin ingin menghapus data transaksi ini?">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-folder-x d-block fs-2 mb-2"></i>Tidak ada riwayat transaksi pada bulan ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>