<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->string('id_pegawai');
            $table->string('nama_pegawai');
            $table->string('role');
            $table->string('no_telepon');
            $table->int('no_rekening');
            $table->string('email');
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //jika parent di hapus, maka anak akan ikut terhapus

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
