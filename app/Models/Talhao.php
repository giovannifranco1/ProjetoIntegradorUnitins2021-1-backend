<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Talhao extends Model
{
  protected $table = 'talhao';
  protected $fillable = [
    'nome',
    'cultura',
    'id_visita',
    'relatorio',
  ];
}
