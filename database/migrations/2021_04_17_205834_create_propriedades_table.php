<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropriedadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('propriedade', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('localidade');
            $table->double('tamanho_area');
            $table->string('matricula' , 100);
            $table->unsignedBigInteger('id_cooperado');
            $table->unsignedBigInteger('id_tecnico');
            $table->foreign('id_cooperado')->references('id')->on('cooperado')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_tecnico')->references('id')->on('tecnico')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('propriedade');
    }
}
