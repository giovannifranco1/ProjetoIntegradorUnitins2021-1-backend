<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVisitasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('visita', function (Blueprint $table) {
            $table->id();
            $table->dateTime('horario_estimado_visita');
            $table->date('dia_visita');
            $table->unsignedBigInteger('id_tecnico');
            $table->unsignedBigInteger('id_propriedade');
            $table->unsignedBigInteger('id_motivo_visita');
            $table->foreign('id_tecnico')->references('id')->on('tecnico')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_propriedade')->references('id')->on('propriedade')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_motivo_visita')->references('id')->on('motivo_visita')->onDelete('cascade')->onUpdate('cascade');
            $table->string('status')->default('aberto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('visita');
    }
}
