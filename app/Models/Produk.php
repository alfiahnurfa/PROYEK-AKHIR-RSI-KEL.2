<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_produk';

    protected $fillable = [
        'nama_produk',
        'kategori_produk',
        'deskripsi_produk',
        'berat_produk',
        'harga_produk',
        'stok_produk',
        'foto_produk',
        'status_produk',
    ];

    // Relasi: Satu Produk bisa ada di banyak Detail Pesanan [cite: 3573]
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class, 'id_produk', 'id_produk');
    }
}