<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $fillable = ['nome', 'email', 'cpf', 'id_telefone', 'status'];
    protected $table = 'pessoa';

    public function telefone(){
        return $this->hasOne(Pessoa::class, 'id_telefone' , 'id');
    }
}
