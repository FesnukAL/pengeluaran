<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Form Catatan Pengeluaran</h4>
        </div>
        <div class="card-body">

            @if (session()->has('sukses'))
            <div class="alert alert-success alert-dismissible show fade mb-4">
                {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            @if ($pesan_error)
            <div class="alert alert-warning alert-dismissible show fade mb-4">
                {{ $pesan_error }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form wire:submit.prevent="simpan">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold mb-2">Tanggal Transaksi</label>
                            <input type="date" wire:model="tanggal" class="form-control" required>
                            @error('tanggal') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="form-group mb-0">
                            <label class="fw-bold mb-2">Sumber Dana (Dompet)</label>
                            <select wire:model="dompet_id" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Dompet --</option>
                                @foreach($daftarDompet as $dompet)
                                <option value="{{ $dompet->id }}">{{ $dompet->nama }} (Sisa: Rp {{ number_format($dompet->saldo, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('dompet_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr class="mb-4">
                <h5 class="mb-3">Daftar Item Pengeluaran</h5>

                @foreach($pengeluaran as $index => $item)
                <div class="row align-items-end mb-3">

                    <div class="col-md-4">
                        <label class="mb-1">Kategori</label>
                        <select wire:model="pengeluaran.{{ $index }}.kategori_id" class="form-select" required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>

                            @foreach($daftarKategori as $kategori)
                            <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach

                        </select>
                        @error('pengeluaran.'.$index.'.kategori_id') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-3 mt-2 mt-md-0">
                        <label class="mb-1">Nominal (Rp)</label>
                        <input type="number" wire:model.lazy="pengeluaran.{{ $index }}.nominal" class="form-control" placeholder="0" required>
                        @error('pengeluaran.'.$index.'.nominal') <span class="text-danger small">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-4 mt-2 mt-md-0">
                        <label class="mb-1">Keterangan Tambahan</label>
                        <input type="text" wire:model="pengeluaran.{{ $index }}.keterangan" class="form-control" placeholder="Contoh: Beli nasi goreng...">
                    </div>

                    <div class="col-md-1 mt-3 mt-md-0 text-md-center">
                        @if(count($pengeluaran) > 1)
                        <button type="button" wire:click="hapusBaris({{ $index }})" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>

                </div>
                @endforeach

                <div class="mb-4">
                    <button type="button" wire:click="tambahBaris" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-plus-circle"></i> Tambah Item Lain
                    </button>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <h4 class="mb-0 text-primary fw-bold">Total: Rp {{ number_format($total_nominal, 0, ',', '.') }}</h4>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Simpan ke Database
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>