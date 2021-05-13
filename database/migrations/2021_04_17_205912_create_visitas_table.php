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
            $table->time('horario_estimado_visita');
            $table->date('dia_visita');
            $table->unsignedBigInteger('id_tecnico');
            $table->unsignedBigInteger('id_propriedade');
            $table->string('motivo_visita');
            $table->string('status')->default('aberto');
            $table->longText('observacao')->nullable();
            $table->foreign('id_tecnico')->references('id')->on('tecnico')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_propriedade')->references('id')->on('propriedade')->onDelete('cascade')->onUpdate('cascade');
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
