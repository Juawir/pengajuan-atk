@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan statistik pengajuan ATK')

@section('content')
    {{-- Stat Cards --}}
    <div class="grid-4" style="margin-bottom: 28px;">
        <div class="glass-card stat-card indigo animate-in" id="card-total">
            <div class="stat-icon indigo">
                <i class="bi bi-stack"></i>
            </div>
            <div class="stat-value">{{ $totalPengajuan }}</div>
            <div class="stat-label">Total Pengajuan</div>
        </div>

        <div class="glass-card stat-card amber animate-in" id="card-pending">
            <div class="stat-icon amber">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-value">{{ $totalPending }}</div>
            <div class="stat-label">Menunggu Persetujuan</div>
        </div>

        <div class="glass-card stat-card emerald animate-in" id="card-approved">
            <div class="stat-icon emerald">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-value">{{ $totalDisetujui }}</div>
            <div class="stat-label">Disetujui</div>
        </div>

        <div class="glass-card stat-card rose animate-in" id="card-rejected">
            <div class="stat-icon rose">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-value">{{ $totalDitolak }}</div>
            <div class="stat-label">Ditolak</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid-2" style="margin-bottom: 28px;">
        {{-- Status Distribution Chart --}}
        <div class="glass-card animate-in" id="chart-status-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-pie-chart-fill" style="color: var(--accent-primary); margin-right: 8px;"></i>
                    Distribusi Status
                </div>
            </div>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        {{-- Department Chart --}}
        <div class="glass-card animate-in" id="chart-dept-card">
            <div class="card-header">
                <div class="card-header-title">
                    <i class="bi bi-building" style="color: var(--accent-cyan); margin-right: 8px;"></i>
                    Pengajuan per Departemen
                </div>
            </div>
            <div class="chart-container">
                <canvas id="deptChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Recent Pengajuan Table --}}
    <div class="glass-card animate-in" id="recent-table-card">
        <div class="card-header">
            <div class="card-header-title">
                <i class="bi bi-clock-history" style="color: var(--accent-amber); margin-right: 8px;"></i>
                Pengajuan Terbaru
            </div>
            <a href="{{ route('pengajuan.index') }}" class="btn btn-ghost btn-sm" id="btn-view-all">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table" id="table-recent">
                <thead>
                    <tr>
                        <th>Pemohon</th>
                        <th>Departemen</th>
                        <th>Barang</th>
                        <th>Jumlah</th>
                        <th>Prioritas</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPengajuan as $p)
                        <tr>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h3>Belum Ada Data</h3>
                                    <p>Belum ada pengajuan ATK yang masuk</p>
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
    // Chart.js global config for dark theme
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(230, 33, 23, 0.08)';

    // Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Disetujui', 'Ditolak'],
            datasets: [{
                data: [{{ $totalPending }}, {{ $totalDisetujui }}, {{ $totalDitolak }}],
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
                hoverBorderWidth: 3,
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

    // Department Bar Chart
    const deptCtx = document.getElementById('deptChart').getContext('2d');
    const deptLabels = @json($deptLabels);
    const deptValues = @json($deptValues);

    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: deptLabels,
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: deptValues,
                backgroundColor: [
                    'rgba(230, 33, 23, 0.6)',
                    'rgba(27, 38, 59, 0.6)',
                    'rgba(13, 229, 255, 0.6)',
                    'rgba(16, 185, 129, 0.6)',
                    'rgba(245, 158, 11, 0.6)',
                    'rgba(230, 33, 23, 0.6)'
                ],
                borderColor: [
                    'rgba(230, 33, 23, 1)',
                    'rgba(27, 38, 59, 1)',
                    'rgba(13, 229, 255, 1)',
                    'rgba(16, 185, 129, 1)',
                    'rgba(245, 158, 11, 1)',
                    'rgba(230, 33, 23, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        font: { family: 'Inter', size: 11 }
                    },
                    grid: { color: 'rgba(230, 33, 23, 0.06)' }
                },
                x: {
                    ticks: {
                        font: { family: 'Inter', size: 11 }
                    },
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endsection
