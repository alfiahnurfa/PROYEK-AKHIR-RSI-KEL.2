<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePembayaransTable extends Migration
{
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pesanan')->unique()->constrained('pesanans', 'id_pesanan')->onDelete('cascade');
            $table->string('status_pembayaran', 50)->default('Menunggu');
            $table->string('metode_pembayaran', 50);
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
}