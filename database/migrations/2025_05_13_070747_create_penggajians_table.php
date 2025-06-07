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
        Schema::create('penggajian', function (Blueprint $table) {
    
    $table->id();
    $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
    $table->integer('jumlah_hadir')->default(0);    
    $table->integer('gaji_per_hari')->default(0);
    $table->integer('total_gaji')->default(0);
    $table->date('periode_awal');
    $table->date('periode_akhir');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penggajian');
    }
};
