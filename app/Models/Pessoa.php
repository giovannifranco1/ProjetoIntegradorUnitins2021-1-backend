<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    protected $fillable = ['nome', 'sobrenome', 'email', 'cpf', 'id_telefone'];
    protected $table = 'pessoa';

    public function telefone(){
        return $this->belongsTo(Telefone::class, 'id_telefone');
    }
    public function cooperado(){
        return $this->hasOne(Pessoa::class);
    }
}
