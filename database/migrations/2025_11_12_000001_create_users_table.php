<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id('id_pengguna'); // Sesuai PDF [cite: 3559]
            $table->string('nama', 100); // Sesuai PDF [cite: 3559]
            $table->string('email', 100)->unique(); // Sesuai PDF [cite: 3559]
            $table->string('password', 255); // Sesuai PDF [cite: 3559]
            $table->string('nomor_telepon', 15); // Sesuai PDF [cite: 3559]
            $table->text('alamat'); // Sesuai PDF [cite: 3559]
            $table->string('foto_profil', 255)->nullable(); // Sesuai PDF [cite: 3559]
            $table->enum('role', ['pembeli', 'admin'])->default('pembeli');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}