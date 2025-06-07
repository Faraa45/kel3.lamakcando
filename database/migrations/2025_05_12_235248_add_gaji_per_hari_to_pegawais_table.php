<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('pegawai', function (Blueprint $table) {
        $table->integer('gaji_per_hari')->default(0)->after('nama_pegawai'); // sesuaikan kolom 'nama' jika perlu
    });
}

public function down()
{
    Schema::table('pegawai', function (Blueprint $table) {
        $table->dropColumn('gaji_per_hari');
    });
}
};
