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
    Schema::table('penggajian', function (Blueprint $table) {
        $table->foreignId('pegawai_id')->after('jumlah_hadir')->constrained('pegawai')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('penggajian', function (Blueprint $table) {
        $table->dropForeign(['pegawai_id']);
        $table->dropColumn('pegawai_id');
    });
}

};
