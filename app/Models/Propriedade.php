<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Propriedade extends Model
{
  protected $fillable  = ['nome', 'localidade', 'tamanho_area', 'matricula', 'id_cooperado','id_tecnico'];
  protected $table = 'propriedade';
  use HasFactory;
}
