<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
// Jika Anda pakai Spatie Roles, tambahkan:
// use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    // Hapus 'HasRoles' jika tidak pakai Spatie
    use HasApiTokens, HasFactory, Notifiable; 

    protected $primaryKey = 'id_pengguna';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'nomor_telepon',
        'alamat',
        'foto_profil',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relasi: Satu User (Pembeli) punya banyak Pesanan [cite: 3573]
    public function pesanans()
    {
        return $this->hasMany(Pesanan::class, 'id_pembeli', 'id_pengguna');
    }

    // Relasi: Satu User (Pembeli) bisa punya banyak Komplain [cite: 3573]
    public function komplains()
    {
        return $this->hasMany(Komplain::class, 'id_pembeli', 'id_pengguna');
    }
}