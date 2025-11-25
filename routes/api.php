<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\Admin\ProdukController as AdminProdukController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\KomplainController as AdminKomplainController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === Rute Publik (Tidak perlu login) ===
Route::post('/register', [UserController::class, 'daftarPengguna']);
Route::post('/login', [UserController::class, 'masuk']);

Route::get('/produk', [ProdukController::class, 'ambilSemuaProduk']);
Route::get('/produk/search', [ProdukController::class, 'cariProduk']); // Contoh route untuk cari/filter
Route::get('/produk/{id}', [ProdukController::class, 'ambilDetailProduk']);

Route::get('/berita', [BeritaController::class, 'ambilDaftarBerita']);
Route::get('/berita/{id}', [BeritaController::class, 'ambilDetailBerita']);


// === Rute Terotentikasi (Perlu login sebagai 'pembeli' atau 'admin') ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    // --- Rute Khusus Pembeli ---
    // (Asumsi Anda akan menambahkan middleware role 'pembeli')
    Route::prefix('user')->middleware('role:pembeli')->group(function () {
        Route::get('/me', [UserController::class, 'me']);

        Route::get('/profil', [ProfilController::class, 'ambilProfil']);
        Route::put('/profil', [ProfilController::class, 'perbaruiProfil']);
        Route::put('/profil/ubah-sandi', [ProfilController::class, 'ubahKataSandi']);

        // Pesanan & Pembayaran
        Route::post('/pesanan/checkout', [PesananController::class, 'buatPesanan']); // (buatPesanan)
        Route::get('/pesanan', [PesananController::class, 'ambilDaftarPesanan']);
        Route::get('/pesanan/{id_pesanan}', [PesananController::class, 'ambilDetailPesanan']);
        Route::get('/pesanan/{id_pesanan}/status', [PesananController::class, 'cekStatusPesanan']);
        
        Route::get('/pesanan/{id_pesanan}/pembayaran', [PembayaranController::class, 'ambilDetailPembayaran']);
        Route::post('/pesanan/{id_pesanan}/pembayaran/konfirmasi', [PembayaranController::class, 'perbaruiStatusPembayaran']);
        Route::post('/pesanan/{id_pesanan}/batalkan', [PembayaranController::class, 'batalkanPesanan']);

        // Komplain
        Route::post('/pesanan/{id_pesanan}/komplain', [KomplainController::class, 'ajukanKomplain']);
    });

    // --- Rute Khusus Admin ---
    Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
        // Admin: Manajemen Produk
        Route::post('/produk', [AdminProdukController::class, 'tambahProduk']);
        Route::put('/produk/{id}', [AdminProdukController::class, 'ubahProduk']);
        Route::put('/produk/{id}/arsip', [AdminProdukController::class, 'arsipProduk']);
        Route::delete('/produk/{id}', [AdminProdukController::class, 'hapusProduk']);

        // Admin: Manajemen Pesanan
        Route::get('/pesanan', [AdminPesananController::class, 'ambilSemuaPesanan']);
        Route::put('/pesanan/{id_pesanan}/status', [AdminPesananController::class, 'perbaruiStatusPesanan']);

        // Admin: Manajemen Komplain
        Route::get('/komplain', [AdminKomplainController::class, 'ambilDaftarKomplain']);
        Route::get('/komplain/{id_komplain}', [AdminKomplainController::class, 'ambilDetailKomplain']);
        Route::put('/komplain/{id_komplain}/status', [AdminKomplainController::class, 'perbaruiStatusKomplain']);

        // Admin: Manajemen Berita
        Route::post('/berita', [AdminBeritaController::class, 'tambahBerita']);
        Route::put('/berita/{id}', [AdminBeritaController::class, 'ubahBerita']);
        Route::delete('/berita/{id}', [AdminBeritaController::class, 'hapusBerita']);
    });
});