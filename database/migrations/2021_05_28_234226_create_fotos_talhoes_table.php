<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFotosTalhoesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('fotos_talhao', function (Blueprint $table) {
      $table->id();
      $table->string('nome', 500);
      $table->string('imagem', 500);
      $table->unsignedBigInteger('id_talhao');
      $table->foreign('id_talhao')->references('id')->on('talhao')->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('fotos_talhao');
  }
}
