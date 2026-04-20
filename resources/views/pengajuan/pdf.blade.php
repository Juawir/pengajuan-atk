<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengajuan ATK - {{ $periodLabel }}</title>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>

    <div class="print-header">
        <h1>SISTEM PENGAJUAN ATK</h1>
        <h2>Data Pengajuan Alat Tulis Kantor</h2>
        <div class="period">{{ $periodLabel }}</div>
        <div class="date">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="value">{{ $totalPengajuan }}</div>
            <div class="label">Total</div>
        </div>
        <div class="stat-box">
            <div class="value stat-value-approved">{{ $totalDisetujui }}</div>
            <div class="label">Disetujui</div>
        </div>
        <div class="stat-box">
            <div class="value stat-value-pending">{{ $totalPending }}</div>
            <div class="label">Pending</div>
        </div>
        <div class="stat-box">
            <div class="value stat-value-rejected">{{ $totalDitolak }}</div>
            <div class="label">Ditolak</div>
        </div>
        <div class="stat-box">
            <div class="value stat-value-items">{{ number_format($totalBarang) }}</div>
            <div class="label">Total Barang</div>
        </div>
    </div>

    @if($perDepartemen->count() > 0)
        <div class="section-title">Rekap per Departemen</div>
        <table>
            <thead>
                <tr>
                    <th>Departemen</th>
                    <th class="text-center">Pengajuan</th>
                    <th class="text-center">Total Barang</th>
                    <th class="text-center">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @foreach($perDepartemen as $dept)
                    <tr>
                        <td class="text-bold">{{ $dept->departemen }}</td>
                        <td class="text-center">{{ $dept->total }}</td>
                        <td class="text-center">{{ $dept->total_barang }}</td>
                        <td class="text-center">{{ $totalPengajuan > 0 ? round(($dept->total / $totalPengajuan) * 100) : 0 }}%</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="section-title">Detail Data Pengajuan ({{ $pengajuans->count() }} data)</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Pemohon</th>
                <th>Departemen</th>
                <th>Barang</th>
                <th class="text-center">Jml</th>
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
                    <td class="text-center text-bold">{{ $p->jumlah }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower($p->prioritas ?? 'sedang') }}">{{ $p->prioritas ?? 'Sedang' }}</span>
                    </td>
                    <td>
                        @if($p->status === 'Pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($p->status === 'Disetujui')
                            <span class="badge badge-approved">Disetujui</span>
                        @else
                            <span class="badge badge-rejected">Ditolak</span>
                        @endif
                    </td>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="empty-cell">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="print-footer">
        <p>Dokumen ini digenerate otomatis oleh Sistem Pengajuan ATK</p>
        <p>© {{ date('Y') }} ATK System — Management Portal</p>
    </div>
</body>
</html>
