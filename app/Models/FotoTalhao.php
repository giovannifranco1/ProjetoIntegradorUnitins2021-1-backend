<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FotoTalhao extends Model
{
  protected $table = 'fotos_talhao';
  protected $fillable = [
    'nome',
    'imagem',
    'id_talhao',
  ];
}
