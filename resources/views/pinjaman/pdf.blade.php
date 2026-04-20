<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $periodLabel }}</title>
    <link rel="stylesheet" href="{{ asset('css/print.css') }}">
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>

    <div class="print-header">
        <h1>SISTEM PENGAJUAN ATK</h1>
        <h2>Data Pinjaman Barang Antar Departemen</h2>
        <div class="period">{{ $periodLabel }}</div>
        <div class="date">Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="value">{{ $totalPinjaman }}</div>
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

    <div class="section-title">Detail Data Pinjaman ({{ $pinjamans->count() }} data)</div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                @if($tab === 'masuk')
                    <th>Peminjam</th>
                    <th>Dept. Asal</th>
                @else
                    <th>Peminjam</th>
                    <th>Tujuan Dept.</th>
                @endif
                <th>Barang</th>
                <th class="text-center">Jml</th>
                <th>Tgl Pinjam</th>
                <th>Tgl Kembali</th>
                <th>Status</th>
                <th>Alasan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pinjamans as $i => $p)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $p->peminjam_nama }}</td>
                    <td>{{ $tab === 'masuk' ? $p->peminjam_departemen : $p->tujuan_departemen }}</td>
                    <td>{{ $p->nama_barang }}</td>
                    <td class="text-center text-bold">{{ $p->jumlah }}</td>
                    <td>{{ $p->tanggal_pinjam->format('d/m/Y') }}</td>
                    <td>{{ $p->tanggal_kembali ? $p->tanggal_kembali->format('d/m/Y') : '-' }}</td>
                    <td>
                        @if($p->status === 'Pending')
                            <span class="badge badge-pending">Pending</span>
                        @elseif($p->status === 'Disetujui')
                            <span class="badge badge-approved">Disetujui</span>
                        @else
                            <span class="badge badge-rejected">Ditolak</span>
                        @endif
                    </td>
                    <td class="text-small">{{ Str::limit($p->alasan ?? '-', 50) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="empty-cell">Tidak ada data</td>
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
