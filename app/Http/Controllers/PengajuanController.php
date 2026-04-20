<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    /**
     * Dashboard utama dengan statistik.
     */
    public function dashboard()
    {
        $user = auth()->user();

        $baseQuery = Pengajuan::query();
        if ($user->isUser()) {
            $baseQuery->where('departemen', $user->departemen);
        }

        $totalPengajuan = (clone $baseQuery)->count();
        $totalPending   = (clone $baseQuery)->where('status', 'Pending')->count();
        $totalDisetujui = (clone $baseQuery)->where('status', 'Disetujui')->count();
        $totalDitolak   = (clone $baseQuery)->where('status', 'Ditolak')->count();

        $deptQuery = Pengajuan::query();
        if ($user->isUser()) {
            $deptQuery->where('departemen', $user->departemen);
        }
        $deptData = $deptQuery->selectRaw('departemen, COUNT(*) as total')
            ->groupBy('departemen')
            ->orderByDesc('total')
            ->get();

        $deptLabels = $deptData->pluck('departemen')->toArray();
        $deptValues = $deptData->pluck('total')->toArray();

        $recentQuery = Pengajuan::query();
        if ($user->isUser()) {
            $recentQuery->where('departemen', $user->departemen);
        }
        $recentPengajuan = $recentQuery->latest()->take(5)->get();

        return view('dashboard', compact(
            'totalPengajuan',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'deptLabels',
            'deptValues',
            'recentPengajuan'
        ));
    }

    /**
     * Menampilkan daftar semua pengajuan dengan filter & search.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Pengajuan::query();

        if ($user->isUser()) {
            $query->where('departemen', $user->departemen);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('departemen') && $user->isAdmin()) {
            $query->where('departemen', $request->departemen);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('departemen', 'like', "%{$search}%");
            });
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

        $pengajuans = $query->latest()->paginate(10)->withQueryString();
        $departemenList = Pengajuan::select('departemen')->distinct()->pluck('departemen');
        $tahunList = Pengajuan::selectRaw('YEAR(created_at) as tahun')->distinct()->orderByDesc('tahun')->pluck('tahun');

        return view('pengajuan.index', compact('pengajuans', 'departemenList', 'selectedMonths', 'tahun', 'tahunList'));
    }

    /**
     * Form tambah pengajuan.
     */
    public function create()
    {
        return view('pengajuan.create');
    }

    /**
     * Simpan pengajuan baru + kirim notifikasi ke semua admin.
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'departemen'   => 'required|string|max:255',
            'nama_barang'  => 'required|string|max:255',
            'jumlah'       => 'required|integer|min:1',
            'prioritas'    => 'nullable|in:Rendah,Sedang,Tinggi',
            'keterangan'   => 'nullable|string',
        ]);

        $data = $request->all();
        $data['user_id'] = $user->id;

        if ($user->isUser()) {
            $data['departemen'] = $user->departemen;
        }

        $pengajuan = Pengajuan::create($data);

        // === NOTIFIKASI: Kirim ke semua admin ===
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notifikasi::create([
                'user_id'      => $admin->id,
                'type'         => 'pengajuan_baru',
                'title'        => 'Pengajuan Baru',
                'message'      => $user->name . ' mengajukan ' . $pengajuan->nama_barang . ' (' . $pengajuan->jumlah . ' unit) dari dept. ' . $pengajuan->departemen,
                'pengajuan_id' => $pengajuan->id,
            ]);
        }

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan ATK berhasil ditambahkan!');
    }

    /**
     * Detail pengajuan.
     */
    public function show(Pengajuan $pengajuan)
    {
        $this->authorizeAccess($pengajuan);
        return view('pengajuan.show', compact('pengajuan'));
    }

    /**
     * Form edit pengajuan.
     */
    public function edit(Pengajuan $pengajuan)
    {
        $this->authorizeAccess($pengajuan);
        return view('pengajuan.edit', compact('pengajuan'));
    }

    /**
     * Update pengajuan.
     */
    public function update(Request $request, Pengajuan $pengajuan)
    {
        $this->authorizeAccess($pengajuan);

        $request->validate([
            'nama_pemohon' => 'required|string|max:255',
            'departemen'   => 'required|string|max:255',
            'nama_barang'  => 'required|string|max:255',
            'jumlah'       => 'required|integer|min:1',
            'prioritas'    => 'nullable|in:Rendah,Sedang,Tinggi',
            'keterangan'   => 'nullable|string',
        ]);

        $data = $request->all();
        if (auth()->user()->isUser()) {
            $data['departemen'] = auth()->user()->departemen;
        }

        $pengajuan->update($data);

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil diperbarui!');
    }

    /**
     * Hapus pengajuan (admin only).
     */
    public function destroy(Pengajuan $pengajuan)
    {
        $pengajuan->delete();
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus!');
    }

    /**
     * Update status pengajuan (admin only) + kirim notifikasi ke user pembuat.
     */
    public function updateStatus(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'status' => 'required|in:Pending,Disetujui,Ditolak',
        ]);

        $oldStatus = $pengajuan->status;
        $newStatus = $request->status;
        $pengajuan->update(['status' => $newStatus]);

        // === NOTIFIKASI: Kirim ke user pembuat pengajuan ===
        if ($pengajuan->user_id) {
            $statusMap = [
                'Disetujui' => ['type' => 'status_disetujui', 'icon' => '✅', 'title' => 'Pengajuan Disetujui'],
                'Ditolak'   => ['type' => 'status_ditolak',   'icon' => '❌', 'title' => 'Pengajuan Ditolak'],
                'Pending'   => ['type' => 'status_pending',   'icon' => '⏳', 'title' => 'Status Kembali Pending'],
            ];

            $info = $statusMap[$newStatus];

            Notifikasi::create([
                'user_id'      => $pengajuan->user_id,
                'type'         => $info['type'],
                'title'        => $info['title'],
                'message'      => 'Pengajuan ' . $pengajuan->nama_barang . ' (' . $pengajuan->jumlah . ' unit) telah ' . strtolower($newStatus) . ' oleh Admin.',
                'pengajuan_id' => $pengajuan->id,
            ]);
        }

        return redirect()->back()->with('success', "Status berhasil diubah menjadi {$newStatus}!");
    }

    /**
     * Cek akses berdasarkan departemen.
     */
    private function authorizeAccess(Pengajuan $pengajuan): void
    {
        $user = auth()->user();
        if ($user->isUser() && $pengajuan->departemen !== $user->departemen) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
        }
    }

    /**
     * Export data pengajuan ke CSV.
     */
    public function exportCSV(Request $request)
    {
        $query = $this->buildExportQuery($request);
        $pengajuans = $query->latest()->get();

        $user = auth()->user();
        $deptLabel = $user->isAdmin() ? 'semua' : $user->departemen;
        $filename = 'pengajuan_atk_' . strtolower($deptLabel) . '_' . date('Ymd') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($pengajuans) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

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
     * Export data pengajuan ke PDF (printable HTML).
     */
    public function exportPDF(Request $request)
    {
        $query = $this->buildExportQuery($request);
        $pengajuans = $query->latest()->get();

        $user = auth()->user();
        $totalPengajuan = $pengajuans->count();
        $totalPending   = $pengajuans->where('status', 'Pending')->count();
        $totalDisetujui = $pengajuans->where('status', 'Disetujui')->count();
        $totalDitolak   = $pengajuans->where('status', 'Ditolak')->count();
        $totalBarang    = $pengajuans->sum('jumlah');

        $perDepartemen = $pengajuans->groupBy('departemen')->map(function ($items, $dept) {
            return (object)[
                'departemen'   => $dept,
                'total'        => $items->count(),
                'total_barang' => $items->sum('jumlah'),
            ];
        })->sortByDesc('total')->values();

        $periodLabel = $user->isAdmin() ? 'Semua Departemen' : 'Departemen ' . $user->departemen;
        $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $selectedMonths = $request->get('bulan', []);
        if (!is_array($selectedMonths)) $selectedMonths = $selectedMonths ? [$selectedMonths] : [];
        if (!empty($selectedMonths)) {
            $periodLabel .= ' — ' . implode(', ', array_map(fn($m) => $monthNames[(int)$m], $selectedMonths)) . ' ' . $request->get('tahun', now()->year);
        }
        $statusFilter = $request->get('status');
        if ($statusFilter) {
            $periodLabel .= ' — Status: ' . $statusFilter;
        }

        return view('pengajuan.pdf', compact(
            'pengajuans', 'totalPengajuan', 'totalPending', 'totalDisetujui', 'totalDitolak',
            'totalBarang', 'perDepartemen', 'periodLabel'
        ));
    }

    /**
     * Build export query respecting user's department filter.
     */
    private function buildExportQuery(Request $request)
    {
        $user = auth()->user();
        $query = Pengajuan::query();

        if ($user->isUser()) {
            $query->where('departemen', $user->departemen);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('departemen') && $user->isAdmin()) {
            $query->where('departemen', $request->departemen);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemohon', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('departemen', 'like', "%{$search}%");
            });
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

        return $query;
    }
}