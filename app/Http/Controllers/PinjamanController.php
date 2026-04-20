<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pinjaman;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\DB;

class PinjamanController extends Controller
{
    /**
     * Daftar pinjaman: Keluar (yang diajukan) & Masuk (yang ditujukan ke departemen user).
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'keluar');

        // Multi-month filter
        $selectedMonths = $request->get('bulan', []);
        if (!is_array($selectedMonths)) {
            $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        }
        $tahun = $request->get('tahun', now()->year);

        // Pinjaman Keluar = yang diajukan oleh user / departemen user
        $keluarQuery = Pinjaman::query();
        if ($user->isUser()) {
            $keluarQuery->where('peminjam_departemen', $user->departemen);
        }
        if ($request->filled('status_keluar')) {
            $keluarQuery->where('status', $request->status_keluar);
        }
        if (!empty($selectedMonths)) {
            $keluarQuery->whereYear('created_at', $tahun)
                        ->whereIn(DB::raw('MONTH(created_at)'), $selectedMonths);
        }
        $pinjamanKeluar = $keluarQuery->latest()->get();

        // Pinjaman Masuk = yang ditujukan ke departemen user
        $masukQuery = Pinjaman::query();
        if ($user->isUser()) {
            $masukQuery->where('tujuan_departemen', $user->departemen);
        }
        if ($request->filled('status_masuk')) {
            $masukQuery->where('status', $request->status_masuk);
        }
        if (!empty($selectedMonths)) {
            $masukQuery->whereYear('created_at', $tahun)
                       ->whereIn(DB::raw('MONTH(created_at)'), $selectedMonths);
        }
        $pinjamanMasuk = $masukQuery->latest()->get();

        // Daftar departemen untuk dropdown
        $departemenList = User::whereNotNull('departemen')
            ->select('departemen')
            ->distinct()
            ->pluck('departemen');

        $tahunList = Pinjaman::selectRaw('YEAR(created_at) as tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('pinjaman.index', compact('pinjamanKeluar', 'pinjamanMasuk', 'tab', 'departemenList', 'selectedMonths', 'tahun', 'tahunList'));
    }

    /**
     * Form buat pinjaman baru.
     */
    public function create()
    {
        $user = auth()->user();

        // Ambil daftar departemen yang bisa dituju (selain departemen sendiri)
        $departemenList = User::whereNotNull('departemen')
            ->where('departemen', '!=', $user->departemen ?? '')
            ->select('departemen')
            ->distinct()
            ->pluck('departemen');

        return view('pinjaman.create', compact('departemenList'));
    }

    /**
     * Simpan pinjaman baru.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tujuan_departemen' => 'required|string|max:255',
            'nama_barang'       => 'required|string|max:255',
            'jumlah'            => 'required|integer|min:1',
            'alasan'            => 'nullable|string',
            'tanggal_pinjam'    => 'required|date',
            'tanggal_kembali'   => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        // Pastikan tidak pinjam ke departemen sendiri
        $peminjamDept = $user->isAdmin() ? ($request->peminjam_departemen ?? 'Admin') : $user->departemen;
        if ($peminjamDept === $request->tujuan_departemen) {
            return back()->withErrors(['tujuan_departemen' => 'Tidak bisa meminjam ke departemen sendiri.'])->withInput();
        }

        $pinjaman = Pinjaman::create([
            'peminjam_user_id'   => $user->id,
            'peminjam_nama'      => $user->name,
            'peminjam_departemen' => $peminjamDept,
            'tujuan_departemen'  => $request->tujuan_departemen,
            'nama_barang'        => $request->nama_barang,
            'jumlah'             => $request->jumlah,
            'alasan'             => $request->alasan,
            'tanggal_pinjam'     => $request->tanggal_pinjam,
            'tanggal_kembali'    => $request->tanggal_kembali,
            'status'             => 'Pending',
        ]);

        // Kirim notifikasi ke user departemen tujuan
        $targetUsers = User::where('departemen', $request->tujuan_departemen)->get();
        foreach ($targetUsers as $targetUser) {
            Notifikasi::create([
                'user_id'      => $targetUser->id,
                'type'         => 'pengajuan_baru',
                'title'        => 'Pinjaman Baru Masuk',
                'message'      => $user->name . ' (Dept. ' . $peminjamDept . ') meminjam ' . $pinjaman->nama_barang . ' (' . $pinjaman->jumlah . ' unit)',
                'pengajuan_id' => null,
            ]);
        }

        return redirect()->route('pinjaman.index', ['tab' => 'keluar'])->with('success', 'Pengajuan pinjaman berhasil dikirim ke departemen ' . $request->tujuan_departemen . '!');
    }

    /**
     * Detail pinjaman.
     */
    public function show(Pinjaman $pinjaman)
    {
        $user = auth()->user();

        // Otorisasi: boleh lihat jika admin, dept peminjam, atau dept tujuan
        if ($user->isUser()) {
            if ($pinjaman->peminjam_departemen !== $user->departemen && $pinjaman->tujuan_departemen !== $user->departemen) {
                abort(403, 'Anda tidak memiliki akses ke pinjaman ini.');
            }
        }

        return view('pinjaman.show', compact('pinjaman'));
    }

    /**
     * Update status pinjaman (hanya oleh departemen tujuan atau admin).
     */
    public function updateStatus(Request $request, Pinjaman $pinjaman)
    {
        $user = auth()->user();

        // Otorisasi: hanya dept tujuan atau admin
        if ($user->isUser() && $pinjaman->tujuan_departemen !== $user->departemen) {
            abort(403, 'Hanya departemen tujuan yang dapat merespon pinjaman ini.');
        }

        $request->validate([
            'status'           => 'required|in:Pending,Disetujui,Ditolak',
            'catatan_response' => 'nullable|string|max:500',
        ]);

        $pinjaman->update([
            'status'            => $request->status,
            'catatan_response'  => $request->catatan_response,
            'responder_user_id' => $user->id,
        ]);

        // Notifikasi ke peminjam
        if ($pinjaman->peminjam_user_id) {
            $statusMap = [
                'Disetujui' => ['type' => 'status_disetujui', 'title' => 'Pinjaman Disetujui'],
                'Ditolak'   => ['type' => 'status_ditolak',   'title' => 'Pinjaman Ditolak'],
                'Pending'   => ['type' => 'status_pending',   'title' => 'Pinjaman Kembali Pending'],
            ];

            $info = $statusMap[$request->status];

            Notifikasi::create([
                'user_id'      => $pinjaman->peminjam_user_id,
                'type'         => $info['type'],
                'title'        => $info['title'],
                'message'      => 'Pinjaman ' . $pinjaman->nama_barang . ' ke Dept. ' . $pinjaman->tujuan_departemen . ' telah ' . strtolower($request->status) . ' oleh ' . $user->name,
                'pengajuan_id' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Status pinjaman berhasil diubah menjadi ' . $request->status . '!');
    }

    /**
     * Export data pinjaman ke CSV.
     */
    public function exportCSV(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'keluar');
        $pinjamans = $this->getExportData($user, $tab, $request);

        $typeLabel = $tab === 'masuk' ? 'masuk' : 'keluar';
        $deptLabel = $user->isAdmin() ? 'semua' : strtolower($user->departemen);
        $filename = 'pinjaman_' . $typeLabel . '_' . $deptLabel . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($pinjamans, $tab) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($tab === 'masuk') {
                fputcsv($file, ['No', 'Peminjam', 'Dept. Asal', 'Barang', 'Jumlah', 'Tgl Pinjam', 'Tgl Kembali', 'Status', 'Alasan']);
            } else {
                fputcsv($file, ['No', 'Tujuan Dept.', 'Barang', 'Jumlah', 'Tgl Pinjam', 'Tgl Kembali', 'Status', 'Alasan']);
            }

            foreach ($pinjamans as $i => $p) {
                if ($tab === 'masuk') {
                    fputcsv($file, [
                        $i + 1,
                        $p->peminjam_nama,
                        $p->peminjam_departemen,
                        $p->nama_barang,
                        $p->jumlah,
                        $p->tanggal_pinjam->format('d/m/Y'),
                        $p->tanggal_kembali ? $p->tanggal_kembali->format('d/m/Y') : '-',
                        $p->status,
                        $p->alasan ?? '-',
                    ]);
                } else {
                    fputcsv($file, [
                        $i + 1,
                        $p->tujuan_departemen,
                        $p->nama_barang,
                        $p->jumlah,
                        $p->tanggal_pinjam->format('d/m/Y'),
                        $p->tanggal_kembali ? $p->tanggal_kembali->format('d/m/Y') : '-',
                        $p->status,
                        $p->alasan ?? '-',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export data pinjaman ke PDF (printable HTML).
     */
    public function exportPDF(Request $request)
    {
        $user = auth()->user();
        $tab = $request->get('tab', 'keluar');
        $pinjamans = $this->getExportData($user, $tab, $request);

        $typeLabel = $tab === 'masuk' ? 'Pinjaman Masuk' : 'Pinjaman Keluar';
        $deptLabel = $user->isAdmin() ? 'Semua Departemen' : 'Departemen ' . $user->departemen;
        $periodLabel = $typeLabel . ' — ' . $deptLabel;

        // Tambahkan info bulan ke label
        $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $selectedMonths = $request->get('bulan', []);
        if (!is_array($selectedMonths)) $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        if (!empty($selectedMonths)) {
            $periodLabel .= ' — ' . implode(', ', array_map(fn($m) => $monthNames[(int)$m], $selectedMonths)) . ' ' . $request->get('tahun', now()->year);
        }

        $totalPinjaman  = $pinjamans->count();
        $totalPending   = $pinjamans->where('status', 'Pending')->count();
        $totalDisetujui = $pinjamans->where('status', 'Disetujui')->count();
        $totalDitolak   = $pinjamans->where('status', 'Ditolak')->count();
        $totalBarang    = $pinjamans->sum('jumlah');

        return view('pinjaman.pdf', compact(
            'pinjamans', 'tab', 'periodLabel',
            'totalPinjaman', 'totalPending', 'totalDisetujui', 'totalDitolak', 'totalBarang'
        ));
    }

    /**
     * Get filtered export data.
     */
    private function getExportData($user, $tab, Request $request)
    {
        $query = Pinjaman::query();

        if ($tab === 'masuk') {
            if ($user->isUser()) {
                $query->where('tujuan_departemen', $user->departemen);
            }
        } else {
            if ($user->isUser()) {
                $query->where('peminjam_departemen', $user->departemen);
            }
        }

        // Multi-month filter
        $selectedMonths = $request->get('bulan', []);
        if (!is_array($selectedMonths)) {
            $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        }
        $tahun = $request->get('tahun', now()->year);

        if (!empty($selectedMonths)) {
            $query->whereYear('created_at', $tahun)
                  ->whereIn(DB::raw('MONTH(created_at)'), $selectedMonths);
        }

        return $query->latest()->get();
    }
}
