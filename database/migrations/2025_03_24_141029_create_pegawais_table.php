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
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nama_pegawai');
            $table->string('role');
            $table->string('no_telepon');
            $table->string('no_rekening');
            $table->string('email');
            $table->string('bank');
            $table->timestamps();

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
