<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Tecnico extends Model
{
    use HasRoles;

    protected $fillable = [
        'numero_registro',
        'senha', 'id_pessoa',
        'id_grupo'
    ];

    protected $table = 'tecnico';
}
