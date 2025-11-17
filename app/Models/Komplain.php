<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Komplain extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_komplain';

    protected $fillable = [
        'id_pesanan',
        'id_pembeli',
        'judul_komplain',
        'deskripsi_komplain',
        'bukti_komplain',
        'tanggal_pengajuan',
        'status_komplain',
    ];

    // Relasi: Satu Komplain milik satu Pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi: Satu Komplain diajukan oleh satu User (Pembeli)
    public function pembeli()
    {
        return $this->belongsTo(User::class, 'id_pembeli', 'id_pengguna');
    }
}