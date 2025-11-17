<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKomplainsTable extends Migration
{
    public function up()
    {
        Schema::create('komplains', function (Blueprint $table) {
            $table->id('id_komplain');
            $table->foreignId('id_pesanan')->constrained('pesanans', 'id_pesanan');
            $table->foreignId('id_pembeli')->constrained('users', 'id_pengguna');
            $table->string('judul_komplain', 150);
            $table->text('deskripsi_komplain');
            $table->string('bukti_komplain', 255)->nullable();
            $table->dateTime('tanggal_pengajuan');
            $table->string('status_komplain', 50)->default('Baru');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('komplains');
    }
}