<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_detail_pesanan';

    protected $fillable = [
        'id_pesanan',
        'id_produk',
        'kuantitas_produk',
        'harga_produk_tersimpan',
    ];

    // Relasi: Satu Detail Pesanan milik satu Pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi: Satu Detail Pesanan merujuk ke satu Produk
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }
}