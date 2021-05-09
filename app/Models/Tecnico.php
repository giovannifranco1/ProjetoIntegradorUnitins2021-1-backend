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

    public function getId($nome){
        $_pessoa = Pessoa::where('nome', $nome)->fisrt();
        $_tecnico = Tecnico::where('id_pessoa', $_pessoa->id)->fisrt();

        return $_tecnico->id;
    }
}
