<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Halaman laporan lengkap (admin only).
     */
    public function index(Request $request)
    {
        // Filter periode — sekarang support multi-bulan
        $selectedMonths = $request->get('bulan', []); // array of months
        $tahun = $request->get('tahun', now()->year);

        // Jika bulan datang sebagai single value (backward compat), jadikan array
        if (!is_array($selectedMonths)) {
            $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        }

        // Query pengajuan berdasarkan periode
        $query = Pengajuan::query();

        if (!empty($selectedMonths) && $request->filled('tahun')) {
            $query->whereYear('created_at', $tahun)
                  ->whereIn(DB::raw('MONTH(created_at)'), $selectedMonths);
        } elseif ($request->filled('tahun')) {
            $query->whereYear('created_at', $tahun);
        }

        // Statistik umum
        $totalPengajuan = $query->count();
        $totalPending   = (clone $query)->where('status', 'Pending')->count();
        $totalDisetujui = (clone $query)->where('status', 'Disetujui')->count();
        $totalDitolak   = (clone $query)->where('status', 'Ditolak')->count();
        $totalBarang    = (clone $query)->sum('jumlah');

        // Data per departemen
        $perDepartemen = (clone $query)
            ->select('departemen', DB::raw('COUNT(*) as total'), DB::raw('SUM(jumlah) as total_barang'))
            ->groupBy('departemen')
            ->orderByDesc('total')
            ->get();

        // Data per status
        $perStatus = (clone $query)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Data per prioritas
        $perPrioritas = (clone $query)
            ->select('prioritas', DB::raw('COUNT(*) as total'))
            ->groupBy('prioritas')
            ->get()
            ->pluck('total', 'prioritas')
            ->toArray();

        // Data trend bulanan (12 bulan terakhir)
        $trendBulanan = Pengajuan::select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('YEAR(created_at) as tahun'),
                DB::raw('COUNT(*) as total')
            )
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get();

        $trendLabels = [];
        $trendValues = [];
        $namaBulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        foreach ($trendBulanan as $item) {
            $trendLabels[] = $namaBulan[$item->bulan] . ' ' . $item->tahun;
            $trendValues[] = $item->total;
        }

        // Barang paling sering diminta
        $topBarang = (clone $query)
            ->select('nama_barang', DB::raw('COUNT(*) as frekuensi'), DB::raw('SUM(jumlah) as total_jumlah'))
            ->groupBy('nama_barang')
            ->orderByDesc('frekuensi')
            ->limit(10)
            ->get();

        // Tabel semua data sesuai filter
        $pengajuans = (clone $query)->latest()->get();

        // Daftar tahun yang tersedia
        $tahunList = Pengajuan::selectRaw('YEAR(created_at) as tahun')
            ->distinct()
            ->orderByDesc('tahun')
            ->pluck('tahun');

        return view('laporan.index', compact(
            'totalPengajuan', 'totalPending', 'totalDisetujui', 'totalDitolak', 'totalBarang',
            'perDepartemen', 'perStatus', 'perPrioritas',
            'trendLabels', 'trendValues',
            'topBarang', 'pengajuans',
            'selectedMonths', 'tahun', 'tahunList'
        ));
    }

    /**
     * Export ke CSV.
     */
    public function exportCSV(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $pengajuans = $query->latest()->get();

        $selectedMonths = $request->get('bulan', []);
        if (!is_array($selectedMonths)) {
            $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        }
        $tahun = $request->get('tahun', now()->year);

        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $periodLabel = !empty($selectedMonths)
            ? implode('-', array_map(fn($m) => $monthNames[(int)$m], $selectedMonths)) . '_' . $tahun
            : 'semua_' . $tahun;

        $filename = 'laporan_atk_' . $periodLabel . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($pengajuans) {
            $file = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, ['No', 'Pemohon', 'Departemen', 'Nama Barang', 'Jumlah', 'Prioritas', 'Status', 'Keterangan', 'Tanggal']);

            foreach ($pengajuans as $i => $p) {
                fputcsv($file, [
                    $i + 1,
                    $p->nama_pemohon,
                    $p->departemen,
                    $p->nama_barang,
                    $p->jumlah,
                    $p->prioritas ?? 'Sedang',
                    $p->status,
                    $p->keterangan ?? '-',
                    $p->created_at->format('d/m/Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export ke PDF (via HTML render + browser print).
     */
    public function exportPDF(Request $request)
    {
        $query = $this->buildFilteredQuery($request);
        $pengajuans = $query->latest()->get();

        $selectedMonths = $request->get('bulan', []);
        if (!is_array($selectedMonths)) {
            $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        }
        $tahun = $request->get('tahun', now()->year);

        // Statistik
        $totalPengajuan = $pengajuans->count();
        $totalPending   = $pengajuans->where('status', 'Pending')->count();
        $totalDisetujui = $pengajuans->where('status', 'Disetujui')->count();
        $totalDitolak   = $pengajuans->where('status', 'Ditolak')->count();
        $totalBarang    = $pengajuans->sum('jumlah');

        $perDepartemen = $pengajuans->groupBy('departemen')->map(function ($items, $dept) {
            return (object)[
                'departemen' => $dept,
                'total' => $items->count(),
                'total_barang' => $items->sum('jumlah'),
            ];
        })->sortByDesc('total')->values();

        $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $periodLabel = !empty($selectedMonths)
            ? implode(', ', array_map(fn($m) => $monthNames[(int)$m], $selectedMonths)) . ' ' . $tahun
            : 'Tahun ' . $tahun;

        return view('laporan.pdf', compact(
            'pengajuans', 'totalPengajuan', 'totalPending', 'totalDisetujui', 'totalDitolak',
            'totalBarang', 'perDepartemen', 'periodLabel'
        ));
    }

    /**
     * Build filtered query based on request.
     */
    private function buildFilteredQuery(Request $request)
    {
        $selectedMonths = $request->get('bulan', []);
        $tahun = $request->get('tahun', now()->year);

        if (!is_array($selectedMonths)) {
            $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        }

        $query = Pengajuan::query();

        if (!empty($selectedMonths) && $request->filled('tahun')) {
            $query->whereYear('created_at', $tahun)
                  ->whereIn(DB::raw('MONTH(created_at)'), $selectedMonths);
        } elseif ($request->filled('tahun')) {
            $query->whereYear('created_at', $tahun);
        }

        return $query;
    }
}
