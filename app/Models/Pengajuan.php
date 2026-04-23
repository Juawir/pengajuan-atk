<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_pemohon',
        'departemen',
        'nama_barang',
        'jumlah',
        'prioritas',
        'keterangan',
        'foto_barang',
        'status'
    ];

    protected $casts = [
        'foto_barang' => 'array',
    ];

    /**
     * User pembuat pengajuan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}