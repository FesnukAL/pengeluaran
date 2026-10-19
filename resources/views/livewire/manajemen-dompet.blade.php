<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Tambah Dompet Baru</h4>
        </div>
        <div class="card-body">

            @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible show fade">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible show fade">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <form wire:submit.prevent="simpanDompet">
                <div class="row align-items-end">

                    <div class="col-md-5 mb-3">
                        <div class="form-group mb-0">
                            <label for="nama" class="mb-2">Nama Dompet</label>
                            <input type="text" id="nama" wire:model="nama" class="form-control" placeholder="Contoh: Bank BRI" required>
                            @error('nama') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-5 mb-3">
                        <div class="form-group mb-0">
                            <label for="saldo" class="mb-2">Saldo Awal (Rp)</label>
                            <input type="number" id="saldo" wire:model="saldo" class="form-control" placeholder="Contoh: 1000000" required min="0">
                            @error('saldo') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle"></i> Simpan
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Daftar Dompet & Saldo Saat Ini</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama Dompet</th>
                            <th>Total Saldo</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($dompets as $dompet)
                        <tr>
                            <td class="text-bold-500">{{ $dompet->nama }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($dompet->saldo, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <button wire:click="hapusDompet({{ $dompet->id }})" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Belum ada dompet yang ditambahkan.</td>
                        </tr>
                        @endempty
                    </tbody>
                    <tfoot class="border-top">
                        <tr>
                            <td class="text-end fw-bold">TOTAL KESELURUHAN:</td>
                            <td class="text-success fw-bold">Rp {{ number_format($dompets->sum('saldo'), 0, ',', '.') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>