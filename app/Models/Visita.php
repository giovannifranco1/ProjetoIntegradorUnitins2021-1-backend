<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
  protected $fillable = [
    'horario_estimado_visita',
    'id_propriedade',
    'motivo_visita',
    'dia_visita',
    'observacao',
    'id_tecnico',
    'status',
  ];
  protected $table = "visita";
}
