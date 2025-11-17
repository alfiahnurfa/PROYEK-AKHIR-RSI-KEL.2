<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_berita';
    protected $table = 'beritas'; // Menjelaskan nama tabel

    protected $fillable = [
        'judul_berita',
        'isi_berita',
        'gambar_berita',
        'tanggal_publikasi',
        'status_publikasi_berita',
    ];
}