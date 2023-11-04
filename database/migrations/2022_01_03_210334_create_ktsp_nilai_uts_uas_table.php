<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKtspNilaiUtsUasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ktsp_nilai_uts_uas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pembelajaran_id')->unsigned();
            $table->unsignedBigInteger('anggota_kelas_id')->unsigned();
            $table->integer('nilai_uts');
                $table->integer('nilai_uas');
            $table->timestamps();

            $table->foreign('pembelajaran_id')->references('id')->on('pembelajaran');
            $table->foreign('anggota_kelas_id')->references('id')->on('anggota_kelas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ktsp_nilai_uts_uas');
    }
}
