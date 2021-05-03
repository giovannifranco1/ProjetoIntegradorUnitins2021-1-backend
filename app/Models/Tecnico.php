<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tecnico extends Model
{
    protected $fillable = ['numero_registro', 'senha', 'id_pessoa', 'id_grupo'];
    protected $table = 'tecnico';
}
