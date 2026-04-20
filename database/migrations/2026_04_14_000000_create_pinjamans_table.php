<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pinjamans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjam_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('peminjam_nama');
            $table->string('peminjam_departemen');
            $table->string('tujuan_departemen');
            $table->string('nama_barang');
            $table->integer('jumlah');
            $table->text('alasan')->nullable();
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();
            $table->string('status')->default('Pending'); // Pending, Disetujui, Ditolak
            $table->text('catatan_response')->nullable();
            $table->foreignId('responder_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pinjamans');
    }
};
