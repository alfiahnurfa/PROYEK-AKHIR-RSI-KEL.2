<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_pesanan';

    protected $fillable = [
        'id_pembeli',
        'alamat_pengiriman',
        'status_pesanan',
    ];

    // Relasi: Satu Pesanan dimiliki oleh satu User (Pembeli) [cite: 3573]
    public function pembeli()
    {
        return $this->belongsTo(User::class, 'id_pembeli', 'id_pengguna');
    }

    // Relasi: Satu Pesanan terdiri dari banyak Detail Pesanan [cite: 3573]
    public function detailPesanans()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi: Satu Pesanan memiliki satu Pembayaran [cite: 3573]
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi: Satu Pesanan bisa memiliki satu Komplain [cite: 3573]
    public function komplain()
    {
        return $this->hasOne(Komplain::class, 'id_pesanan', 'id_pesanan');
    }
}