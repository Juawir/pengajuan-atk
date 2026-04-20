<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\PinjamanController;

// ===== AUTH ROUTES (Guest only) =====
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');
});

// ===== AUTHENTICATED ROUTES =====
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [PengajuanController::class, 'dashboard'])->name('dashboard');

    // Pengajuan (semua user bisa akses)
    Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/export-csv', [PengajuanController::class, 'exportCSV'])->name('pengajuan.exportCSV');
    Route::get('/pengajuan/export-pdf', [PengajuanController::class, 'exportPDF'])->name('pengajuan.exportPDF');
    Route::get('/pengajuan/create', [PengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/pengajuan/{pengajuan}', [PengajuanController::class, 'show'])->name('pengajuan.show');
    Route::get('/pengajuan/{pengajuan}/edit', [PengajuanController::class, 'edit'])->name('pengajuan.edit');
    Route::put('/pengajuan/{pengajuan}', [PengajuanController::class, 'update'])->name('pengajuan.update');

    // Pinjaman Barang Antar Departemen (semua user bisa akses)
    Route::get('/pinjaman', [PinjamanController::class, 'index'])->name('pinjaman.index');
    Route::get('/pinjaman/create', [PinjamanController::class, 'create'])->name('pinjaman.create');
    Route::post('/pinjaman', [PinjamanController::class, 'store'])->name('pinjaman.store');
    Route::get('/pinjaman/{pinjaman}', [PinjamanController::class, 'show'])->name('pinjaman.show');
    Route::patch('/pinjaman/{pinjaman}/status', [PinjamanController::class, 'updateStatus'])->name('pinjaman.updateStatus');
    Route::get('/pinjaman-export/csv', [PinjamanController::class, 'exportCSV'])->name('pinjaman.exportCSV');
    Route::get('/pinjaman-export/pdf', [PinjamanController::class, 'exportPDF'])->name('pinjaman.exportPDF');

    // Pengaturan akun (semua user bisa akses)
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/profile', [PengaturanController::class, 'updateProfile'])->name('pengaturan.updateProfile');
    Route::put('/pengaturan/password', [PengaturanController::class, 'updatePassword'])->name('pengaturan.updatePassword');

    // Notifikasi API
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::patch('/notifikasi/{notifikasi}/read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.read');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.readAll');

    // ===== ADMIN ONLY ROUTES =====
    Route::middleware('admin')->group(function () {
        Route::delete('/pengajuan/{pengajuan}', [PengajuanController::class, 'destroy'])->name('pengajuan.destroy');
        Route::patch('/pengajuan/{pengajuan}/status', [PengajuanController::class, 'updateStatus'])->name('pengajuan.updateStatus');
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/export-csv', [LaporanController::class, 'exportCSV'])->name('laporan.exportCSV');
        Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPDF'])->name('laporan.exportPDF');

        // Registrasi Akun & Reset Password (Admin Only)
        Route::get('/registrasi', [AuthController::class, 'showRegistrasi'])->name('registrasi.index');
        Route::post('/registrasi', [AuthController::class, 'registerUser'])->name('registrasi.store');
        Route::patch('/registrasi/{user}/reset-password', [AuthController::class, 'resetUserPassword'])->name('registrasi.resetPassword');
    });
});
