<div>
    @if (session()->has('sukses'))
        <div class="alert alert-success alert-dismissible show fade">
            <i class="bi bi-check-circle me-2"></i> {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Statistik Tugas --}}
    <div class="row mb-4">
        <div class="col-6 col-lg-3 col-md-6 mb-2">
            <div class="card bg-light-danger p-2 p-sm-3 h-100 shadow-sm border-0 stat-card-compact">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-clock-history"></i> BELUM SELESAI</small>
                <h4 class="mb-0 text-danger fw-extrabold">{{ $total_belum }} Tugas</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6 mb-2">
            <div class="card bg-light-warning p-2 p-sm-3 h-100 shadow-sm border-0 stat-card-compact">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-exclamation-triangle"></i> DEADLINE HARI INI</small>
                <h4 class="mb-0 text-warning fw-extrabold">{{ $total_deadline_hari_ini }} Tugas</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6 mb-2">
            <div class="card bg-light-success p-2 p-sm-3 h-100 shadow-sm border-0 stat-card-compact">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-check-circle"></i> SUDAH SELESAI</small>
                <h4 class="mb-0 text-success fw-extrabold">{{ $total_selesai }} Tugas</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6 mb-2">
            <div class="card bg-light-primary p-2 p-sm-3 h-100 shadow-sm border-0 stat-card-compact">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-list-task"></i> TOTAL KESELURUHAN</small>
                <h4 class="mb-0 text-primary fw-extrabold">{{ $total_tugas }} Tugas</h4>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Form Tambah / Edit Tugas & Daily Task --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom pb-2">
                    <h5 class="card-title mb-0">
                        <i class="bi {{ $is_edit ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' }}"></i>
                        {{ $is_edit ? 'Edit ' . ($tipe === 'daily' ? 'Daily Task' : 'Tugas Kuliah') : 'Tambah Catatan Baru' }}
                    </h5>
                </div>
                <div class="card-body pt-3">
                    {{-- Toggle Pilihan Tipe: Tugas Kuliah vs Daily Task --}}
                    @if(!$is_edit)
                    <div class="btn-group w-100 mb-3" role="group">
                        <button type="button" class="btn {{ $tipe === 'kuliah' ? 'btn-primary' : 'btn-outline-primary' }} fw-bold" wire:click="setTipeForm('kuliah')">
                            <i class="bi bi-book me-1"></i> Tugas Kuliah
                        </button>
                        <button type="button" class="btn {{ $tipe === 'daily' ? 'btn-primary' : 'btn-outline-primary' }} fw-bold" wire:click="setTipeForm('daily')">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Daily Task
                        </button>
                    </div>
                    @endif

                    <form wire:submit.prevent="simpan">
                        @if($tipe === 'kuliah')
                            {{-- Field Khusus Tugas Kuliah --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Mata Kuliah / Asal Tugas</label>
                                <select class="form-select" wire:model="matkul_id">
                                    <option value="">-- Tugas Umum (Tanpa Matkul) --</option>
                                    <optgroup label="📚 Mata Kuliah">
                                        @foreach($daftarMatkul->where('tipe', 'kuliah') as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_matkul }} ({{ $m->hari }})</option>
                                        @endforeach
                                    </optgroup>
                                    @if($daftarMatkul->where('tipe', 'jaga_lab')->count() > 0)
                                    <optgroup label="🔬 Tugas / Piket Lab">
                                        @foreach($daftarMatkul->where('tipe', 'jaga_lab') as $m)
                                            <option value="{{ $m->id }}">[Lab] {{ $m->nama_matkul }} ({{ $m->hari }})</option>
                                        @endforeach
                                    </optgroup>
                                    @endif
                                </select>
                                <small class="text-muted">Pilih mata kuliah atau tugas shift lab.</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Tugas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul_tugas') is-invalid @enderror" wire:model="judul_tugas" placeholder="Contoh: Makalah Pertemuan 4">
                                @error('judul_tugas') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal & Waktu Deadline <span class="text-danger">*</span></label>
                                <input type="datetime-local" class="form-control @error('deadline') is-invalid @enderror" wire:model="deadline">
                                @error('deadline') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        @else
                            {{-- Field Cepat Khusus Daily Task --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Kegiatan / Rencana Hari Ini <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul_tugas') is-invalid @enderror" wire:model="judul_tugas" placeholder="Contoh: Belajar modul algo / Rapikan berkas">
                                @error('judul_tugas') <span class="text-danger small">{{ $message }}</span> @enderror
                                <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Target otomatis: Hari ini (23:59 WIB)</small>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Prioritas</label>
                                <select class="form-select" wire:model="prioritas">
                                    <option value="Tinggi">🔴 Tinggi / Urgent</option>
                                    <option value="Sedang">🟡 Sedang / Biasa</option>
                                    <option value="Rendah">🟢 Rendah / Santai</option>
                                </select>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select class="form-select" wire:model="status">
                                    <option value="belum">Belum</option>
                                    <option value="proses">Proses</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan / Deskripsi Tambahan</label>
                            <textarea class="form-control" wire:model="deskripsi" rows="2" placeholder="Catatan singkat, link referensi, dll..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn {{ $is_edit ? 'btn-warning' : 'btn-primary' }} flex-grow-1">
                                <i class="bi bi-save me-1"></i> {{ $is_edit ? 'Update ' . ($tipe === 'daily' ? 'Daily Task' : 'Tugas') : 'Simpan ' . ($tipe === 'daily' ? 'Daily Task' : 'Tugas Kuliah') }}
                            </button>
                            @if($is_edit)
                                <button type="button" class="btn btn-secondary" wire:click="resetForm">
                                    Batal
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Daftar Tugas & Daily Task --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom pb-2">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h5 class="card-title mb-0"><i class="bi bi-clipboard-check me-1"></i> Daftar Tugas & Daily Task</h5>

                        {{-- Filter Status Tabs --}}
                        <div class="btn-group btn-group-sm flex-wrap">
                            <button class="btn {{ $filter_status === 'belum' ? 'btn-danger' : 'btn-outline-secondary' }}" wire:click="$set('filter_status', 'belum')">
                                Belum Selesai ({{ $total_belum }})
                            </button>
                            <button class="btn {{ $filter_status === 'selesai' ? 'btn-success' : 'btn-outline-secondary' }}" wire:click="$set('filter_status', 'selesai')">
                                Selesai ({{ $total_selesai }})
                            </button>
                            <button class="btn {{ $filter_status === 'semua' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filter_status', 'semua')">
                                Semua ({{ $total_tugas }})
                            </button>
                        </div>
                    </div>

                    {{-- Filter Tipe: Kuliah vs Daily Task --}}
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <button class="btn btn-sm {{ $filter_tipe === '' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('filter_tipe', '')">
                            Semua Tipe ({{ $total_tugas }})
                        </button>
                        <button class="btn btn-sm {{ $filter_tipe === 'kuliah' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filter_tipe', 'kuliah')">
                            <i class="bi bi-book me-1"></i> Tugas Kuliah ({{ $total_kuliah }})
                        </button>
                        <button class="btn btn-sm {{ $filter_tipe === 'daily' ? 'btn-primary' : 'btn-outline-primary' }}" wire:click="$set('filter_tipe', 'daily')">
                            <i class="bi bi-lightning-charge-fill me-1"></i> Daily Task ({{ $total_daily }})
                        </button>
                    </div>

                    {{-- Filter Matkul & Tombol Telegram --}}
                    <div class="row mt-2 g-2 align-items-center">
                        <div class="col-md-5">
                            @if($filter_tipe !== 'daily')
                            <select class="form-select form-select-sm" wire:model.live="filter_matkul">
                                <option value="">-- Semua Mata Kuliah & Lab --</option>
                                <optgroup label="📚 Mata Kuliah">
                                    @foreach($daftarMatkul->where('tipe', 'kuliah') as $m)
                                        <option value="{{ $m->id }}">{{ $m->nama_matkul }}</option>
                                    @endforeach
                                </optgroup>
                                @if($daftarMatkul->where('tipe', 'jaga_lab')->count() > 0)
                                <optgroup label="🔬 Jadwal Jaga Lab">
                                    @foreach($daftarMatkul->where('tipe', 'jaga_lab') as $m)
                                        <option value="{{ $m->id }}">[Lab] {{ $m->nama_matkul }}</option>
                                    @endforeach
                                </optgroup>
                                @endif
                            </select>
                            @endif
                        </div>
                        <div class="col-md-7 text-md-end d-flex flex-wrap gap-1 justify-content-md-end">
                            <button type="button" class="btn btn-sm btn-success text-white" wire:click="kirimLaporanHarian" wire:loading.attr="disabled" title="Kirim Laporan Harian Lengkap ke Bot Telegram">
                                <i class="bi bi-send-check me-1"></i>
                                <span wire:loading.remove wire:target="kirimLaporanHarian">Kirim Laporan ke Bot</span>
                                <span wire:loading wire:target="kirimLaporanHarian">Mengirim...</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-info text-white" wire:click="kirimPengingatManual" wire:loading.attr="disabled" title="Kirim Pengingat Jadwal & Deadline Hari Ini">
                                <i class="bi bi-bell-fill me-1"></i>
                                <span wire:loading.remove wire:target="kirimPengingatManual">Pengingat</span>
                                <span wire:loading wire:target="kirimPengingatManual">...</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="list-group">
                        @forelse($tugasList as $tugas)
                            @php
                                $deadlineCarbon = \Carbon\Carbon::parse($tugas->deadline);
                                $isOverdue = $deadlineCarbon->isPast() && $tugas->status !== 'selesai';
                                $isToday = $deadlineCarbon->isToday();
                            @endphp

                            <div class="list-group-item list-group-item-action p-3 mb-2 rounded border {{ $tugas->status === 'selesai' ? 'bg-light opacity-75' : ($isOverdue ? 'border-danger bg-light-danger' : ($isToday ? 'border-warning bg-light-warning' : '')) }}">
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div class="d-flex align-items-start gap-3 flex-grow-1">
                                        {{-- Checkbox Toggle Selesai --}}
                                        <div class="form-check mt-1">
                                            <input class="form-check-input fs-5" type="checkbox" style="cursor: pointer" 
                                                {{ $tugas->status === 'selesai' ? 'checked' : '' }}
                                                wire:click="toggleStatus({{ $tugas->id }})"
                                                title="Tandai selesai">
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                <h6 class="mb-0 {{ $tugas->status === 'selesai' ? 'text-decoration-line-through text-muted' : 'fw-bold' }}">
                                                    {{ $tugas->judul_tugas }}
                                                </h6>

                                                {{-- Tipe Badge: Daily Task vs Kuliah --}}
                                                @if($tugas->tipe === 'daily')
                                                    <span class="badge bg-primary text-white">
                                                        <i class="bi bi-lightning-charge-fill me-1"></i> Daily Task
                                                    </span>
                                                @elseif($tugas->matkul)
                                                    <span class="badge" style="background-color: {{ $tugas->matkul->warna }};">
                                                        <i class="bi {{ $tugas->matkul->tipe === 'jaga_lab' ? 'bi-laptop' : 'bi-book' }} me-1"></i>
                                                        {{ $tugas->matkul->tipe === 'jaga_lab' ? '[Lab] ' : '' }}{{ $tugas->matkul->nama_matkul }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">Umum</span>
                                                @endif

                                                {{-- Prioritas Badge --}}
                                                @if($tugas->prioritas === 'Tinggi')
                                                    <span class="badge bg-light-danger text-danger border border-danger">Prioritas Tinggi</span>
                                                @elseif($tugas->prioritas === 'Sedang')
                                                    <span class="badge bg-light-warning text-warning border border-warning">Prioritas Sedang</span>
                                                @else
                                                    <span class="badge bg-light-info text-info border border-info">Prioritas Rendah</span>
                                                @endif

                                                {{-- Status Badge --}}
                                                @if($tugas->status === 'selesai')
                                                    <span class="badge bg-success"><i class="bi bi-check2"></i> Selesai</span>
                                                @elseif($tugas->status === 'proses')
                                                    <span class="badge bg-info text-white"><i class="bi bi-hourglass-split"></i> Sedang Dikerjakan</span>
                                                @endif
                                            </div>

                                            @if($tugas->deskripsi)
                                                <p class="mb-2 text-muted small" style="white-space: pre-line;">{{ $tugas->deskripsi }}</p>
                                            @endif

                                            <div class="d-flex flex-wrap align-items-center gap-3 small mt-1">
                                                {{-- Deadline / Target Info --}}
                                                <div>
                                                    <i class="bi {{ $tugas->tipe === 'daily' ? 'bi-calendar-check' : 'bi-calendar-event' }} me-1"></i>
                                                    @if($tugas->tipe === 'daily')
                                                        <span class="fw-bold">Target:</span> {{ $deadlineCarbon->isToday() ? 'Hari ini (' . $deadlineCarbon->translatedFormat('d M Y') . ')' : $deadlineCarbon->translatedFormat('d M Y') }}
                                                    @else
                                                        <span class="fw-bold">Deadline:</span> {{ $deadlineCarbon->translatedFormat('d M Y, H:i') }}
                                                    @endif
                                                </div>

                                                {{-- Countdown / Sisa Waktu --}}
                                                <div>
                                                    @if($tugas->status === 'selesai')
                                                        <span class="text-success"><i class="bi bi-check-all"></i> Sudah dituntaskan</span>
                                                    @elseif($isOverdue)
                                                        <span class="badge bg-danger"><i class="bi bi-exclamation-octagon"></i> Terlewat {{ $deadlineCarbon->diffForHumans() }}</span>
                                                    @elseif($isToday)
                                                        <span class="badge bg-warning text-dark"><i class="bi bi-bell-fill"></i> Hari ini! ({{ $deadlineCarbon->diffForHumans() }})</span>
                                                    @else
                                                        <span class="text-muted"><i class="bi bi-stopwatch"></i> {{ $deadlineCarbon->diffForHumans() }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Aksi Edit & Hapus --}}
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" wire:click="edit({{ $tugas->id }})" title="Edit tugas">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" onclick="confirm('Yakin ingin menghapus tugas ini?') || event.stopImmediatePropagation()" wire:click="hapus({{ $tugas->id }})" title="Hapus tugas">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2 text-secondary"></i>
                                Tidak ada tugas kuliah pada filter ini.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
