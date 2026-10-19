<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Form Catat Pemasukan</h4>
        </div>
        <div class="card-body">

            @if (session()->has('sukses'))
            <div class="alert alert-success alert-dismissible show fade mb-4">
                {{ session('sukses') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible show fade mb-4">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form wire:submit.prevent="simpan">

                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="fw-bold mb-2">Tanggal</label>
                            <input type="date" wire:model="tanggal" class="form-control" required>
                            @error('tanggal') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="form-group mb-0">
                            <label class="fw-bold mb-2">Dompet Tujuan (Uang Masuk Ke Mana?)</label>
                            <select wire:model="dompet_id" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Dompet --</option>
                                @foreach($daftarDompet as $dompet)
                                <option value="{{ $dompet->id }}">{{ $dompet->nama }} (Saldo: Rp {{ number_format($dompet->saldo, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('dompet_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="fw-bold mb-2">Nominal (Rp)</label>
                    <input type="number" wire:model="nominal" class="form-control" placeholder="Contoh: 4800000" required>
                    @error('nominal') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="form-group mb-5">
                    <label class="fw-bold mb-2">Sumber / Keterangan</label>
                    <input type="text" wire:model="keterangan" class="form-control" placeholder="Contoh: Pencairan KIP-K Semester ini" required>
                    @error('keterangan') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end border-top pt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="bi bi-wallet2"></i> Simpan Pemasukan
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>