<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('penggajian', function (Blueprint $table) {
            // Hapus foreign key constraint dari pegawai_id
            $table->dropForeign(['pegawai_id']);
        });
    }

    public function down(): void
    {
        Schema::table('penggajian', function (Blueprint $table) {
            // Tambahkan kembali jika dibutuhkan
            $table->foreign('pegawai_id')->references('id')->on('pegawai')->onDelete('cascade');
        });
    }
};
