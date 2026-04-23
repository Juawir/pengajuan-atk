<?php

namespace Database\Seeders;

use App\Models\BarangAtk;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use App\Models\Pinjaman;
use App\Models\ResetPasswordRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BigDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan pengecekan foreign key & bersihkan tabel (kecuali users)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Pengajuan::truncate();
        Pinjaman::truncate();
        BarangAtk::truncate();
        Notifikasi::truncate();
        ResetPasswordRequest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('id_ID');

        // Ambil data users yang ada (pisahkan admin dan user biasa)
        $users = User::where('role', 'user')->get();
        $admins = User::where('role', 'admin')->get();

        if ($users->isEmpty()) {
            $this->command->error('Tidak ada data user biasa (role: user) di database. Mohon buat terlebih dahulu.');
            return;
        }

        if ($admins->isEmpty()) {
            $this->command->error('Tidak ada data admin (role: admin) di database. Mohon buat terlebih dahulu.');
            return;
        }

        $this->command->info('Membuat Master Barang ATK...');
        
        $masterBarang = [
            'Kertas A4 70gsm', 'Kertas A4 80gsm', 'Kertas F4 70gsm', 'Kertas F4 80gsm',
            'Tinta Printer Canon Black', 'Tinta Printer Canon Color', 'Tinta Printer Epson Black', 'Tinta Printer Epson Color',
            'Pulpen Snowman V1', 'Pulpen Pilot G2', 'Pensil 2B Faber Castell', 'Penghapus Joyko',
            'Buku Tulis Sidu 38 Lembar', 'Buku Tulis Sidu 58 Lembar', 'Buku Agenda Tahunan',
            'Map Plastik Transparan', 'Map Snelhecter Kertas', 'Bantalan Stempel', 'Tinta Stempel Biru',
            'Spidol Whiteboard Hitam', 'Spidol Whiteboard Biru', 'Spidol Permanen Hitam',
            'Stabilo Boss Kuning', 'Stabilo Boss Hijau', 'Sticky Notes Kuning Kecil', 'Sticky Notes Warna-Warni',
            'Stapler Joyko HD-10', 'Isi Stapler Joyko No.10', 'Gunting Sedang', 'Cutter Kenko',
            'Isi Cutter Kenko Besar', 'Lem Kertas Glukol', 'Double Tape 1 Inch', 'Selotip Bening 1 Inch',
            'Binder Clip 105', 'Binder Clip 111', 'Paper Clip Kecil', 'Flashdisk 32GB Sandisk', 'Baterai AA Alkaline', 'Baterai AAA Alkaline'
        ];

        foreach ($masterBarang as $barang) {
            BarangAtk::create([
                'nama_barang' => $barang,
                'foto_path'   => null,
            ]);
        }

        $this->command->info('Membuat Data Pengajuan ATK (300 data)...');
        
        $prioritasList = ['Rendah', 'Sedang', 'Tinggi'];
        $statusList = ['Pending', 'Disetujui', 'Ditolak'];
        
        for ($i = 0; $i < 300; $i++) {
            $user = $users->random();
            $status = $faker->randomElement($statusList);
            $prioritas = $faker->randomElement($prioritasList);
            
            // Random date within the last 6 months
            $createdAt = Carbon::now()->subDays(rand(0, 180))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $updatedAt = (clone $createdAt)->addDays(rand(0, 2));
            if ($updatedAt > Carbon::now()) $updatedAt = Carbon::now();

            $alasan = null;
            if ($status === 'Ditolak') {
                $alasan = $faker->randomElement([
                    'Stok barang sedang kosong di gudang.',
                    'Mohon diajukan ulang bulan depan karena kuota departemen habis.',
                    'Barang ini tidak diizinkan untuk departemen Anda.',
                    'Harap hubungi admin pengadaan untuk barang jenis ini.',
                    'Spesifikasi barang tidak sesuai standar perusahaan.'
                ]);
            }

            // Pilih 1 hingga 3 barang acak dari master
            $jumlahItemBarang = rand(1, 3);
            $namaBarangGabungan = [];
            $totalJumlah = 0;
            
            for ($j = 0; $j < $jumlahItemBarang; $j++) {
                $b = $faker->randomElement($masterBarang);
                $qty = rand(1, 15);
                $namaBarangGabungan[] = $b . " ($qty)";
                $totalJumlah += $qty;
            }

            $pengajuan = Pengajuan::create([
                'user_id' => $user->id,
                'nama_pemohon' => $user->name,
                'departemen' => $user->departemen ?? $faker->randomElement(['IT', 'HRD', 'Keuangan', 'Marketing', 'Operasional']),
                'nama_barang' => implode(', ', $namaBarangGabungan),
                'jumlah' => $totalJumlah,
                'prioritas' => $prioritas,
                'keterangan' => $faker->optional(0.6)->sentence(),
                'status' => $status,
                'alasan_penolakan' => $alasan,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);

            // Jika status tidak pending, buatkan notifikasi untuk user pemohon
            if ($status !== 'Pending') {
                Notifikasi::create([
                    'user_id' => $user->id,
                    'type' => $status === 'Disetujui' ? 'status_disetujui' : 'status_ditolak',
                    'title' => $status === 'Disetujui' ? 'Pengajuan Disetujui' : 'Pengajuan Ditolak',
                    'message' => 'Pengajuan ' . $pengajuan->nama_barang . ' telah ' . strtolower($status) . ' oleh Admin.' . ($alasan ? ' Alasan: ' . $alasan : ''),
                    'pengajuan_id' => $pengajuan->id,
                    'is_read' => $faker->boolean(70),
                    'created_at' => $updatedAt,
                    'updated_at' => $updatedAt,
                ]);
            }
        }

        $this->command->info('Membuat Data Pinjaman (100 data)...');
        $departemenList = ['IT', 'HRD', 'Keuangan', 'Marketing', 'Operasional', 'Umum'];
        
        for ($i = 0; $i < 100; $i++) {
            $peminjam = $users->random();
            $tujuanDept = $faker->randomElement(array_diff($departemenList, [$peminjam->departemen]));
            if (!$tujuanDept) $tujuanDept = 'Umum';

            $statusPinjaman = $faker->randomElement(['Pending', 'Disetujui', 'Ditolak', 'Dikembalikan']);
            $tanggalPinjam = Carbon::now()->subDays(rand(5, 120));
            $tanggalKembali = (clone $tanggalPinjam)->addDays(rand(1, 14));
            
            $catatan = null;
            $responderId = null;

            if ($statusPinjaman !== 'Pending') {
                $responder = $users->where('departemen', $tujuanDept)->first() ?? $admins->random();
                $responderId = $responder->id;
                
                if ($statusPinjaman === 'Disetujui' || $statusPinjaman === 'Dikembalikan') {
                    $catatan = $faker->optional(0.5)->sentence(4);
                } elseif ($statusPinjaman === 'Ditolak') {
                    $catatan = 'Barang sedang digunakan oleh tim internal kami. Maaf.';
                }
            }

            Pinjaman::create([
                'peminjam_user_id' => $peminjam->id,
                'peminjam_nama' => $peminjam->name,
                'peminjam_departemen' => $peminjam->departemen ?? 'IT',
                'tujuan_departemen' => $tujuanDept,
                'nama_barang' => $faker->randomElement(['Proyektor', 'Laptop Cadangan', 'Kamera DSLR', 'Kabel Roll 15M', 'Layar Proyektor Tripod', 'Speaker Portable', 'Webcam Logitech']),
                'jumlah' => rand(1, 3),
                'alasan' => $faker->sentence(),
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_kembali' => $tanggalKembali,
                'status' => $statusPinjaman,
                'catatan_response' => $catatan,
                'responder_user_id' => $responderId,
                'created_at' => $tanggalPinjam,
                'updated_at' => $statusPinjaman === 'Pending' ? $tanggalPinjam : (clone $tanggalPinjam)->addDays(1),
            ]);
        }

        $this->command->info('Membuat Data Reset Password (15 data)...');
        for ($i = 0; $i < 15; $i++) {
            $user = $users->random();
            $status = $faker->randomElement(['pending', 'resolved']);
            $createdAt = Carbon::now()->subDays(rand(1, 30));

            ResetPasswordRequest::create([
                'email' => $user->email,
                'status' => $status,
                'resolved_by' => $status === 'resolved' ? $admins->random()->id : null,
                'resolved_at' => $status === 'resolved' ? (clone $createdAt)->addHours(rand(1, 24)) : null,
                'created_at' => $createdAt,
                'updated_at' => $status === 'resolved' ? (clone $createdAt)->addHours(rand(1, 24)) : $createdAt,
            ]);
        }

        $this->command->info('Selesai! Database Anda telah diisi dengan ratusan data dummy siap uji.');
    }
}
