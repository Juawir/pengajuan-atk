<?php

namespace Database\Seeders;

use App\Models\Pengajuan;
use App\Models\Pinjaman;
use App\Models\User;
use App\Models\Notifikasi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // === USER ACCOUNTS ===
        $admin = User::create([
            'name'       => 'Admin ATK',
            'email'      => 'admin@atk.com',
            'password'   => '12345678',
            'role'       => 'admin',
            'departemen' => null,
        ]);

        $budi = User::create([
            'name'       => 'Budi Santoso',
            'email'      => 'budi@atk.com',
            'password'   => 'password123',
            'role'       => 'user',
            'departemen' => 'IT',
        ]);

        $siti = User::create([
            'name'       => 'Siti Aminah',
            'email'      => 'siti@atk.com',
            'password'   => 'password123',
            'role'       => 'user',
            'departemen' => 'HRD',
        ]);

        $ahmad = User::create([
            'name'       => 'Ahmad Fauzi',
            'email'      => 'ahmad@atk.com',
            'password'   => 'password123',
            'role'       => 'user',
            'departemen' => 'Keuangan',
        ]);

        $dewi = User::create([
            'name'       => 'Dewi Kartika',
            'email'      => 'dewi@atk.com',
            'password'   => 'password123',
            'role'       => 'user',
            'departemen' => 'Marketing',
        ]);

        // === DATA PENGAJUAN ATK ===
        $data = [
            ['user_id' => $budi->id, 'nama_pemohon' => 'Budi Santoso', 'departemen' => 'IT', 'nama_barang' => 'Kertas A4 80gsm', 'jumlah' => 10, 'prioritas' => 'Tinggi', 'keterangan' => 'Kebutuhan cetak laporan bulanan', 'status' => 'Disetujui'],
            ['user_id' => $siti->id, 'nama_pemohon' => 'Siti Aminah', 'departemen' => 'HRD', 'nama_barang' => 'Pulpen Pilot', 'jumlah' => 24, 'prioritas' => 'Sedang', 'keterangan' => 'Untuk keperluan wawancara karyawan baru', 'status' => 'Pending'],
            ['user_id' => $ahmad->id, 'nama_pemohon' => 'Ahmad Fauzi', 'departemen' => 'Keuangan', 'nama_barang' => 'Binder Clip Besar', 'jumlah' => 5, 'prioritas' => 'Rendah', 'keterangan' => 'Penyimpanan dokumen keuangan', 'status' => 'Disetujui'],
            ['user_id' => $dewi->id, 'nama_pemohon' => 'Dewi Kartika', 'departemen' => 'Marketing', 'nama_barang' => 'Spidol Whiteboard', 'jumlah' => 12, 'prioritas' => 'Sedang', 'keterangan' => 'Untuk presentasi mingguan tim marketing', 'status' => 'Pending'],
            ['user_id' => null, 'nama_pemohon' => 'Rudi Hermawan', 'departemen' => 'Operasional', 'nama_barang' => 'Stappler HD-10', 'jumlah' => 3, 'prioritas' => 'Rendah', 'keterangan' => null, 'status' => 'Ditolak'],
            ['user_id' => null, 'nama_pemohon' => 'Lina Marlina', 'departemen' => 'Umum', 'nama_barang' => 'Tinta Printer Canon', 'jumlah' => 4, 'prioritas' => 'Tinggi', 'keterangan' => 'Tinta habis, deadline cetak undangan', 'status' => 'Disetujui'],
            ['user_id' => $budi->id, 'nama_pemohon' => 'Budi Santoso', 'departemen' => 'IT', 'nama_barang' => 'Sticky Notes Warna', 'jumlah' => 20, 'prioritas' => 'Rendah', 'keterangan' => 'Untuk brainstorming sprint planning', 'status' => 'Pending'],
            ['user_id' => $siti->id, 'nama_pemohon' => 'Siti Aminah', 'departemen' => 'HRD', 'nama_barang' => 'Map Plastik', 'jumlah' => 50, 'prioritas' => 'Sedang', 'keterangan' => 'Arsip data karyawan tahun berjalan', 'status' => 'Disetujui'],
            ['user_id' => $ahmad->id, 'nama_pemohon' => 'Ahmad Fauzi', 'departemen' => 'Keuangan', 'nama_barang' => 'Kalkulator Casio', 'jumlah' => 2, 'prioritas' => 'Tinggi', 'keterangan' => 'Kalkulator lama sudah rusak', 'status' => 'Pending'],
            ['user_id' => $dewi->id, 'nama_pemohon' => 'Dewi Kartika', 'departemen' => 'Marketing', 'nama_barang' => 'Kertas Foto Glossy A4', 'jumlah' => 5, 'prioritas' => 'Sedang', 'keterangan' => 'Cetak brosur produk baru', 'status' => 'Ditolak'],
            ['user_id' => null, 'nama_pemohon' => 'Hendra Wijaya', 'departemen' => 'Operasional', 'nama_barang' => 'Amplop Coklat Besar', 'jumlah' => 100, 'prioritas' => 'Sedang', 'keterangan' => 'Pengiriman dokumen antar cabang', 'status' => 'Disetujui'],
            ['user_id' => $budi->id, 'nama_pemohon' => 'Budi Santoso', 'departemen' => 'IT', 'nama_barang' => 'Flash Disk 32GB', 'jumlah' => 5, 'prioritas' => 'Tinggi', 'keterangan' => 'Backup data project', 'status' => 'Pending'],
        ];

        foreach ($data as $item) {
            Pengajuan::create($item);
        }

        // === PINJAMAN BARANG ANTAR DEPARTEMEN ===
        Pinjaman::create([
            'peminjam_user_id'    => $budi->id,
            'peminjam_nama'       => 'Budi Santoso',
            'peminjam_departemen' => 'IT',
            'tujuan_departemen'   => 'HRD',
            'nama_barang'         => 'Proyektor Portable',
            'jumlah'              => 1,
            'alasan'              => 'Presentasi demo project ke klien, proyektor IT sedang maintenance.',
            'tanggal_pinjam'      => now()->subDays(3),
            'tanggal_kembali'     => now()->addDays(4),
            'status'              => 'Pending',
        ]);

        Pinjaman::create([
            'peminjam_user_id'    => $siti->id,
            'peminjam_nama'       => 'Siti Aminah',
            'peminjam_departemen' => 'HRD',
            'tujuan_departemen'   => 'IT',
            'nama_barang'         => 'Laptop Cadangan',
            'jumlah'              => 2,
            'alasan'              => 'Training karyawan baru memerlukan laptop tambahan.',
            'tanggal_pinjam'      => now()->subDays(5),
            'tanggal_kembali'     => now()->addDays(10),
            'status'              => 'Disetujui',
            'catatan_response'    => 'Mohon dijaga kondisinya. Kembalikan tepat waktu.',
            'responder_user_id'   => $budi->id,
        ]);

        Pinjaman::create([
            'peminjam_user_id'    => $ahmad->id,
            'peminjam_nama'       => 'Ahmad Fauzi',
            'peminjam_departemen' => 'Keuangan',
            'tujuan_departemen'   => 'Marketing',
            'nama_barang'         => 'Kamera DSLR',
            'jumlah'              => 1,
            'alasan'              => 'Dokumentasi audit tahunan.',
            'tanggal_pinjam'      => now()->subDays(1),
            'tanggal_kembali'     => now()->addDays(2),
            'status'              => 'Ditolak',
            'catatan_response'    => 'Maaf, kamera sedang dipakai untuk photoshoot produk.',
            'responder_user_id'   => $dewi->id,
        ]);

        Pinjaman::create([
            'peminjam_user_id'    => $dewi->id,
            'peminjam_nama'       => 'Dewi Kartika',
            'peminjam_departemen' => 'Marketing',
            'tujuan_departemen'   => 'IT',
            'nama_barang'         => 'Monitor 27 inch',
            'jumlah'              => 1,
            'alasan'              => 'Desain banner event perusahaan perlu layar besar.',
            'tanggal_pinjam'      => now(),
            'tanggal_kembali'     => now()->addDays(7),
            'status'              => 'Pending',
        ]);

        // === NOTIFIKASI DEMO ===
        Notifikasi::create([
            'user_id'      => $admin->id,
            'type'         => 'pengajuan_baru',
            'title'        => 'Pengajuan Baru',
            'message'      => 'Budi Santoso mengajukan Flash Disk 32GB (5 unit) dari dept. IT',
            'pengajuan_id' => 12,
            'is_read'      => false,
        ]);

        Notifikasi::create([
            'user_id'      => $admin->id,
            'type'         => 'pengajuan_baru',
            'title'        => 'Pengajuan Baru',
            'message'      => 'Budi Santoso mengajukan Sticky Notes Warna (20 unit) dari dept. IT',
            'pengajuan_id' => 7,
            'is_read'      => false,
        ]);

        Notifikasi::create([
            'user_id'      => $budi->id,
            'type'         => 'status_disetujui',
            'title'        => 'Pengajuan Disetujui',
            'message'      => 'Pengajuan Kertas A4 80gsm (10 unit) telah disetujui oleh Admin.',
            'pengajuan_id' => 1,
            'is_read'      => false,
        ]);

        // Notifikasi pinjaman
        Notifikasi::create([
            'user_id'      => $siti->id,
            'type'         => 'pengajuan_baru',
            'title'        => 'Pinjaman Baru Masuk',
            'message'      => 'Budi Santoso (Dept. IT) meminjam Proyektor Portable (1 unit)',
            'pengajuan_id' => null,
            'is_read'      => false,
        ]);

        Notifikasi::create([
            'user_id'      => $budi->id,
            'type'         => 'pengajuan_baru',
            'title'        => 'Pinjaman Baru Masuk',
            'message'      => 'Dewi Kartika (Dept. Marketing) meminjam Monitor 27 inch (1 unit)',
            'pengajuan_id' => null,
            'is_read'      => false,
        ]);
    }
}
