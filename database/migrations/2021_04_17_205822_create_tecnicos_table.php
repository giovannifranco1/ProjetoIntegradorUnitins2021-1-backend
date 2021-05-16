<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTecnicosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tecnico', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 14);
            $table->string('sobrenome');
            $table->string('numero_registro');
            $table->unsignedBigInteger('id_telefone');
            $table->unsignedBigInteger('id_user');
            $table->foreign('id_telefone')->references('id')->on('telefone')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('tecnico');
    }
}
