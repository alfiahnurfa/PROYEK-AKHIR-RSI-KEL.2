<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProduksTable extends Migration
{
    public function up()
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk', 100);
            $table->string('kategori_produk', 50);
            $table->text('deskripsi_produk')->nullable();
            $table->float('berat_produk');
            $table->decimal('harga_produk', 10, 2);
            $table->integer('stok_produk');
            $table->string('foto_produk', 255);
            $table->enum('status_produk', ['Aktif', 'Arsip'])->default('Aktif');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('produks');
    }
}