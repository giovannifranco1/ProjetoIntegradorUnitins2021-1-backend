<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visita extends Model
{
    protected $fillable =['horario_estimado_visita','dia_visita',
    'id_tecnico','id_propriedade','motivo_visita',
    'status','observacao'];
    protected $table = "visita";


}
