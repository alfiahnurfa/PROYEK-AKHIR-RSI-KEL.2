<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Profil\LihatProfilController;
use App\Http\Controllers\Profil\UbahProfilController;
use App\Http\Controllers\Profil\UbahKataSandiController;
use App\Http\Controllers\Produk\LihatProdukController;
use App\Http\Controllers\Pesanan\LihatPesananController;
use App\Http\Controllers\Pesanan\TambahPesananController;
use App\Http\Controllers\Pembayaran\BatalkanPembayaranController;
use App\Http\Controllers\Pembayaran\KonfirmasiPembayaranController;
use App\Http\Controllers\Pembayaran\LihatDetailPembayaranController;
use App\Http\Controllers\Komplain\TambahKomplainController;
use App\Http\Controllers\Berita\LihatBeritaController;
use App\Http\Controllers\Admin\Produk\TambahProdukController;
use App\Http\Controllers\Admin\Produk\UbahProdukController;
use App\Http\Controllers\Admin\Produk\HapusProdukController;
use App\Http\Controllers\Admin\Pesanan\LihatSemuaPesananController;
use App\Http\Controllers\Admin\Pesanan\UbahPesananController;
use App\Http\Controllers\Admin\Komplain\UbahKomplainController;
use App\Http\Controllers\Admin\Komplain\LihatKomplainController;
use App\Http\Controllers\Admin\Berita\HapusBeritaController;
use App\Http\Controllers\Admin\Berita\TambahBeritaController;
use App\Http\Controllers\Admin\Berita\UbahBeritaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// === Rute Publik (Tidak perlu login) ===
Route::post('/register', [RegisterController::class, 'daftarPengguna']);
Route::post('/login', [LoginController::class, 'masuk']);

Route::get('/produk', [LihatProdukController::class, 'ambilSemuaProduk']);
Route::get('/produkLaris', [LihatProdukController::class, 'ambilProdukTerlaris']);
Route::get('/produk/search', [LihatProdukController::class, 'cariProduk']);
Route::get('/produk/{id}', [LihatProdukController::class, 'ambilDetailProduk']);

Route::get('/berita', [LihatBeritaController::class, 'ambilDaftarBerita']);
Route::get('/beritaBaru', [LihatBeritaController::class, 'ambilBeritaTerkini']);
Route::get('/berita/{id}', [LihatBeritaController::class, 'ambilDetailBerita']);


// === Rute Terotentikasi (Perlu login sebagai 'pembeli' atau 'admin') ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [LogoutController::class, 'logout']);

    // --- Rute Khusus Pembeli ---
    // (Asumsi Anda akan menambahkan middleware role 'pembeli')
    Route::prefix('user')->middleware('role:pembeli')->group(function () {
        Route::get('/me', [LoginController::class, 'me']);

        Route::get('/profil', [LihatProfilController::class, 'ambilProfil']);
        Route::put('/profil', [UbahProfilController::class, 'ubahProfil']);
        Route::put('/profil/ubah-sandi', [UbahKataSandiController::class, 'ubahKataSandi']);

        // Pesanan & Pembayaran
        Route::post('/pesanan/checkout', [TambahPesananController::class, 'buatPesanan']); // (buatPesanan)
        Route::get('/pesanan', [LihatPesananController::class, 'ambilDaftarPesanan']);
        Route::get('/pesanan/{id_pesanan}', [LihatPesananController::class, 'ambilDetailPesanan']);
        
        Route::get('/pesanan/{id_pesanan}/pembayaran', [LihatDetailPembayaranController::class, 'ambilDetailPembayaran']);
        Route::post('/pesanan/{id_pesanan}/pembayaran/konfirmasi', [KonfirmasiPembayaranController::class, 'perbaruiStatusPembayaran']);
        Route::post('/pesanan/{id_pesanan}/batalkan', [BatalkanPembayaranController::class, 'batalkanPesanan']);

        // Komplain
        Route::post('/pesanan/{id_pesanan}/komplain', [TambahKomplainController::class, 'ajukanKomplain']);
    });

    // --- Rute Khusus Admin ---
    Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
        // Admin: Manajemen Produk
        Route::post('/produk', [TambahProdukController::class, 'tambahProduk']);
        Route::put('/produk/{id}', [UbahProdukController::class, 'ubahProduk']);
        Route::delete('/produk/{id}', [HapusProdukController::class, 'hapusProduk']);

        // Admin: Manajemen Pesanan
        Route::get('/pesanan', [LihatSemuaPesananController::class, 'ambilSemuaPesanan']);
        Route::put('/pesanan/{id_pesanan}/status', [UbahPesananController::class, 'perbaruiStatusPesanan']);

        // Admin: Manajemen Komplain
        Route::get('/komplain', [LihatKomplainController::class, 'ambilDaftarKomplain']);
        Route::get('/komplain/{id_komplain}', [LihatKomplainController::class, 'ambilDetailKomplain']);
        Route::put('/komplain/{id_komplain}/status', [UbahKomplainController::class, 'perbaruiStatusKomplain']);

        // Admin: Manajemen Berita
        Route::post('/berita', [TambahBeritaController::class, 'tambahBerita']);
        Route::put('/berita/{id}', [UbahBeritaController::class, 'ubahBerita']);
        Route::delete('/berita/{id}', [HapusBeritaController::class, 'hapusBerita']);
    });
});