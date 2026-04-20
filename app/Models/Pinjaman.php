<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pinjaman extends Model
{
    use HasFactory;

    protected $table = 'pinjamans';

    protected $fillable = [
        'peminjam_user_id',
        'peminjam_nama',
        'peminjam_departemen',
        'tujuan_departemen',
        'nama_barang',
        'jumlah',
        'alasan',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'catatan_response',
        'responder_user_id',
    ];

    protected $casts = [
        'tanggal_pinjam'   => 'date',
        'tanggal_kembali'  => 'date',
    ];

    /**
     * User yang mengajukan pinjaman.
     */
    public function peminjam()
    {
        return $this->belongsTo(User::class, 'peminjam_user_id');
    }

    /**
     * User yang merespon pinjaman.
     */
    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_user_id');
    }
}
