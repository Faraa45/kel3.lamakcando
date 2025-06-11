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

        Schema::create('pengirimanemailgaji', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penggajian_id');
            $table->foreign('penggajian_id')->references('id')->on('penggajian')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->dateTime('tgl_pengiriman_pesan')->nullable();
            $table->timestamps();
        });

Schema::create('pengirimanemailgaji', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('penggajian_id'); // tambahkan ini
    $table->foreign('penggajian_id')->references('id')->on('penggajian')->onDelete('cascade');
    $table->string('status')->nullable();
    $table->dateTime('tgl_pengiriman_pesan')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengirimanemailgaji');
    }
};