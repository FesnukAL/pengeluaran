<div>
    <div class="card">
        <div class="card-content">
            <div class="card-body">

                @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible show fade mb-4">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                @if (session()->has('error'))
                <div class="alert alert-danger alert-dismissible show fade mb-4">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form wire:submit.prevent="simpanMutasi">

                    <div class="form-group mb-4">
                        <label for="tanggal" class="mb-2 fw-bold">Tanggal Transaksi</label>
                        <input type="date" id="tanggal" wire:model="tanggal" class="form-control" required>
                        @error('tanggal') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label for="dompet_asal" class="mb-2 fw-bold">Dari Dompet (Asal)</label>
                                <select id="dompet_asal" wire:model="dompet_asal_id" class="form-select" required>
                                    <option value="" disabled>-- Pilih Dompet Asal --</option>
                                    @foreach ($dompets as $dompet)
                                    <option value="{{ $dompet->id }}">{{ $dompet->nama }} (Saldo: Rp {{ number_format($dompet->saldo, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                @error('dompet_asal_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="col-md-6 mt-3 mt-md-0">
                            <div class="form-group mb-0">
                                <label for="dompet_tujuan" class="mb-2 fw-bold">Ke Dompet (Tujuan)</label>
                                <select id="dompet_tujuan" wire:model="dompet_tujuan_id" class="form-select" required>
                                    <option value="" disabled>-- Pilih Dompet Tujuan --</option>
                                    @foreach ($dompets as $dompet)
                                    <option value="{{ $dompet->id }}">{{ $dompet->nama }}</option>
                                    @endforeach
                                </select>
                                @error('dompet_tujuan_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-4">
                        <label for="nominal" class="mb-2 fw-bold">Nominal (Rp)</label>
                        <input type="number" id="nominal" wire:model="nominal" class="form-control" placeholder="Contoh: 300000" required min="1">
                        @error('nominal') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="keterangan" class="mb-2 fw-bold">Keterangan Tambahan</label>
                        <input type="text" id="keterangan" wire:model="keterangan" class="form-control" placeholder="Contoh: Tarik tunai dari ATM BCA">
                        @error('keterangan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan Mutasi
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>