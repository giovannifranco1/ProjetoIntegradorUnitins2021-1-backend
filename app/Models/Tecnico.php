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

    public function pessoa(){
        return $this->hasOne(Pessoa::class , 'id' , 'id_pessoa');
    }

    public static function findId($nome){
        $_pessoa = Pessoa::where('nome', $nome)->first();
        dd($_tecnico = Tecnico::where('id_pessoa', $_pessoa->id)->first());

        return $_tecnico->id;
    }
}
