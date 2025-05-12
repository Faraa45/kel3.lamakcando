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
        Schema::create('bahan_baku', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bahan')->unique(); // kode unik bahan baku
            $table->string('nama_bahan');
            $table->string('satuan'); // contoh: kg, liter, pcs
            $table->integer('stok'); // stok bahan baku
            $table->decimal('harga_satuan', 15, 2); // harga per satuan
            $table->foreignId('vendor_id')->constrained('vendor')->cascadeOnDelete(); // relasi ke tabel vendor
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahan_baku');
    }
};
