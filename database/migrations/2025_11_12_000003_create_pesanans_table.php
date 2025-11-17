<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePesanansTable extends Migration
{
    public function up()
    {
        Schema::create('pesanans', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->foreignId('id_pembeli')->constrained('users', 'id_pengguna')->onDelete('cascade');
            $table->text('alamat_pengiriman');
            $table->string('status_pesanan', 50)->default('Menunggu Pembayaran');
            $table->timestamps();
        });

        Schema::create('detail_pesanans', function (Blueprint $table) {
            $table->id('id_detail_pesanan');
            $table->foreignId('id_pesanan')->constrained('pesanans', 'id_pesanan')->onDelete('cascade');
            $table->foreignId('id_produk')->constrained('produks', 'id_produk');
            $table->integer('kuantitas_produk');
            $table->decimal('harga_produk_tersimpan', 10, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_pesanans');
        Schema::dropIfExists('pesanans');
    }
}