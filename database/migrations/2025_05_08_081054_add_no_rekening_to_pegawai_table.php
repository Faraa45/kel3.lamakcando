<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->string('no_rekening')->nullable()->after('no_telepon');
        });
    }

    public function down()
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('no_rekening');
        });
    }
};
