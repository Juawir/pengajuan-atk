@extends('layouts.app')

@section('title', 'Data Pengajuan')
@section('subtitle', 'Kelola semua pengajuan alat tulis kantor')

@section('content')
    {{-- Filter Periode — Multi-month --}}
    <div class="glass-card animate-in" style="padding: 14px 18px; margin-bottom: 16px;" id="filter-period-card">
        <form method="GET" action="{{ route('pengajuan.index') }}" id="form-filter-periode">
            {{-- Preserve existing filters --}}
            @if(request('search'))
                <input type="hidden" name="search" value="{{ request('search') }}">
            @endif
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            @if(request('departemen'))
                <input type="hidden" name="departemen" value="{{ request('departemen') }}">
            @endif

            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 10px;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-calendar3" style="color: var(--accent-primary); font-size: 14px;"></i>
                    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted); font-family: 'Manrope', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Periode:</span>
                </div>
                <select name="tahun" class="form-control" style="width: auto; min-width: 100px; padding: 6px 12px; font-size: 13px;" id="filter-tahun">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ (int)$tahun === (int)$t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm" id="btn-filter-apply" style="padding: 6px 14px;">
                    <i class="bi bi-funnel-fill"></i> Terapkan
                </button>
                <a href="{{ route('pengajuan.index') }}" class="btn btn-ghost btn-sm" id="btn-filter-reset" style="padding: 6px 14px;">
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

    {{-- Toolbar --}}
    <div class="toolbar">
        <div class="toolbar-left">
            <form method="GET" action="{{ route('pengajuan.index') }}" style="display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
                {{-- Preserve month filters --}}
                @foreach($selectedMonths as $m)
                    <input type="hidden" name="bulan[]" value="{{ $m }}">
                @endforeach
                <input type="hidden" name="tahun" value="{{ $tahun }}">

                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" name="search" class="form-control" placeholder="Cari pemohon, barang..." value="{{ request('search') }}" id="input-search">
                </div>
                <select name="status" class="form-control" style="width: auto; min-width: 160px;" onchange="this.form.submit()" id="filter-status">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui" {{ request('status') === 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                @if(auth()->user()->isAdmin())
                    <select name="departemen" class="form-control" style="width: auto; min-width: 160px;" onchange="this.form.submit()" id="filter-departemen">
                        <option value="">Semua Departemen</option>
                        @foreach($departemenList as $dept)
                            <option value="{{ $dept }}" {{ request('departemen') === $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                @endif
            </form>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <a href="{{ route('pengajuan.exportCSV', request()->query()) }}" class="btn btn-ghost btn-sm" id="btn-export-csv" title="Export CSV">
                <i class="bi bi-filetype-csv" style="color: var(--accent-emerald);"></i> CSV
            </a>
            <a href="{{ route('pengajuan.exportPDF', request()->query()) }}" class="btn btn-ghost btn-sm" target="_blank" id="btn-export-pdf" title="Export PDF">
                <i class="bi bi-filetype-pdf" style="color: var(--accent-primary);"></i> PDF
            </a>
            <a href="{{ route('pengajuan.create') }}" class="btn btn-primary" id="btn-create">
                <i class="bi bi-plus-lg"></i> Buat Pengajuan
            </a>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="glass-card animate-in" id="main-table-card">
        <div class="table-wrapper">
            <table class="data-table" id="table-pengajuan">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Pemohon</th>
                        <th>Departemen</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $i => $p)
                        <tr>
                            <td>{{ $pengajuans->firstItem() + $i }}</td>
                            <td>{{ $p->nama_pemohon }}</td>
                            <td>{{ $p->departemen }}</td>
                            <td>{{ $p->nama_barang }}</td>
                            <td>{{ $p->jumlah }}</td>
                            <td>
                                <span class="badge badge-priority-{{ strtolower($p->prioritas ?? 'sedang') }}">
                                    {{ $p->prioritas ?? 'Sedang' }}
                                </span>
                            </td>
                            <td>
                                @if($p->status === 'Pending')
                                    <span class="badge badge-pending"><i class="bi bi-clock"></i> Pending</span>
                                @elseif($p->status === 'Disetujui')
                                    <span class="badge badge-approved"><i class="bi bi-check-lg"></i> Disetujui</span>
                                @else
                                    <span class="badge badge-rejected"><i class="bi bi-x-lg"></i> Ditolak</span>
                                @endif
                            </td>
                            <td>{{ $p->created_at->format('d M Y') }}</td>
                            <td>
                                <div class="action-group" style="justify-content: center;">
                                    <a href="{{ route('pengajuan.show', $p->id) }}" class="btn btn-ghost btn-sm" title="Detail" id="btn-view-{{ $p->id }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('pengajuan.edit', $p->id) }}" class="btn btn-warning btn-sm" title="Edit" id="btn-edit-{{ $p->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </a>

                                    @if(auth()->user()->isAdmin())
                                        {{-- Status Dropdown (Admin Only) --}}
                                        <div class="status-dropdown" id="status-dd-{{ $p->id }}">
                                            <button type="button" class="btn btn-success btn-sm" title="Ubah Status" id="btn-status-{{ $p->id }}" onclick="toggleStatusDropdown({{ $p->id }})">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <div class="status-dropdown-content">
                                                <form method="POST" action="{{ route('pengajuan.updateStatus', $p->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Pending">
                                                    <button type="submit" class="status-dropdown-item">
                                                        <i class="bi bi-clock" style="color: #fbbf24;"></i> Pending
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('pengajuan.updateStatus', $p->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Disetujui">
                                                    <button type="submit" class="status-dropdown-item">
                                                        <i class="bi bi-check-circle" style="color: #34d399;"></i> Disetujui
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('pengajuan.updateStatus', $p->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="Ditolak">
                                                    <button type="submit" class="status-dropdown-item">
                                                        <i class="bi bi-x-circle" style="color: #ff6b5e;"></i> Ditolak
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Delete (Admin Only) --}}
                                        <button class="btn btn-danger btn-sm" title="Hapus" onclick="confirmDelete({{ $p->id }})" id="btn-delete-{{ $p->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $p->id }}" action="{{ route('pengajuan.destroy', $p->id) }}" method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h3>Tidak Ada Data</h3>
                                    <p>Belum ada pengajuan ATK untuk periode yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengajuans->hasPages())
            <div class="pagination-wrapper" style="padding: 16px 22px;">
                <div class="pagination-info">
                    Menampilkan {{ $pengajuans->firstItem() }} - {{ $pengajuans->lastItem() }} dari {{ $pengajuans->total() }} data
                </div>
                <div class="pagination-links">
                    @if($pengajuans->onFirstPage())
                        <span class="disabled"><i class="bi bi-chevron-left"></i></span>
                    @else
                        <a href="{{ $pengajuans->previousPageUrl() }}"><i class="bi bi-chevron-left"></i></a>
                    @endif

                    @foreach($pengajuans->getUrlRange(1, $pengajuans->lastPage()) as $page => $url)
                        @if($page == $pengajuans->currentPage())
                            <span class="active">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach

                    @if($pengajuans->hasMorePages())
                        <a href="{{ $pengajuans->nextPageUrl() }}"><i class="bi bi-chevron-right"></i></a>
                    @else
                        <span class="disabled"><i class="bi bi-chevron-right"></i></span>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal (Admin Only) --}}
    @if(auth()->user()->isAdmin())
        <div class="modal-overlay" id="deleteModal">
            <div class="modal-box">
                <div class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill" style="color: var(--accent-rose); margin-right: 8px;"></i>
                    Konfirmasi Hapus
                </div>
                <div class="modal-text">
                    Apakah Anda yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.
                </div>
                <div class="modal-actions">
                    <button class="btn btn-ghost" onclick="closeDeleteModal()" id="btn-cancel-delete">Batal</button>
                    <button class="btn btn-danger" id="btn-confirm-delete" onclick="submitDelete()">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </div>
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

    // Status dropdown click toggle
    function toggleStatusDropdown(id) {
        const dd = document.getElementById('status-dd-' + id);
        const isActive = dd.classList.contains('active');

        // Close all other open dropdowns first
        document.querySelectorAll('.status-dropdown.active').forEach(el => {
            el.classList.remove('active');
        });

        // Toggle this one
        if (!isActive) {
            dd.classList.add('active');
        }
    }

    // Close status dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.status-dropdown')) {
            document.querySelectorAll('.status-dropdown.active').forEach(el => {
                el.classList.remove('active');
            });
        }
    });

    @if(auth()->user()->isAdmin())
        let deleteId = null;

        function confirmDelete(id) {
            deleteId = id;
            document.getElementById('deleteModal').classList.add('show');
        }

        function closeDeleteModal() {
            deleteId = null;
            document.getElementById('deleteModal').classList.remove('show');
        }

        function submitDelete() {
            if (deleteId) {
                document.getElementById('delete-form-' + deleteId).submit();
            }
        }

        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) closeDeleteModal();
        });
    @endif
</script>
@endsection
