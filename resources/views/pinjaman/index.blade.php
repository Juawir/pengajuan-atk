@extends('layouts.app')

@section('title', 'Pinjaman Barang')
@section('subtitle', 'Pengajuan pinjaman barang antar departemen')

@section('content')
    {{-- Filter Periode — Multi-month --}}
    <div class="glass-card animate-in" style="padding: 14px 18px; margin-bottom: 16px;" id="filter-period-card">
        <form method="GET" action="{{ route('pinjaman.index') }}" id="form-filter-periode">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-calendar3" style="color: var(--accent-primary); font-size: 14px;"></i>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); font-family: 'Manrope', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Periode:</span>
                </div>
                <select name="tahun" class="form-control" style="width: auto; min-width: 100px; padding: 6px 12px; font-size: 13px;" id="filter-tahun-pinjaman">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ (int)$tahun === (int)$t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm" id="btn-filter-apply-pinjaman" style="padding: 6px 14px;">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
                <a href="{{ route('pinjaman.index', ['tab' => $tab]) }}" class="btn btn-ghost btn-sm" id="btn-filter-reset-pinjaman" style="padding: 6px 14px;">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
            <div style="display: flex; align-items: center; gap: 5px; flex-wrap: wrap;">
                @foreach(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'] as $i => $nama)
                    @php $monthNum = $i + 1; $isActive = in_array($monthNum, array_map('intval', $selectedMonths)); @endphp
                    <label class="month-chip {{ $isActive ? 'active' : '' }}">
                        <input type="checkbox" name="bulan[]" value="{{ $monthNum }}" {{ $isActive ? 'checked' : '' }}>
                        {{ $nama }}
                    </label>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Tab Navigation --}}
    <div class="glass-card animate-in" style="padding: 6px; margin-bottom: 16px; display: flex; gap: 4px;" id="tab-nav">
        <a href="{{ route('pinjaman.index', array_merge(request()->query(), ['tab' => 'keluar'])) }}"
           class="btn {{ $tab === 'keluar' ? 'btn-primary' : 'btn-ghost' }}" id="tab-keluar"
           style="flex: 1; justify-content: center;">
            <i class="bi bi-box-arrow-up-right"></i> Pinjaman Keluar
            <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 20px; font-size: 11px; margin-left: 4px;">{{ $pinjamanKeluar->count() }}</span>
        </a>
        <a href="{{ route('pinjaman.index', array_merge(request()->query(), ['tab' => 'masuk'])) }}"
           class="btn {{ $tab === 'masuk' ? 'btn-primary' : 'btn-ghost' }}" id="tab-masuk"
           style="flex: 1; justify-content: center;">
            <i class="bi bi-box-arrow-in-down-left"></i> Pinjaman Masuk
            <span style="background: rgba(255,255,255,0.2); padding: 2px 8px; border-radius: 20px; font-size: 11px; margin-left: 4px;">{{ $pinjamanMasuk->count() }}</span>
        </a>
    </div>

    {{-- Action Bar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <a href="{{ route('pinjaman.create') }}" class="btn btn-primary" id="btn-create-pinjaman">
                <i class="bi bi-plus-lg"></i> Ajukan Pinjaman
            </a>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('pinjaman.exportCSV', array_merge(request()->query(), ['tab' => $tab])) }}" class="btn btn-ghost btn-sm" id="btn-export-csv-pinjaman" title="Export CSV">
                <i class="bi bi-filetype-csv" style="color: var(--accent-emerald);"></i> CSV
            </a>
            <a href="{{ route('pinjaman.exportPDF', array_merge(request()->query(), ['tab' => $tab])) }}" class="btn btn-ghost btn-sm" target="_blank" id="btn-export-pdf-pinjaman" title="Export PDF">
                <i class="bi bi-filetype-pdf" style="color: var(--accent-primary);"></i> PDF
            </a>
        </div>
    </div>

    {{-- TAB: PINJAMAN KELUAR --}}
    @if($tab === 'keluar')
        <div class="glass-card animate-in" id="table-keluar-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-box-arrow-up-right" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Pinjaman yang Anda Ajukan
                </div>
                <span style="font-size: 13px; color: var(--text-muted);">{{ $pinjamanKeluar->count() }} data</span>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="table-pinjaman-keluar">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tujuan Dept.</th>
                            <th>Barang</th>
                            <th style="text-align: center;">Jumlah</th>
                            <th>Tgl Pinjam</th>
                            <th>Tgl Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjamanKeluar as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-cyan);"></div>
                                        {{ $p->tujuan_departemen }}
                                    </div>
                                </td>
                                <td>{{ $p->nama_barang }}</td>
                                <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $p->jumlah }}</td>
                                <td>{{ $p->tanggal_pinjam->format('d M Y') }}</td>
                                <td>{{ $p->tanggal_kembali ? $p->tanggal_kembali->format('d M Y') : '-' }}</td>
                                <td>
                                    @if($p->status === 'Pending')
                                        <span class="badge badge-pending"><i class="bi bi-clock"></i> Pending</span>
                                    @elseif($p->status === 'Disetujui')
                                        <span class="badge badge-approved"><i class="bi bi-check-lg"></i> Disetujui</span>
                                    @else
                                        <span class="badge badge-rejected"><i class="bi bi-x-lg"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pinjaman.show', $p) }}" class="btn btn-ghost btn-sm" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h3>Belum Ada Pinjaman Keluar</h3>
                                        <p>Anda belum mengajukan pinjaman barang ke departemen lain.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TAB: PINJAMAN MASUK --}}
    @if($tab === 'masuk')
        <div class="glass-card animate-in" id="table-masuk-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-box-arrow-in-down-left" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                    Pinjaman yang Masuk ke Departemen Anda
                </div>
                <span style="font-size: 13px; color: var(--text-muted);">{{ $pinjamanMasuk->count() }} data</span>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="table-pinjaman-masuk">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Peminjam</th>
                            <th>Dept. Asal</th>
                            <th>Barang</th>
                            <th style="text-align: center;">Jumlah</th>
                            <th>Tgl Pinjam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pinjamanMasuk as $i => $p)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $p->peminjam_nama }}</td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-primary);"></div>
                                        {{ $p->peminjam_departemen }}
                                    </div>
                                </td>
                                <td>{{ $p->nama_barang }}</td>
                                <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $p->jumlah }}</td>
                                <td>{{ $p->tanggal_pinjam->format('d M Y') }}</td>
                                <td>
                                    @if($p->status === 'Pending')
                                        <span class="badge badge-pending"><i class="bi bi-clock"></i> Pending</span>
                                    @elseif($p->status === 'Disetujui')
                                        <span class="badge badge-approved"><i class="bi bi-check-lg"></i> Disetujui</span>
                                    @else
                                        <span class="badge badge-rejected"><i class="bi bi-x-lg"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a href="{{ route('pinjaman.show', $p) }}" class="btn btn-ghost btn-sm" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if($p->status === 'Pending')
                                            <form method="POST" action="{{ route('pinjaman.updateStatus', $p) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Disetujui">
                                                <button type="submit" class="btn btn-success btn-sm" title="Setujui">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('pinjaman.updateStatus', $p) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="Ditolak">
                                                <button type="submit" class="btn btn-danger btn-sm" title="Tolak">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox"></i>
                                        <h3>Tidak Ada Pinjaman Masuk</h3>
                                        <p>Tidak ada departemen lain yang meminjam barang ke departemen Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
<script>
    // Month chip toggle
    document.querySelectorAll('.month-chip').forEach(label => {
        label.addEventListener('click', function() {
            setTimeout(() => {
                const cb = this.querySelector('input[type="checkbox"]');
                this.classList.toggle('active', cb.checked);
            }, 10);
        });
    });
</script>
@endsection
