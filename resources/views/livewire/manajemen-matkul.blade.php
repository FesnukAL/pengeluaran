<div>
    @if (session()->has('sukses'))
        <div class="alert alert-success alert-dismissible show fade">
            <i class="bi bi-check-circle me-2"></i> {{ session('sukses') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Ringkasan Statistik --}}
    <div class="row mb-4 g-2 g-sm-3">
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-primary stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-book"></i> TOTAL MATKUL</small>
                <h4 class="mb-0 text-primary fw-extrabold fs-5 fs-sm-4">{{ $total_matkul }} Matkul</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-success stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-laptop"></i> JAGA LAB</small>
                <h4 class="mb-0 text-success fw-extrabold fs-5 fs-sm-4">{{ $total_lab }} Shift</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-info stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-award"></i> TOTAL SKS</small>
                <h4 class="mb-0 text-info fw-extrabold fs-5 fs-sm-4">{{ $total_sks }} SKS</h4>
            </div>
        </div>
        <div class="col-6 col-lg-3 col-md-6">
            <div class="card bg-light-warning stat-card-compact p-2 p-sm-3 h-100 shadow-sm border-0">
                <small class="text-muted d-block mb-1 fw-bold"><i class="bi bi-calendar-event"></i> HARI INI</small>
                @php
                    $hari_indo = [
                        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                    ];
                    $hari_ini = $hari_indo[date('l')] ?? date('l');
                    $kegiatan_hari_ini = $matkuls->where('hari', $hari_ini)->count();
                @endphp
                <h4 class="mb-0 text-warning fw-extrabold fs-5 fs-sm-4">{{ $hari_ini }} ({{ $kegiatan_hari_ini }})</h4>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Form Tambah / Edit --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom pb-2">
                    <h5 class="card-title mb-0">
                        <i class="bi {{ $is_edit ? 'bi-pencil-square text-warning' : 'bi-plus-circle text-primary' }}"></i>
                        {{ $is_edit ? 'Edit Jadwal' : 'Tambah Jadwal Baru' }}
                    </h5>
                </div>
                <div class="card-body pt-3">
                    <form wire:submit.prevent="simpan">
                        {{-- Pilihan Kategori / Tipe --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Kategori Jadwal <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="tipe" id="tipe_kuliah" value="kuliah">
                                    <label class="form-check-label fw-semibold" for="tipe_kuliah">
                                        📚 Mata Kuliah
                                    </label>
                                </div>
                                <div class="form-check ms-3">
                                    <input class="form-check-input" type="radio" wire:model.live="tipe" id="tipe_lab" value="jaga_lab">
                                    <label class="form-check-label fw-semibold text-success" for="tipe_lab">
                                        🔬 Jaga Lab
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                {{ $tipe === 'jaga_lab' ? 'Nama Lab / Sesi Jaga' : 'Nama Mata Kuliah' }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nama_matkul') is-invalid @enderror" wire:model="nama_matkul" 
                                placeholder="{{ $tipe === 'jaga_lab' ? 'Contoh: Jaga Lab RPL / Multimedia' : 'Contoh: Pemrograman Web' }}">
                            @error('nama_matkul') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">{{ $tipe === 'jaga_lab' ? 'Kode / Shift' : 'Kode Matkul' }}</label>
                                <input type="text" class="form-control" wire:model="kode_matkul" placeholder="{{ $tipe === 'jaga_lab' ? 'Shift 1' : 'IF101' }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">{{ $tipe === 'jaga_lab' ? 'Durasi (Jam)' : 'SKS' }}</label>
                                <input type="number" class="form-control" wire:model="sks" min="0" max="10">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ $tipe === 'jaga_lab' ? 'Koordinator Lab / Rekan Jaga' : 'Dosen Pengampu' }}</label>
                            <input type="text" class="form-control" wire:model="dosen" placeholder="{{ $tipe === 'jaga_lab' ? 'Nama kordas atau rekan jaga' : 'Nama dosen pengampu' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Hari <span class="text-danger">*</span></label>
                            <select class="form-select @error('hari') is-invalid @enderror" wire:model="hari">
                                @foreach($hari_list as $h)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endforeach
                            </select>
                            @error('hari') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Jam Mulai</label>
                                <input type="time" class="form-control" wire:model="jam_mulai">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label fw-bold">Jam Selesai</label>
                                <input type="time" class="form-control" wire:model="jam_selesai">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-8 mb-3">
                                <label class="form-label fw-bold">Ruangan / Tempat</label>
                                <input type="text" class="form-control" wire:model="ruangan" placeholder="{{ $tipe === 'jaga_lab' ? 'Lab Komputer 3 / Gedung B' : 'R. 302 / Zoom' }}">
                            </div>
                            <div class="col-4 mb-3">
                                <label class="form-label fw-bold">Warna</label>
                                <input type="color" class="form-control form-control-color w-100" wire:model="warna" title="Pilih warna tema jadwal">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Catatan Tambahan</label>
                            <textarea class="form-control" wire:model="catatan" rows="2" placeholder="Catatan tugas piket, inventaris, kontrak kuliah..."></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn {{ $is_edit ? 'btn-warning' : 'btn-primary' }} flex-grow-1">
                                <i class="bi bi-save me-1"></i> {{ $is_edit ? 'Update Jadwal' : 'Simpan Jadwal' }}
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

        {{-- Daftar Jadwal Kuliah & Jaga Lab --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header border-bottom pb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h5 class="card-title mb-0"><i class="bi bi-calendar3 me-1"></i> Jadwal Kuliah & Jaga Lab</h5>

                        {{-- Filter Kategori / Tipe --}}
                        <div class="btn-group btn-group-sm flex-wrap">
                            <button class="btn {{ $filter_tipe === '' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filter_tipe', '')">
                                Semua Jadwal
                            </button>
                            <button class="btn {{ $filter_tipe === 'kuliah' ? 'btn-primary' : 'btn-outline-secondary' }}" wire:click="$set('filter_tipe', 'kuliah')">
                                📚 Kuliah ({{ $total_matkul }})
                            </button>
                            <button class="btn {{ $filter_tipe === 'jaga_lab' ? 'btn-success text-white' : 'btn-outline-secondary' }}" wire:click="$set('filter_tipe', 'jaga_lab')">
                                🔬 Jaga Lab ({{ $total_lab }})
                            </button>
                        </div>
                    </div>

                    {{-- Filter Hari --}}
                    <div class="d-flex gap-1 flex-wrap mt-2">
                        <button class="btn btn-sm {{ $filter_hari === '' ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('filter_hari', '')">
                            Semua Hari
                        </button>
                        @foreach($hari_list as $h)
                            <button class="btn btn-sm {{ $filter_hari === $h ? 'btn-dark' : 'btn-outline-secondary' }}" wire:click="$set('filter_hari', '{{ $h }}')">
                                {{ $h }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="card-body pt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kegiatan & Kategori</th>
                                    <th>Hari & Jam</th>
                                    <th>Ruang & Penanggung Jawab</th>
                                    <th class="text-center">Tugas</th>
                                    <th class="text-center" width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($matkuls as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="d-inline-block rounded-circle me-2 flex-shrink-0" style="width: 12px; height: 12px; background-color: {{ $item->warna }};"></span>
                                                <div>
                                                    <span class="fw-bold">{{ $item->nama_matkul }}</span>
                                                    <div class="mt-1">
                                                        @if($item->tipe === 'jaga_lab')
                                                             <span class="badge bg-success"><i class="bi bi-laptop"></i> Jaga Lab</span>
                                                        @else
                                                             <span class="badge bg-primary"><i class="bi bi-book"></i> Kuliah</span>
                                                        @endif

                                                        @if($item->kode_matkul)
                                                            <span class="badge bg-secondary ms-1">{{ $item->kode_matkul }}</span>
                                                        @endif
                                                        <small class="text-muted ms-1">
                                                            {{ $item->tipe === 'jaga_lab' ? ($item->sks ? $item->sks . ' Jam' : '') : $item->sks . ' SKS' }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light-primary text-primary fw-bold">{{ $item->hari }}</span>
                                            <div class="small text-muted mt-1">
                                                @if($item->jam_mulai)
                                                    <i class="bi bi-clock"></i> {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $item->ruangan ?: '-' }}</div>
                                            <small class="text-muted">
                                                <i class="bi bi-person"></i> {{ $item->dosen ?: 'Belum diisi' }}
                                            </small>
                                        </td>
                                        <td class="text-center">
                                            @if($item->tugas_aktif_count > 0)
                                                <a href="{{ route('kuliah.tugas') }}" class="badge bg-danger text-white text-decoration-none">
                                                    {{ $item->tugas_aktif_count }} Tugas
                                                </a>
                                            @else
                                                <span class="badge bg-light-secondary text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-warning" wire:click="edit({{ $item->id }})" title="Edit">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button class="btn btn-outline-danger" onclick="confirm('Yakin ingin menghapus jadwal ini?') || event.stopImmediatePropagation()" wire:click="hapus({{ $item->id }})" title="Hapus">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                                            Belum ada jadwal yang tercatat pada filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
