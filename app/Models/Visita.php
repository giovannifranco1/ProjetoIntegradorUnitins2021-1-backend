<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
  use HasFactory;
  protected $table = "visita";
  protected $fillable = [
    'horario_estimado_visita',
    'dia_visita',
    'id_tecnico',
    'id_propriedade',
    'status',
    'observacao',
    'motivo_visita',
  ];
}
