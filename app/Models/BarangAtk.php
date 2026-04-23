<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangAtk extends Model
{
    use HasFactory;

    protected $table = 'barang_atk';

    protected $fillable = [
        'nama_barang',
        'foto_path',
    ];
}
