<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTalhoesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('talhao', function (Blueprint $table) {
      $table->id();
      $table->string('cultura');
      $table->string('relatorio');
      $table->string('id_visita');
      $table->unsignedBigInteger('id_visita');
      $table->foreign('id_visita')->references('id')->on('visita')->onUpdate('cascade')->onDelete('cascade');
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
    Schema::dropIfExists('talhao');
  }
}
