<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeritasTable extends Migration
{
    public function up()
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id('id_berita');
            $table->string('judul_berita', 200);
            $table->longText('isi_berita');
            $table->string('gambar_berita', 255)->nullable();
            $table->dateTime('tanggal_publikasi');
            $table->enum('status_publikasi_berita', ['Draft', 'Terbit', 'Arsip'])->default('Draft');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('beritas');
    }
}