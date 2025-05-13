<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->string('no_absensi');
            $table->string('status'); // Hadir, Izin, Sakit, dll.
            $table->dateTime('tgl');
            $table->text('keterangan')->nullable(); // Kolom baru untuk keterangan tambahan
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('absensi');
    }
};
