@extends('layouts.app')

@section('title', 'Laporan')
@section('subtitle', 'Analisis & statistik lengkap pengajuan ATK')

@section('content')
    {{-- Filter Periode — Multi-month --}}
    <div class="glass-card animate-in" style="padding: 18px 22px; margin-bottom: 24px;" id="filter-card">
        <form method="GET" action="{{ route('laporan.index') }}" id="form-filter-laporan">
            <div style="display: flex; align-items: center; gap: 14px; flex-wrap: wrap; margin-bottom: 14px;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <i class="bi bi-funnel-fill" style="color: var(--accent-primary); font-size: 16px;"></i>
                    <span style="font-size: 14px; font-weight: 600; color: var(--text-secondary);">Filter Periode:</span>
                </div>
                <select name="tahun" class="form-control" style="width: auto; min-width: 120px;" id="filter-tahun">
                    @foreach($tahunList as $t)
                        <option value="{{ $t }}" {{ (int)$tahun === (int)$t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary btn-sm" id="btn-filter-apply">
                    <i class="bi bi-search"></i> Terapkan
                </button>
                <a href="{{ route('laporan.index') }}" class="btn btn-ghost btn-sm" id="btn-filter-reset">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>

            {{-- Multi-month Selector --}}
            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                <span style="font-size: 12px; font-weight: 600; color: var(--text-muted); margin-right: 6px; font-family: 'Manrope', sans-serif; text-transform: uppercase; letter-spacing: 0.5px;">Bulan:</span>
                @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $i => $nama)
                    @php $monthNum = $i + 1; $isActive = in_array($monthNum, array_map('intval', $selectedMonths)); @endphp
                    <label style="display: inline-flex; align-items: center; gap: 4px; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.2s; border: 1px solid {{ $isActive ? 'var(--accent-primary)' : 'var(--border-color)' }}; background: {{ $isActive ? 'rgba(230,33,23,0.15)' : 'transparent' }}; color: {{ $isActive ? 'var(--accent-primary)' : 'var(--text-secondary)' }};">
                        <input type="checkbox" name="bulan[]" value="{{ $monthNum }}" {{ $isActive ? 'checked' : '' }} style="display: none;">
                        {{ substr($nama, 0, 3) }}
                    </label>
                @endforeach
            </div>
        </form>
    </div>

    {{-- Export Buttons --}}
    <div style="display: flex; gap: 10px; margin-bottom: 24px; justify-content: flex-end;" id="export-actions">
        <a href="{{ route('laporan.exportCSV', request()->query()) }}" class="btn btn-ghost btn-sm" id="btn-export-csv">
            <i class="bi bi-filetype-csv" style="color: var(--accent-emerald);"></i> Export CSV
        </a>
        <a href="{{ route('laporan.exportPDF', request()->query()) }}" class="btn btn-ghost btn-sm" target="_blank" id="btn-export-pdf">
            <i class="bi bi-filetype-pdf" style="color: var(--accent-primary);"></i> Export PDF
        </a>
    </div>

    {{-- Stat Cards --}}
    <div class="grid-4" style="margin-bottom: 24px;">
        <div class="glass-card stat-card primary animate-in" id="stat-total">
            <div class="stat-icon primary"><i class="bi bi-stack"></i></div>
            <div class="stat-value">{{ $totalPengajuan }}</div>
            <div class="stat-label">Total Pengajuan</div>
        </div>
        <div class="glass-card stat-card emerald animate-in" id="stat-approved">
            <div class="stat-icon emerald"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-value">{{ $totalDisetujui }}</div>
            <div class="stat-label">Disetujui</div>
        </div>
        <div class="glass-card stat-card amber animate-in" id="stat-pending">
            <div class="stat-icon amber"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-value">{{ $totalPending }}</div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="glass-card stat-card rose animate-in" id="stat-rejected">
            <div class="stat-icon rose"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-value">{{ $totalDitolak }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>

    {{-- Total Barang Highlight --}}
    <div class="glass-card animate-in" style="padding: 20px 24px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px;" id="total-barang-card">
        <div style="width: 52px; height: 52px; border-radius: var(--radius-md); background: linear-gradient(135deg, rgba(13, 229, 255, 0.2), rgba(230, 33, 23, 0.2)); display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--accent-cyan);">
            <i class="bi bi-boxes"></i>
        </div>
        <div>
            <div style="font-size: 28px; font-weight: 800; color: var(--text-primary); letter-spacing: -1px;">{{ number_format($totalBarang) }}</div>
            <div style="font-size: 13px; color: var(--text-muted); font-weight: 500;">Total Barang yang Diajukan</div>
        </div>
        <div style="margin-left: auto; display: flex; gap: 24px;">
            @foreach($perPrioritas as $prio => $jumlah)
                <div style="text-align: center;">
                    <div style="font-size: 20px; font-weight: 700; color: var(--text-primary);">{{ $jumlah }}</div>
                    <span class="badge badge-priority-{{ strtolower($prio ?: 'sedang') }}">{{ $prio ?: 'Sedang' }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid-2" style="margin-bottom: 24px;">
        <div class="glass-card animate-in" id="chart-trend-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-graph-up" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Trend Pengajuan (12 Bulan Terakhir)
                </div>
            </div>
            <div class="chart-container">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        <div class="glass-card animate-in" id="chart-status-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pie-chart-fill" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                    Distribusi Status
                </div>
            </div>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Departemen & Top Barang --}}
    <div class="grid-2" style="margin-bottom: 24px;">
        <div class="glass-card animate-in" id="dept-table-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-building" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                    Rekap per Departemen
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="table-departemen">
                    <thead>
                        <tr>
                            <th>Departemen</th>
                            <th style="text-align: center;">Pengajuan</th>
                            <th style="text-align: center;">Total Barang</th>
                            <th style="text-align: center;">Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perDepartemen as $dept)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <div style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-primary);"></div>
                                        {{ $dept->departemen }}
                                    </div>
                                </td>
                                <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $dept->total }}</td>
                                <td style="text-align: center;">{{ $dept->total_barang }}</td>
                                <td style="text-align: center;">
                                    <div style="display: flex; align-items: center; gap: 8px; justify-content: center;">
                                        <div style="width: 60px; height: 6px; border-radius: 3px; background: rgba(230,33,23,0.1); overflow: hidden;">
                                            <div style="width: {{ $totalPengajuan > 0 ? round(($dept->total / $totalPengajuan) * 100) : 0 }}%; height: 100%; background: var(--gradient-primary); border-radius: 3px;"></div>
                                        </div>
                                        <span style="font-size: 12px; font-weight: 600; color: var(--text-primary);">{{ $totalPengajuan > 0 ? round(($dept->total / $totalPengajuan) * 100) : 0 }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="glass-card animate-in" id="top-barang-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-trophy-fill" style="color: var(--accent-amber); margin-right: 8px;"></i>
                    Top 10 Barang Paling Sering Diminta
                </div>
            </div>
            <div class="table-wrapper">
                <table class="data-table" id="table-top-barang">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Barang</th>
                            <th style="text-align: center;">Frekuensi</th>
                            <th style="text-align: center;">Total Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topBarang as $i => $item)
                            <tr>
                                <td>
                                    @if($i === 0) <span style="font-size: 16px;">🥇</span>
                                    @elseif($i === 1) <span style="font-size: 16px;">🥈</span>
                                    @elseif($i === 2) <span style="font-size: 16px;">🥉</span>
                                    @else {{ $i + 1 }}
                                    @endif
                                </td>
                                <td>{{ $item->nama_barang }}</td>
                                <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $item->frekuensi }}x</td>
                                <td style="text-align: center;">{{ $item->total_jumlah }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" style="text-align: center; color: var(--text-muted); padding: 30px;">Tidak ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Data Table --}}
    <div class="glass-card animate-in" id="detail-table-card">
        <div class="card-header">
            <div class="card-header-title">
                <i class="bi bi-table" style="color: var(--accent-emerald); margin-right: 8px;"></i>
                Detail Data Pengajuan
            </div>
            <span style="font-size: 13px; color: var(--text-muted);">{{ $pengajuans->count() }} data</span>
        </div>
        <div class="table-wrapper" style="max-height: 420px; overflow-y: auto;">
            <table class="data-table" id="table-detail-laporan">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pemohon</th>
                        <th>Departemen</th>
                        <th>Barang</th>
                        <th style="text-align: center;">Jumlah</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $i => $p)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $p->nama_pemohon }}</td>
                            <td>{{ $p->departemen }}</td>
                            <td>{{ $p->nama_barang }}</td>
                            <td style="text-align: center; font-weight: 600; color: var(--text-primary);">{{ $p->jumlah }}</td>
                            <td>
                                <span class="badge badge-priority-{{ strtolower($p->prioritas ?? 'sedang') }}">{{ $p->prioritas ?? 'Sedang' }}</span>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h3>Tidak Ada Data</h3>
                                    <p>Tidak ada pengajuan untuk periode yang dipilih.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Month toggle chips
    document.querySelectorAll('#filter-card label').forEach(label => {
        label.addEventListener('click', function() {
            const cb = this.querySelector('input[type="checkbox"]');
            // Toggle will happen naturally, update visual after
            setTimeout(() => {
                if (cb.checked) {
                    this.style.border = '1px solid var(--accent-primary)';
                    this.style.background = 'rgba(230,33,23,0.15)';
                    this.style.color = 'var(--accent-primary)';
                } else {
                    this.style.border = '1px solid var(--border-color)';
                    this.style.background = 'transparent';
                    this.style.color = 'var(--text-secondary)';
                }
            }, 10);
        });
    });

    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(230, 33, 23, 0.08)';

    // Trend Line Chart
    const trendCtx = document.getElementById('trendChart').getContext('2d');
    const gradient = trendCtx.createLinearGradient(0, 0, 0, 280);
    gradient.addColorStop(0, 'rgba(230, 33, 23, 0.3)');
    gradient.addColorStop(1, 'rgba(230, 33, 23, 0.01)');

    new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: @json($trendLabels),
            datasets: [{
                label: 'Pengajuan',
                data: @json($trendValues),
                borderColor: '#E62117',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#E62117',
                pointBorderColor: '#0D1117',
                pointBorderWidth: 2,
                pointHoverRadius: 8,
                pointHoverBackgroundColor: '#ff6b5e'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } },
                    grid: { color: 'rgba(230, 33, 23, 0.06)' }
                },
                x: {
                    ticks: { font: { family: 'Inter', size: 10 } },
                    grid: { display: false }
                }
            }
        }
    });

    // Status Doughnut
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                data: [
                    {{ $perStatus['Pending'] ?? 0 }},
                    {{ $perStatus['Disetujui'] ?? 0 }},
                    {{ $perStatus['Ditolak'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(251, 191, 36, 0.8)',
                    'rgba(52, 211, 153, 0.8)',
                    'rgba(230, 33, 23, 0.8)'
                ],
                borderColor: [
                    'rgba(251, 191, 36, 1)',
                    'rgba(52, 211, 153, 1)',
                    'rgba(230, 33, 23, 1)'
                ],
                borderWidth: 2,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        pointStyleWidth: 12,
                        font: { family: 'Inter', size: 12, weight: '500' }
                    }
                }
            }
        }
    });
</script>
@endsection
