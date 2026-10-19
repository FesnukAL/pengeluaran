<div>
    <div class="page-heading">
        <div class="page-title mb-3">
            <h3><i class="bi bi-list-check"></i> Input Daily Task</h3>
            <p class="text-subtitle text-muted">Catat dan centang pekerjaanmu hari ini.</p>
        </div>

        <section class="section">
            <div class="card shadow-sm border-primary">
                <div class="card-body">

                    @if (session()->has('sukses'))
                    <div class="alert alert-success"><i class="bi bi-check-circle"></i> {{ session('sukses') }}</div>
                    @endif
                    @if (session()->has('error'))
                    <div class="alert alert-danger"><i class="bi bi-exclamation-circle"></i> {{ session('error') }}</div>
                    @endif

                    <form wire:submit.prevent="tambah" class="mb-4">
                        <div class="row g-2">
                            <div class="col-12 col-md-8">
                                <input type="text" class="form-control border-primary" wire:model="kegiatan_baru" placeholder="Tulis rencana kegiatan...">
                            </div>
                            <div class="col-8 col-md-3 mt-2 mt-md-0">
                                <select class="form-select border-primary" wire:model="prioritas">
                                    <option value="Urgent">🔴 Urgent</option>
                                    <option value="Biasa">🟡 Biasa</option>
                                    <option value="Santai">🟢 Santai</option>
                                </select>
                            </div>
                            <div class="col-4 col-md-1 mt-2 mt-md-0 d-grid">
                                <button class="btn btn-primary" type="submit">
                                    <i class="bi bi-plus-lg fw-bold"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mt-1">
                            @error('kegiatan_baru') <span class="text-danger small me-3">{{ $message }}</span> @enderror
                            @error('prioritas') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </form>

                    <hr>

                    <div class="list-group mb-3">
                        @forelse ($todos as $todo)
                        <label class="list-group-item d-flex gap-3 align-items-center" style="cursor: pointer;">
                            <input class="form-check-input flex-shrink-0" type="checkbox" value="{{ $todo->id }}" wire:model.live="kegiatan_terpilih" style="font-size: 1.375em;">

                            <div class="w-100 d-flex justify-content-between align-items-center">
                                <span>
                                    <strong class="{{ in_array($todo->id, $kegiatan_terpilih) ? 'text-decoration-line-through text-muted' : '' }}">
                                        {{ $todo->kegiatan }}
                                    </strong>
                                </span>

                                <div>
                                    @if($todo->prioritas == 'Urgent')
                                    <span class="badge bg-danger">Urgent</span>
                                    @elseif($todo->prioritas == 'Santai')
                                    <span class="badge bg-success">Santai</span>
                                    @else
                                    <span class="badge bg-primary">Biasa</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @empty
                        <div class="text-center py-4 text-muted">
                            Belum ada tugas hari ini.
                        </div>
                        @endforelse
                    </div>

                    @if(count($todos) > 0)
                    <div class="mt-3">
                        @if($konfirmasi_simpan)
                        <div class="card bg-light-warning border border-warning shadow-sm animate__animated animate__fadeIn">
                            <div class="card-body text-center py-3">
                                <h5 class="text-warning"><i class="bi bi-exclamation-triangle-fill"></i> Konfirmasi Perhatian</h5>
                                <p class="mb-3 fw-bold">Task Anda belum selesai semua, yakin mau disimpan?</p>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-warning fw-bold" wire:click="eksekusiSimpan">
                                        <i class="bi bi-check-circle"></i> Ya, Konfirmasi Simpan
                                    </button>
                                    <button class="btn btn-secondary fw-bold" wire:click="batalSimpan">
                                        <i class="bi bi-x-circle"></i> Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                        @else
                        <div class="d-grid">
                            <button class="btn btn-dark fw-bold" wire:click="cekDanSimpan">
                                <i class="bi bi-archive"></i> Tutup & Simpan Hari Ini
                            </button>
                        </div>
                        @endif
                    </div>
                    @endif

                </div>
            </div>
        </section>
    </div>
</div>